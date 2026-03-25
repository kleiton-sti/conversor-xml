<?php

namespace App\Services;

use DOMDocument;
use SimpleXMLElement;

/**
 * XmlGeneratorDynamicService
 *
 * Versão genérica/dinâmica do gerador de XML.
 *
 * Diferente do XmlGeneratorService (que tem mapeamento fixo coluna→nó),
 * este service:
 *   1. Lê o XML base e mapeia todos os nós "folha" (que contêm texto) pelo nome da tag.
 *   2. Lê o cabeçalho da planilha (primeira linha) como chaves de mapeamento.
 *   3. Casa automaticamente: se o cabeçalho da coluna bater com o nome de um nó XML,
 *      o valor daquela coluna é inserido naquele nó.
 *   4. Suporta linhas repetidas (loop) para nós que precisam ser criados N vezes.
 *      Convenção: colunas de loop devem ter cabeçalho prefixado com "[loop]"
 *      Ex: "[loop]nomeClassificado", "[loop]ordemClassificacao"
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * COMO MONTAR A PLANILHA
 * ─────────────────────────────────────────────────────────────────────────────
 *
 *  • Linha 1 → cabeçalhos. O nome de cada coluna deve ser IGUAL ao nome da tag
 *    XML correspondente (case-sensitive).
 *
 *  • Colunas fixas (ex: AnoExercicio, Municipio…): só a linha 2 é lida.
 *
 *  • Colunas de loop (repetem por linha): prefixar com "[loop]"
 *    Ex: "[loop]nomeClassificado"  |  "[loop]cpfClassificado"  |  "[loop]ordemClassificacao"
 *
 *  • Linhas 2, 3, 4… → cada linha gera um nó filho repetido no XML.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * EXEMPLO DE USO NO CONTROLLER
 * ─────────────────────────────────────────────────────────────────────────────
 *
 *   // Lê a planilha COM cabeçalho (passar false para ignorarHeader)
 *   $data = (new LoadFileService($planilhaPath))->readerWithHeader();
 *
 *   $generator = new XmlGeneratorDynamicService();
 *   $generator->loadBase($xmlBaseFullPath);
 *   $xmlContent = $generator->generate($data, $outputPath);
 *
 *   // Opcional: ver quais tags o XML base possui
 *   $tags = $generator->getLeafTags();
 * ─────────────────────────────────────────────────────────────────────────────
 */
class GerarXmlDinamicoService
{
    protected SimpleXMLElement $xml;
    protected array $namespaces = [];

    /**
     * Mapa tagName (local), SimpleXMLElement
     * Gerado ao carregar o XML base; contém apenas nós folha.
     */
    protected array $mapaCabecalho = [];

    /** Prefixo que identifica colunas de loop na planilha */
    const LOOP_PREFIX = '[loop]';

    // =========================================================================
    // CARREGAMENTO DO XML BASE
    // =========================================================================

    /**
     * Carrega o XML modelo, normaliza encoding e constrói o mapa de nós folha.
     */
    public function CarregaXmlBase(string $caminhoXml): void
    {
        if (!file_exists($caminhoXml)) {
            throw new \RuntimeException("Arquivo XML base não encontrado: {$caminhoXml}");
        }

        $conteudo = file_get_contents($caminhoXml);

        // Detecta encoding declarado e converte para UTF-8 se necessário
        $encoding = 'UTF-8';
        if (preg_match('/encoding=["\']([^"\']+)["\']/',  $conteudo, $m)) {
            $encoding = strtoupper($m[1]);
        }
        if ($encoding !== 'UTF-8') {
             $conteudo = mb_convert_encoding( $conteudo, 'UTF-8', $encoding);
             $conteudo = preg_replace('/encoding=["\'][^"\']+["\']/', 'encoding="UTF-8"',  $conteudo);
        }

        $this->xml = simplexml_load_string( $conteudo, 'SimpleXMLElement', LIBXML_NOCDATA);

        if (!$this->xml) {
            throw new \RuntimeException("Falha ao parsear o XML base. Verifique se o arquivo é um XML válido.");
        }

        $this->namespaces = $this->xml->getNamespaces(true);

        // Constrói o mapa de nós folha (tagName → nó SimpleXML)
        $this->mapaCabecalho = [];
        $this->mapaFolha($this->xml);
    }

    // =========================================================================
    // GERAÇÃO DO XML
    // =========================================================================

