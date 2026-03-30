<?php

namespace App\Services;

use DOMDocument;
use SimpleXMLElement;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class XmlGeneratorDinamicoService
{
    protected SimpleXMLElement $xml;
    protected array $namespaces = [];
    protected array $mapaDeNos  = [];

    const PREFIXO_LOOP = '[loop]';

    // =========================================================================
    // 1. CARREGAMENTO DO XML BASE
    // =========================================================================

    public function carregarBase(string $caminhoXml): void
    {
        try {
            if (!file_exists($caminhoXml)) {
                throw new \RuntimeException("Arquivo XML base não encontrado: {$caminhoXml}");
            }

            $conteudo = file_get_contents($caminhoXml);

            if ($conteudo === false) {
                throw new \RuntimeException("Não foi possível ler o arquivo XML base: {$caminhoXml}");
            }

            $encoding = 'UTF-8';
            if (preg_match('/encoding=["\']([^"\']+)["\']/', $conteudo, $encontrado)) {
                $encoding = strtoupper($encontrado[1]);
            }

            if ($encoding !== 'UTF-8') {
                $conteudo = mb_convert_encoding($conteudo, 'UTF-8', $encoding);
                $conteudo = preg_replace('/encoding=["\'][^"\']+["\']/', 'encoding="UTF-8"', $conteudo);
            }

            libxml_use_internal_errors(true);
            libxml_clear_errors();

            $this->xml = simplexml_load_string($conteudo, 'SimpleXMLElement', LIBXML_NOCDATA);

            if (!$this->xml) {
                $erros = array_map(
                    fn($e) => "Linha {$e->line} col {$e->column}: " . trim($e->message),
                    libxml_get_errors()
                );
                libxml_clear_errors();
                throw new \RuntimeException("Falha ao ler o XML base. " . implode(' | ', $erros));
            }

            libxml_clear_errors();

            $this->namespaces = $this->xml->getNamespaces(true);
            $this->mapaDeNos  = [];
            $this->varrerNosTerminais($this->xml);
        } catch (\Throwable $e) {
            throw new \RuntimeException('Erro em carregarBase: ' . $e->getMessage(), 0, $e);
        }
    }

    // =========================================================================
    // 2. GERAÇÃO DO XML
    // =========================================================================

    public function gerar(array $dados, string $caminhoSaida): string
    {
        try {
            if (empty($dados)) {
                throw new \InvalidArgumentException("Nenhum dado encontrado na planilha.");
            }

            $primeiraLinha   = $dados[0];
            $camposFixos     = $this->obterCamposFixos($primeiraLinha);
            $camposRepetidos = $this->obterCamposRepetidos($primeiraLinha);

            foreach ($camposFixos as $cabecalho) {
                $valor = $primeiraLinha[$cabecalho] ?? '';
                if (isset($this->mapaDeNos[$cabecalho])) {
                    $this->mapaDeNos[$cabecalho][0] = $this->formatarValor($cabecalho, $valor);
                }
            }

            if (!empty($camposRepetidos)) {
                $this->preencherCamposRepetidos($dados, $camposRepetidos);
            }

            $dom = new DOMDocument('1.0', 'UTF-8');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput       = true;
            $dom->loadXML($this->xml->asXML());
            $dom->save($caminhoSaida);

            return $dom->saveXML();
        } catch (\Throwable $e) {
            throw new \RuntimeException('Erro em gerar: ' . $e->getMessage(), 0, $e);
        }
    }

    // =========================================================================
    // 3. PREENCHIMENTO DOS CAMPOS REPETIDOS (LOOP)
    // =========================================================================

    protected function preencherCamposRepetidos(array $dados, array $camposRepetidos): void
    {
        try {
            $nodoDOMConteiner = null;
            $nomeFilho        = null;
            $namespaceFilho   = null;

            foreach ($camposRepetidos as $campo) {
                $nomeDaTag = $this->removerPrefixoLoop($campo);

                if (!isset($this->mapaDeNos[$nomeDaTag])) {
                    continue;
                }

                $noDaFolha   = dom_import_simplexml($this->mapaDeNos[$nomeDaTag]);
                $noItem      = $noDaFolha->parentNode;
                $noConteiner = $noItem?->parentNode;

                if ($noConteiner) {
                    $nodoDOMConteiner = $noConteiner;
                    $nomeFilho        = $noItem->localName;
                    $namespaceFilho   = $noItem->namespaceURI ?: null;
                    break;
                }
            }

            if (!$nodoDOMConteiner || !$nomeFilho) {
                return;
            }

            while ($nodoDOMConteiner->firstChild) {
                $nodoDOMConteiner->removeChild($nodoDOMConteiner->firstChild);
            }

            $noConteinerSimpleXML = simplexml_import_dom($nodoDOMConteiner);
            $prefixoFilho         = $this->buscarPrefixoNamespace($namespaceFilho);
            $nomeQualificado      = $prefixoFilho ? "{$prefixoFilho}:{$nomeFilho}" : $nomeFilho;

            foreach ($dados as $linha) {
                $temDado = false;
                foreach ($camposRepetidos as $campo) {
                    if (!empty($linha[$campo])) {
                        $temDado = true;
                        break;
                    }
                }
                if (!$temDado) {
                    continue;
                }

                $novoBloco = $noConteinerSimpleXML->addChild($nomeQualificado, null, $namespaceFilho);

                foreach ($camposRepetidos as $campo) {
                    $nomeDaTag      = $this->removerPrefixoLoop($campo);
                    $valor          = $linha[$campo] ?? '';
                    $namespaceDaTag = $this->buscarNamespaceDaTag($nomeDaTag);
                    $prefixoDaTag   = $this->buscarPrefixoNamespace($namespaceDaTag);
                    $tagQualificada = $prefixoDaTag ? "{$prefixoDaTag}:{$nomeDaTag}" : $nomeDaTag;

                    $novoBloco->addChild(
                        $tagQualificada,
                        htmlspecialchars($this->formatarValor($nomeDaTag, (string)$valor), ENT_XML1, 'UTF-8'),
                        $namespaceDaTag ?: null
                    );
                }
            }
        } catch (\Throwable $e) {
            throw new \RuntimeException('Erro em preencherCamposRepetidos: ' . $e->getMessage(), 0, $e);
        }
    }

    // =========================================================================
    // 4. VARREDURA DOS NÓS TERMINAIS DO XML
    // =========================================================================

    protected function varrerNosTerminais(SimpleXMLElement $no): void
    {
        try {
            $todosNamespaces = array_merge(['' => null], $this->namespaces);
            $temFilhos       = false;

            foreach ($todosNamespaces as $ns) {
                $filhos = $ns ? $no->children($ns) : $no->children();

                foreach ($filhos as $filho) {
                    $temFilhos = true;
                    $this->varrerNosTerminais($filho);
                }
            }

            if (!$temFilhos) {
                $nomeDaTag = $no->getName();
                if (!isset($this->mapaDeNos[$nomeDaTag])) {
                    $this->mapaDeNos[$nomeDaTag] = $no;
                }
            }
        } catch (\Throwable $e) {
            throw new \RuntimeException('Erro em varrerNosTerminais: ' . $e->getMessage(), 0, $e);
        }
    }

    // =========================================================================
    // 5. FORMATAÇÃO DE VALORES
    // =========================================================================

    protected function formatarValor(string $nomeDaTag, $valor): string
    {
        try {
            if ($valor === null || $valor === '') {
                return '';
            }

            $nomeMinusculo = strtolower($nomeDaTag);

            if (str_contains($nomeMinusculo, 'data')) {
                return $this->formatarData($valor);
            }

            if (str_contains($nomeMinusculo, 'cpf')) {
                return preg_replace('/\D/', '', (string)$valor);
            }

            return (string)$valor;
        } catch (\Throwable $e) {
            throw new \RuntimeException("Erro em formatarValor (tag: {$nomeDaTag}): " . $e->getMessage(), 0, $e);
        }
    }

    protected function formatarData($valor): string
    {
        try {
            if (empty($valor)) {
                return '';
            }

            if (is_numeric($valor)) {
                return Date::excelToDateTimeObject((float)$valor)->format('Y-m-d');
            }

            $data = \DateTime::createFromFormat('d/m/Y', (string)$valor);
            if ($data) {
                return $data->format('Y-m-d');
            }

            $data = \DateTime::createFromFormat('Y-m-d', (string)$valor);
            if ($data) {
                return $data->format('Y-m-d');
            }

            return (string)$valor;
        } catch (\Throwable $e) {
            throw new \RuntimeException('Erro em formatarData: ' . $e->getMessage(), 0, $e);
        }
    }

    // =========================================================================
    // 6. AUXILIARES
    // =========================================================================

    protected function obterCamposFixos(array $linha): array
    {
        return array_values(array_filter(
            array_keys($linha),
            fn($chave) => !str_starts_with((string)$chave, self::PREFIXO_LOOP)
        ));
    }

    protected function obterCamposRepetidos(array $linha): array
    {
        return array_values(array_filter(
            array_keys($linha),
            fn($chave) => str_starts_with((string)$chave, self::PREFIXO_LOOP)
        ));
    }

    protected function removerPrefixoLoop(string $campo): string
    {
        return substr($campo, strlen(self::PREFIXO_LOOP));
    }

    protected function buscarNamespaceDaTag(string $nomeDaTag): ?string
    {
        if (!isset($this->mapaDeNos[$nomeDaTag])) {
            return null;
        }

        $no = dom_import_simplexml($this->mapaDeNos[$nomeDaTag]);
        return $no->namespaceURI ?: null;
    }

    protected function buscarPrefixoNamespace(?string $namespaceUri): ?string
    {
        if (!$namespaceUri) {
            return null;
        }

        foreach ($this->namespaces as $prefixo => $uri) {
            if ($uri === $namespaceUri) {
                return $prefixo;
            }
        }

        return null;
    }

    // =========================================================================
    // 7. UTILITÁRIOS (depuração / validação)
    // =========================================================================

    public function listarTagsDisponiveis(): array
    {
        return array_keys($this->mapaDeNos);
    }

    public function validarCabecalhos(array $cabecalhos): array
    {
        $naoEncontrados = [];

        foreach ($cabecalhos as $cabecalho) {
            $nomeDaTag = str_starts_with($cabecalho, self::PREFIXO_LOOP)
                ? $this->removerPrefixoLoop($cabecalho)
                : $cabecalho;

            if (!isset($this->mapaDeNos[$nomeDaTag])) {
                $naoEncontrados[] = $cabecalho;
            }
        }

        return $naoEncontrados;
    }
}
