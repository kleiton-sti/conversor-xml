<?php

namespace App\Services;

use DOMDocument;
use SimpleXMLElement;

class XmlGeneratorService {

    protected SimpleXMLElement $xml;
    protected array $namespaces;

    /**
     * Carrega o XML base (modelo/estrutura) a partir de um caminho de arquivo
     */
    public function loadBase(string $xmlFilePath): void {
        if (!file_exists($xmlFilePath)) {
            throw new \RuntimeException("Arquivo XML base não encontrado: {$xmlFilePath}");
        }

        // Usa LIBXML_NOWARNING para tolerar caracteres inválidos no modelo
        $content = file_get_contents($xmlFilePath);

        // Detecta e normaliza encoding declarado no XML
        $encoding = 'UTF-8';
        if (preg_match('/encoding=["\']([^"\']+)["\']/', $content, $m)) {
            $encoding = strtoupper($m[1]);
        }

        // Converte para UTF-8 se necessário
        if ($encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
            // Atualiza declaração para UTF-8
            $content = preg_replace('/encoding=["\'][^"\']+["\']/', 'encoding="UTF-8"', $content);
        }

        $this->xml = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);

        if (!$this->xml) {
            throw new \RuntimeException("Falha ao parsear o XML base. Verifique se o arquivo é um XML válido.");
        }