    /**
     * Preenche o XML com os dados da planilha e salva no destino.
     * Retorna o XML gerado como string.
     *
     * @param array  $data       Array de arrays associativos. Chaves = cabeçalhos da planilha.
     *                           Colunas prefixadas com "[loop]" são tratadas como repetíveis.
     * @param string $outputPath Caminho completo do arquivo XML de saída.
     */
    public function generate(array $data, string $outputPath): string
    {
        if (empty($data)) {
            throw new \InvalidArgumentException("Nenhum dado encontrado na planilha.");
        }

        $firstRow  = $data[0];
        $fixedKeys = $this->getFixedKeys($firstRow);
        $loopKeys  = $this->getLoopKeys($firstRow);

        // ── 1. Campos fixos: preenche com dados da primeira linha ─────────────
        foreach ($fixedKeys as $header) {
            $value = $firstRow[$header] ?? '';
            if (isset($this->mapaCabecalho[$header])) {
                $this->mapaCabecalho[$header][0] = $this->resolveValue($header, $value);
            }
            // Se a tag não existe no XML base, ignora silenciosamente.
        }

        // ── 2. Campos de loop: cria um nó filho por linha ────────────────────
        if (!empty($loopKeys)) {
            $this->fillLoopNodes($data, $loopKeys);
        }

        // ── 3. Formata e salva ───────────────────────────────────────────────
        $xmlString = $this->xml->asXML();

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput       = true;
        $dom->loadXML($xmlString);
        $dom->save($outputPath);

        return $dom->saveXML();
    }

    // =========================================================================
    // PREENCHIMENTO DE NÓS DE LOOP
    // =========================================================================

    /**
     * Para cada linha da planilha, cria um novo nó filho dentro do contêiner
     * detectado automaticamente a partir do XML base.
     *
     * Estratégia de detecção do contêiner:
     *  - Localiza o primeiro campo de loop que existe no mapaCabecalho.
     *  - Sobe dois níveis (folha → item-pai → contêiner) para encontrar o nó
     *    que contém as repetições.
     *  - Remove o item-modelo do XML base e recria um por linha de dados.
     */
    protected function fillLoopNodes(array $data, array $loopKeys): void
    {
        // ── Detecta o nó contêiner e o nome do nó filho repetível ────────────
        $containerDom  = null;
        $childLocalName = null;
        $childNsUri     = null;

        foreach ($loopKeys as $key) {
            $tagName = $this->stripLoopPrefix($key);
            if (!isset($this->mapaCabecalho[$tagName])) continue;

            $leafDom       = dom_import_simplexml($this->mapaCabecalho[$tagName]);
            $itemDom       = $leafDom->parentNode;          // ex: <lcl:Classificado>
            $containerDomNode = $itemDom?->parentNode;      // ex: <lcl:Classificados>

            if ($containerDomNode) {
                $containerDom   = $containerDomNode;
                $childLocalName = $itemDom->localName;
                $childNsUri     = $itemDom->namespaceURI ?: null;
                break;
            }
        }

        if (!$containerDom || !$childLocalName) {
            // Nenhum campo de loop bateu com o XML — ignora
            return;
        }

        // ── Remove todos os filhos existentes do contêiner (modelos) ─────────
        while ($containerDom->firstChild) {
            $containerDom->removeChild($containerDom->firstChild);
        }

        // Reconverte para SimpleXML para usar addChild
        $containerNode = simplexml_import_dom($containerDom);
        $childNsPrefix = $this->findNamespacePrefix($childNsUri);
        $qualifiedChild = $childNsPrefix ? "{$childNsPrefix}:{$childLocalName}" : $childLocalName;

        // ── Cria um filho por linha ───────────────────────────────────────────
        foreach ($data as $row) {
            // Pula linha completamente vazia nos campos de loop
            $hasData = false;
            foreach ($loopKeys as $key) {
                if (isset($row[$key]) && $row[$key] !== null && $row[$key] !== '') {
                    $hasData = true;
                    break;
                }
            }
            if (!$hasData) continue;

            // Cria o nó filho (ex: <lcl:Classificado>)
            $novoFilho = $containerNode->addChild($qualifiedChild, null, $childNsUri);

            // Adiciona cada campo de loop como sub-nó
            foreach ($loopKeys as $key) {
                $tagName = $this->stripLoopPrefix($key);
                $value   = $row[$key] ?? '';

                $leafNsUri    = $this->findLeafNamespaceUri($tagName);
                $leafNsPrefix = $this->findNamespacePrefix($leafNsUri);
                $qualifiedTag = $leafNsPrefix ? "{$leafNsPrefix}:{$tagName}" : $tagName;

                $novoFilho->addChild(
                    $qualifiedTag,
                    htmlspecialchars($this->resolveValue($tagName, (string)$value), ENT_XML1, 'UTF-8'),
                    $leafNsUri ?: null
                );
            }
        }
    }

    // =========================================================================
    // MAPEAMENTO DE NÓS FOLHA
    // =========================================================================