        $this->namespaces = $this->xml->getNamespaces(true);
    }

    /**
     * Gera o XML preenchido com os dados da planilha e salva no destino
     * Retorna o conteúdo XML como string
     */
    public function generate(array $data, string $outputPath): string {
        if (empty($data)) {
            throw new \InvalidArgumentException("Nenhum dado encontrado na planilha.");
        }

        $ns = $this->namespaces;

        // ── Descritor ──────────────────────────────────────────────────────────
        $descritor = $this->xml->children($ns['lcl'])->Descritor ?? null;
        if ($descritor) {
            $this->setNode($descritor, $ns['gen'], 'AnoExercicio',    $data[0]['A'] ?? '');
            $this->setNode($descritor, $ns['gen'], 'TipoDocumento',   $data[0]['B'] ?? '');
            $this->setNode($descritor, $ns['gen'], 'Entidade',        $data[0]['C'] ?? '');
            $this->setNode($descritor, $ns['gen'], 'Municipio',       $data[0]['D'] ?? '');
            $this->setNode($descritor, $ns['gen'], 'DataCriacaoXML',  date('Y-m-d'));
        }

        // ── TipoProcessoSelecao ────────────────────────────────────────────────
        $tipoPS = $this->xml->children($ns['lcl'])->TipoProcessoSelecao ?? null;
        if ($tipoPS !== null) {
            $tipoPS[0] = $data[0]['F'] ?? '';
        }

        // ── IdentificacaoProcessoSelecao ───────────────────────────────────────
        $idPS = $this->xml->children($ns['lcl'])->IdentificacaoProcessoSelecao ?? null;
        if ($idPS) {
            $this->setNode($idPS, $ns['ap'], 'numeroProcessoSelecao', $data[0]['G'] ?? '');
            $this->setNode($idPS, $ns['ap'], 'anoProcessoSelecao',    $data[0]['H'] ?? '');
        }

        // ── Classificacao / DadosClassificacao ────────────────────────────────
        $classificacao    = $this->xml->children($ns['lcl'])->Classificacao ?? null;
        $dadosClass       = $classificacao->children($ns['lcl'])->DadosClassificacao ?? null;
        $cargoFuncaoEdital = $dadosClass->children($ns['lcl'])->CargoFuncaoEdital ?? null;

        if ($cargoFuncaoEdital) {
            $entPrevista = $cargoFuncaoEdital->children($ns['ap'])->EntidadePrevista ?? null;
            if ($entPrevista) {
                $this->setNode($entPrevista, $ns['ap'], 'CodigoEntidadePrevista',        $data[0]['I'] ?? '');
                $this->setNode($entPrevista, $ns['ap'], 'CodigoMunicipioEntidadePrevista', $data[0]['J'] ?? '');
            }
            $this->setNode($cargoFuncaoEdital, $ns['ap'], 'codigoFuncao', $data[0]['K'] ?? '');
        }

        if ($dadosClass) {
            $this->setNode($dadosClass, $ns['lcl'], 'dataPublicacaoListaClassificacao', $this->formatDate($data[0]['L'] ?? ''));
            $this->setNode($dadosClass, $ns['lcl'], 'dataAtoHomologacaoConcurso',       $this->formatDate($data[0]['M'] ?? ''));
            $this->setNode($dadosClass, $ns['lcl'], 'dataValidadeInicial',              $this->formatDate($data[0]['N'] ?? ''));
            $this->setNode($dadosClass, $ns['lcl'], 'dataPublicacaoHomologacao',        $this->formatDate($data[0]['O'] ?? ''));

            $cpfResp = $dadosClass->children($ns['lcl'])->cpfResponsavelHomologacao ?? null;
            if ($cpfResp) {
                $this->setNode($cpfResp, $ns['gen'], 'Numero', $data[0]['Q'] ?? '');
            }

            $this->setNode($dadosClass, $ns['lcl'], 'codigoCargoResponsavelHomologacao', $data[0]['R'] ?? '');
        }

        // ── Classificados (loop em todas as linhas) ────────────────────────────
        $classificados = $classificacao->children($ns['lcl'])->Classificados ?? null;
        if ($classificados) {
            foreach ($data as $row) {
                // CPF (col T), Nome (col U), Ordem (col V)
                $cpfVal   = $row['T'] ?? '';
                $nomeVal  = $row['U'] ?? '';
                $ordemVal = $row['V'] ?? '';

                if (empty($cpfVal) && empty($nomeVal)) {
                    continue; // Pula linhas sem dados essenciais
                }

                $novo = $classificados->addChild('lcl:Classificado', null, $ns['lcl']);

                $cpfNode = $novo->addChild('lcl:cpfClassificado', null, $ns['lcl']);
                // Adiciona atributo Tipo="02"
                $cpfDom = dom_import_simplexml($cpfNode);
                $cpfDom->setAttribute('Tipo', '02');
                $cpfNode->addChild('gen:Numero', $this->sanitizeCpf($cpfVal), $ns['gen']);

                $novo->addChild('lcl:nomeClassificado',   htmlspecialchars((string)$nomeVal,  ENT_XML1, 'UTF-8'), $ns['lcl']);
                $novo->addChild('lcl:ordemClassificacao', (string)$ordemVal, $ns['lcl']);
            }
        }

        // ── Formata e salva ────────────────────────────────────────────────────
        $xmlString = $this->xml->asXML();

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput       = true;
        $dom->loadXML($xmlString);
        $dom->save($outputPath);

        return $dom->saveXML();
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function setNode(SimpleXMLElement $parent, string $namespace, string $tag, $value): void {
        $node = $parent->children($namespace)->$tag ?? null;
        if ($node !== null) {
            $node[0] = (string)$value;
        }
    }

    /**
     * Formata datas. Aceita timestamps Excel (número) ou strings no formato d/m/Y ou Y-m-d
     */
    private function formatDate($value): string {
        if (empty($value)) return '';

        // Timestamp numérico do Excel
        if (is_numeric($value)) {
            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float)$value);
            return $date->format('Y-m-d');
        }

        // Tenta d/m/Y
        $dt = \DateTime::createFromFormat('d/m/Y', (string)$value);
        if ($dt) return $dt->format('Y-m-d');

        // Tenta Y-m-d
        $dt = \DateTime::createFromFormat('Y-m-d', (string)$value);
        if ($dt) return $dt->format('Y-m-d');

        return (string)$value;
    }

    private function sanitizeCpf($value): string {
        return preg_replace('/\D/', '', (string)$value);
    }
}