    /**
     * Percorre recursivamente o XML e registra em $this->mapaCabecalho
     * todos os nós que não possuem filhos (nós "folha" / terminais).
     */
    protected function mapaFolha(SimpleXMLElement $node): void
    {
        $allNs       = array_merge(['' => null], $this->namespaces);
        $hasChildren = false;

        foreach ($allNs as $ns) {
            $children = $ns ? $node->children($ns) : $node->children();
            foreach ($children as $child) {
                $hasChildren = true;
                $this->mapaFolha($child);
            }
        }

        if (!$hasChildren) {
            $tagName = $node->getName();
            // Mantém o primeiro nó encontrado com aquele nome
            if (!isset($this->mapaCabecalho[$tagName])) {
                $this->mapaCabecalho[$tagName] = $node;
            }
        }
    }

    // =========================================================================
    // RESOLUÇÃO DE VALORES ESPECIAIS
    // =========================================================================

    /**
     * Aplica transformações ao valor conforme o nome da tag:
     *  - Tags com "data" ou "Data" no nome → formata como Y-m-d
     *  - Tags com "cpf" ou "Cpf" ou "CPF" no nome → remove não-dígitos
     *  - Demais → retorna como string sem alteração
     */
    protected function resolveValue(string $tagName, $value): string
    {
        if ($value === null || $value === '') return '';

        $lower = strtolower($tagName);

        if (str_contains($lower, 'data')) {
            return $this->formatDate($value);
        }

        if (str_contains($lower, 'cpf')) {
            return preg_replace('/\D/', '', (string)$value);
        }

        return (string)$value;
    }

    /**
     * Formata datas. Aceita:
     *  - Número  → timestamp serial do Excel
     *  - String  → d/m/Y  ou  Y-m-d
     */
    protected function formatDate($value): string
    {
        if (empty($value)) return '';

        if (is_numeric($value)) {
            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float)$value);
            return $date->format('Y-m-d');
        }

        $dt = \DateTime::createFromFormat('d/m/Y', (string)$value);
        if ($dt) return $dt->format('Y-m-d');

        $dt = \DateTime::createFromFormat('Y-m-d', (string)$value);
        if ($dt) return $dt->format('Y-m-d');

        return (string)$value;
    }

    // =========================================================================
    // HELPERS DE NAMESPACE E ESTRUTURA
    // =========================================================================

    /** Retorna chaves do array que NÃO são de loop. */
    protected function getFixedKeys(array $row): array
    {
        return array_values(array_filter(
            array_keys($row),
            fn($k) => !str_starts_with((string)$k, self::LOOP_PREFIX)
        ));
    }

    /** Retorna chaves do array que SÃO de loop (prefixadas com LOOP_PREFIX). */
    protected function getLoopKeys(array $row): array
    {
        return array_values(array_filter(
            array_keys($row),
            fn($k) => str_starts_with((string)$k, self::LOOP_PREFIX)
        ));
    }

    /** Remove o prefixo de loop de uma chave. */
    protected function stripLoopPrefix(string $key): string
    {
        return substr($key, strlen(self::LOOP_PREFIX));
    }

    /** Retorna o namespace URI do nó folha identificado pelo nome local da tag. */
    protected function findLeafNamespaceUri(string $tagName): ?string
    {
        if (!isset($this->mapaCabecalho[$tagName])) return null;
        $dom = dom_import_simplexml($this->mapaCabecalho[$tagName]);
        return $dom->namespaceURI ?: null;
    }

    /** Dado um namespace URI, retorna o prefixo correspondente (ex: "lcl", "gen"). */
    protected function findNamespacePrefix(?string $namespaceUri): ?string
    {
        if (!$namespaceUri) return null;
        foreach ($this->namespaces as $prefix => $uri) {
            if ($uri === $namespaceUri) return $prefix;
        }
        return null;
    }

    // =========================================================================
    // INTROSPECÇÃO / UTILITÁRIOS
    // =========================================================================

    /**
     * Retorna a lista de nomes de tags folha encontradas no XML base.
     *
     * Útil para:
     *  - Depuração
     *  - Gerar automaticamente o cabeçalho esperado da planilha
     *  - Validar se todos os cabeçalhos da planilha existem no XML
     *
     * Exemplo no controller:
     *   $generator->loadBase($xmlBaseFullPath);
     *   dd($generator->getLeafTags());
     */
    public function getLeafTags(): array
    {
        return array_keys($this->mapaCabecalho);
    }

    /**
     * Valida se todos os cabeçalhos fixos da planilha existem no XML base.
     * Retorna array com os cabeçalhos que NÃO foram encontrados.
     *
     * Exemplo no controller:
     *   $missing = $generator->validateHeaders(array_keys($data[0]));
     *   if (!empty($missing)) { ... }
     */
    public function validateHeaders(array $headers): array
    {
        $missing = [];
        foreach ($headers as $header) {
            $tagName = str_starts_with($header, self::LOOP_PREFIX)
                ? $this->stripLoopPrefix($header)
                : $header;

            if (!isset($this->mapaCabecalho[$tagName])) {
                $missing[] = $header;
            }
        }
        return $missing;
    }
}
