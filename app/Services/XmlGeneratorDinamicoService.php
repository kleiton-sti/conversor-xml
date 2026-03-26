<?php

namespace App\Services;

use DOMDocument;
use SimpleXMLElement;
use PhpOffice\PhpSpreadsheet\Shared\Date;

/**
 * XmlGeneratorDinamicoService
 *
 * Versão genérica do gerador de XML.
 *
 *
 * COMO FUNCIONA (resumo simples):
 *   1. Carrega o XML base e varre todos os seus nós terminais (sem filhos),
 *      guardando cada um pelo nome da tag. Ex: 'AnoExercicio' → nó do XML.
 *
 *   2. A planilha tem cabeçalhos na linha 1. Cada cabeçalho deve ser igual
 *      ao nome da tag XML que ele quer preencher.
 *
 *   3. Para campos que aparecem UMA vez no XML (ex: AnoExercicio, Municipio),
 *      o cabeçalho é escrito normalmente: "AnoExercicio"
 *
 *   4. Para campos que se REPETEM (ex: lista de classificados), o cabeçalho
 *      recebe o prefixo "[loop]": "[loop]nomeClassificado"
 *      Cada linha da planilha gera um novo bloco repetido no XML.
 *
 * EXEMPLO DE PLANILHA:
 *   | AnoExercicio | Municipio | [loop]nomeClassificado | [loop]ordemClassificacao |
 *   | 2024         | São Paulo | João da Silva          | 1                        |
 *   | 2024         | São Paulo | Maria Souza            | 2                        |
 *
 * COMO USAR NO CONTROLLER:
 *   $dados     = (new LoadFileService($caminhoPlanilha))->lerComCabecalho();
 *   $gerador   = new XmlGeneratorDinamicoService();
 *   $gerador->carregarBase($caminhoXmlBase);
 *   $xmlGerado = $gerador->gerar($dados, $caminhoSaida);
 */
class XmlGeneratorDinamicoService
{
    // O XML base carregado como objeto SimpleXMLElement
    protected SimpleXMLElement $xml;

    // Lista de namespaces do XML (ex: ['lcl' => 'http://...', 'gen' => 'http://...'])
    protected array $namespaces = [];

    /**
     * Mapa de nós terminais do XML base.
     *
     * Formato: ['NomeDaTag' => $noSimpleXML]
     *
     * Exemplo: ['AnoExercicio' => <nó>, 'Municipio' => <nó>, 'nomeClassificado' => <nó>]
     *
     * Isso permite encontrar qualquer nó do XML pelo nome da tag,
     * sem precisar navegar manualmente na estrutura toda.
     */
    protected array $mapaDeNos = [];

    // Prefixo que identifica colunas repetíveis na planilha
    const PREFIXO_LOOP = '[loop]';

    // =========================================================================
    // 1. CARREGAMENTO DO XML BASE
    // =========================================================================

    /**
     * Lê o arquivo XML base, converte para UTF-8 se necessário,
     * e monta o mapa de nós terminais.
     */
    public function carregarBase(string $caminhoXml): void
    {
        if (!file_exists($caminhoXml)) {
            throw new \RuntimeException("Arquivo XML base não encontrado: {$caminhoXml}");
        }

        $conteudo = file_get_contents($caminhoXml);

        // Detecta o encoding declarado no XML (ex: encoding="ISO-8859-1")
        // Se não for UTF-8, converte para evitar problemas de caracteres
        $encoding = 'UTF-8';
        if (preg_match('/encoding=["\']([^"\']+)["\']/', $conteudo, $encontrado)) {
            $encoding = strtoupper($encontrado[1]);
        }

        if ($encoding !== 'UTF-8') {
            // mb_convert_encoding converte o texto do encoding original para UTF-8
            $conteudo = mb_convert_encoding($conteudo, 'UTF-8', $encoding);
            // Atualiza também a declaração dentro do XML para não criar conflito
            $conteudo = preg_replace('/encoding=["\'][^"\']+["\']/', 'encoding="UTF-8"', $conteudo);
        }

        // Converte a string XML em objeto PHP para podermos navegar nele
        // LIBXML_NOCDATA faz seções CDATA aparecerem como texto normal
        $this->xml = simplexml_load_string($conteudo, 'SimpleXMLElement', LIBXML_NOCDATA);

        if (!$this->xml) {
            throw new \RuntimeException("Falha ao ler o XML base. Verifique se o arquivo é um XML válido.");
        }

        // getNamespaces(true) retorna TODOS os namespaces do XML, inclusive os aninhados
        // true = recursivo (pega namespaces de todos os níveis, não só do nó raiz)
        $this->namespaces = $this->xml->getNamespaces(true);

        // // 
        $this->mapaDeNos = [];
        $this->varrerNosTerminais($this->xml);
    }

    // =========================================================================
    // 2. GERAÇÃO DO XML
    // =========================================================================

    /**
     * Recebe os dados da planilha, preenche o XML base e salva o arquivo de saída.
     *
     * @param array  $dados       Retorno do LoadFileService::lerComCabecalho()
     * @param string $caminhoSaida  Onde salvar o XML gerado
     * @return string               O XML gerado como texto (para download)
     */
    public function gerar(array $dados, string $caminhoSaida): string
    {
        if (empty($dados)) {
            throw new \InvalidArgumentException("Nenhum dado encontrado na planilha.");
        }

        $primeiraLinha  = $dados[0];
        $camposFixos    = $this->obterCamposFixos($primeiraLinha);
        $camposRepetidos = $this->obterCamposRepetidos($primeiraLinha);

        // ── Passo 1: preenche campos fixos (usa só a primeira linha) ──────────
        foreach ($camposFixos as $cabecalho) {
            $valor = $primeiraLinha[$cabecalho] ?? '';

            // Verifica se existe um nó no XML com esse nome de tag
            if (isset($this->mapaDeNos[$cabecalho])) {
                // $no[0] = valor é a forma do SimpleXML de atribuir texto a um nó
                $this->mapaDeNos[$cabecalho][0] = $this->formatarValor($cabecalho, $valor);
            }
            // Se não existir no XML, simplesmente ignora (sem erro)
        }

        // ── Passo 2: preenche campos repetidos (loop por linha) ───────────────
        if (!empty($camposRepetidos)) {
            $this->preencherCamposRepetidos($dados, $camposRepetidos);
        }

        // ── Passo 3: formata e salva ──────────────────────────────────────────
        // SimpleXML não indenta bem o XML, então usamos DOMDocument só para formatar
        $xmlTexto = $this->xml->asXML();

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false; // remove espaços extras
        $dom->formatOutput       = true;  // ativa indentação automática
        $dom->loadXML($xmlTexto);
        $dom->save($caminhoSaida);

        return $dom->saveXML();
    }

    // =========================================================================
    // 3. PREENCHIMENTO DOS CAMPOS REPETIDOS (LOOP)
    // =========================================================================

    /**
     * Para cada linha da planilha, cria um novo bloco de nós no XML.
     *
     * Exemplo: cada linha da planilha vira um <lcl:Classificado> dentro de <lcl:Classificados>.
     *
     * COMO DETECTA O CONTÊINER automaticamente:
     *   - Pega o primeiro campo de loop que existe no mapaDeNos
     *   - Vai até o nó pai desse campo (ex: <Classificado>)
     *   - Vai até o pai do pai (ex: <Classificados>) — esse é o CONTÊINER
     *   - Apaga os filhos de modelo que vieram do XML base
     *   - Cria um filho novo por linha de dados
     */
    protected function preencherCamposRepetidos(array $dados, array $camposRepetidos): void
    {
        // ── Encontra o nó contêiner e o nome do nó filho repetível ───────────

        $nodoDOMConteiner  = null; // ex: o nó DOM de <lcl:Classificados>
        $nomeFilho         = null; // ex: "Classificado"
        $namespaceFilho    = null; // ex: "http://www.tce.sp.gov.br/lcl"

        foreach ($camposRepetidos as $campo) {
            $nomeDaTag = $this->removerPrefixoLoop($campo);

            // Pula campos de loop que não existem no XML base
            if (!isset($this->mapaDeNos[$nomeDaTag])) {
                continue;
            }

            // dom_import_simplexml converte um nó SimpleXML para nó DOM
            // DOM permite navegar para o nó pai, o que SimpleXML não faz facilmente
            $noDaFolha    = dom_import_simplexml($this->mapaDeNos[$nomeDaTag]);
            $noItem       = $noDaFolha->parentNode;       // ex: <Classificado>
            $noConteiner  = $noItem?->parentNode;         // ex: <Classificados>

            if ($noConteiner) {
                $nodoDOMConteiner = $noConteiner;
                $nomeFilho        = $noItem->localName;       // nome sem prefixo
                $namespaceFilho   = $noItem->namespaceURI ?: null;
                break; // achou, não precisa continuar
            }
        }

        // Se não achou nenhum contêiner, não há o que fazer
        if (!$nodoDOMConteiner || !$nomeFilho) {
            return;
        }

        // ── Remove os filhos de modelo que vieram do XML base ─────────────────
        // (o XML base tem um exemplo de <Classificado> para servir de molde,
        //  mas não queremos ele no resultado — queremos os dados reais)
        while ($nodoDOMConteiner->firstChild) {
            $nodoDOMConteiner->removeChild($nodoDOMConteiner->firstChild);
        }

        // Reconverte o contêiner de DOM de volta para SimpleXML
        // (precisamos do SimpleXML para usar o addChild de forma mais simples)
        $noConteinerSimpleXML = simplexml_import_dom($nodoDOMConteiner);

        // Descobre o prefixo do namespace do nó filho (ex: "lcl")
        $prefixoFilho   = $this->buscarPrefixoNamespace($namespaceFilho);
        $nomeQualificado = $prefixoFilho ? "{$prefixoFilho}:{$nomeFilho}" : $nomeFilho;

        // ── Cria um nó filho por linha da planilha ────────────────────────────
        foreach ($dados as $linha) {

            // Pula a linha se todos os campos de loop estiverem vazios
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

            // Cria o nó filho (ex: <lcl:Classificado>)
            $novoBloco = $noConteinerSimpleXML->addChild($nomeQualificado, null, $namespaceFilho);

            // Adiciona cada subcampo dentro do bloco
            foreach ($camposRepetidos as $campo) {
                $nomeDaTag  = $this->removerPrefixoLoop($campo);
                $valor      = $linha[$campo] ?? '';

                // Busca o namespace correto do subcampo (olhando no mapaDeNos original)
                $namespaceDaTag = $this->buscarNamespaceDaTag($nomeDaTag);
                $prefixoDaTag   = $this->buscarPrefixoNamespace($namespaceDaTag);
                $tagQualificada = $prefixoDaTag ? "{$prefixoDaTag}:{$nomeDaTag}" : $nomeDaTag;

                // htmlspecialchars protege caracteres especiais XML como < > & "
                $novoBloco->addChild(
                    $tagQualificada,
                    htmlspecialchars($this->formatarValor($nomeDaTag, (string)$valor), ENT_XML1, 'UTF-8'),
                    $namespaceDaTag ?: null
                );
            }
        }
    }

    // =========================================================================
    // 4. VARREDURA DOS NÓS TERMINAIS DO XML
    // =========================================================================

    /**
     * Varre o XML recursivamente e registra em $this->mapaDeNos
     * todos os nós que são "terminais" (não têm filhos — são os que têm texto).
     *
     * Por que só os terminais?
     *   Porque só eles têm um valor a ser preenchido.
     *   Nós com filhos são apenas agrupadores estruturais.
     *
     * A recursão funciona assim:
     *   varrerNosTerminais(<raiz>)
     *     └─ para cada filho de <raiz>:
     *          varrerNosTerminais(<filho>)
     *            └─ para cada filho de <filho>:
     *                 ... e assim até chegar num nó sem filhos (terminal)
     */
    protected function varrerNosTerminais(SimpleXMLElement $no): void
    {
        // Precisamos checar filhos em TODOS os namespaces
        // porque com namespaces, $no->children() sem parâmetro pode não retornar tudo
        $todosNamespaces = array_merge(['' => null], $this->namespaces);
        $temFilhos = false;

        foreach ($todosNamespaces as $ns) {
            // Se o namespace for null/vazio, chama children() sem parâmetro
            $filhos = $ns ? $no->children($ns) : $no->children();

            foreach ($filhos as $filho) {
                $temFilhos = true;
                // Chama recursivamente para o filho
                $this->varrerNosTerminais($filho);
            }
        }

        // Se não tem filhos, é um nó terminal → registra no mapa
        if (!$temFilhos) {
            $nomeDaTag = $no->getName(); // pega só o nome local, sem prefixo

            // Se já existe uma tag com esse nome, mantém a primeira encontrada
            if (!isset($this->mapaDeNos[$nomeDaTag])) {
                $this->mapaDeNos[$nomeDaTag] = $no;
            }
        }
    }

    // =========================================================================
    // 5. FORMATAÇÃO DE VALORES
    // =========================================================================

    /**
     * Aplica formatação automática com base no nome da tag:
     *
     *   - Tags com "data" no nome → formata como Y-m-d (ex: "2024-03-15")
     *   - Tags com "cpf" no nome  → remove tudo que não é número
     *   - Demais                  → retorna como texto sem alteração
     *
     * Exemplos:
     *   formatarValor('dataPublicacao', '15/03/2024') → '2024-03-15'
     *   formatarValor('cpfClassificado', '123.456.789-00') → '12345678900'
     *   formatarValor('nomeClassificado', 'João Silva') → 'João Silva'
     */
    protected function formatarValor(string $nomeDaTag, $valor): string
    {
        if ($valor === null || $valor === '') {
            return '';
        }

        $nomeMinusculo = strtolower($nomeDaTag);

        if (str_contains($nomeMinusculo, 'data')) {
            return $this->formatarData($valor);
        }

        if (str_contains($nomeMinusculo, 'cpf')) {
            // preg_replace('/\D/', '') remove qualquer coisa que não seja dígito
            return preg_replace('/\D/', '', (string)$valor);
        }

        return (string)$valor;
    }

    /**
     * Formata datas em três situações:
     *
     *   1. Número  → é o formato serial do Excel (ex: 45370 = 15/03/2024)
     *                usa a biblioteca PhpSpreadsheet para converter
     *
     *   2. String "d/m/Y" → formato brasileiro (ex: "15/03/2024")
     *
     *   3. String "Y-m-d" → já está no formato correto (ex: "2024-03-15")
     *
     * Sempre retorna no formato "Y-m-d" que o XML espera.
     */
    protected function formatarData($valor): string
    {
        if (empty($valor)) {
            return '';
        }

        // Caso 1: número serial do Excel
        if (is_numeric($valor)) {
            $data = Date::excelToDateTimeObject((float)$valor);
            return $data->format('Y-m-d');
        }

        // Caso 2: string no formato dia/mês/ano
        $data = \DateTime::createFromFormat('d/m/Y', (string)$valor);
        if ($data) {
            return $data->format('Y-m-d');
        }

        // Caso 3: string já no formato ano-mês-dia
        $data = \DateTime::createFromFormat('Y-m-d', (string)$valor);
        if ($data) {
            return $data->format('Y-m-d');
        }

        // Se não reconheceu nenhum formato, devolve como veio
        return (string)$valor;
    }

    // =========================================================================
    // 6. AUXILIARES
    // =========================================================================

    /**
     * Separa as colunas FIXAS da linha (sem o prefixo [loop]).
     * Campos fixos aparecem uma única vez no XML.
     */
    protected function obterCamposFixos(array $linha): array
    {
        return array_values(array_filter(
            array_keys($linha),
            fn($chave) => !str_starts_with((string)$chave, self::PREFIXO_LOOP)
        ));
    }

    /**
     * Separa as colunas REPETIDAS da linha (com o prefixo [loop]).
     * Campos repetidos geram um novo bloco no XML por linha da planilha.
     */
    protected function obterCamposRepetidos(array $linha): array
    {
        return array_values(array_filter(
            array_keys($linha),
            fn($chave) => str_starts_with((string)$chave, self::PREFIXO_LOOP)
        ));
    }

    /**
     * Remove o prefixo "[loop]" de um nome de campo.
     * Ex: "[loop]nomeClassificado" → "nomeClassificado"
     */
    protected function removerPrefixoLoop(string $campo): string
    {
        return substr($campo, strlen(self::PREFIXO_LOOP));
    }

    /**
     * Busca o namespace URI de uma tag no mapaDeNos.
     * Converte o nó SimpleXML para DOM para conseguir ler o namespaceURI.
     *
     * Ex: para a tag "nomeClassificado" pode retornar "http://www.tce.sp.gov.br/lcl"
     */
    protected function buscarNamespaceDaTag(string $nomeDaTag): ?string
    {
        if (!isset($this->mapaDeNos[$nomeDaTag])) {
            return null;
        }

        $no = dom_import_simplexml($this->mapaDeNos[$nomeDaTag]);
        return $no->namespaceURI ?: null;
    }

    /**
     * Dado um namespace URI, retorna o prefixo correspondente.
     *
     * Ex: "http://www.tce.sp.gov.br/lcl" → "lcl"
     *     "http://www.tce.sp.gov.br/gen" → "gen"
     *
     * Isso é necessário para montar o nome qualificado da tag
     * ao usar addChild() no SimpleXML: "lcl:NomeDaTag"
     */
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
    // 7. UTILITÁRIOS OPCIONAIS (para depuração / validação)
    // =========================================================================

    /**
     * Retorna todos os nomes de tags terminais encontrados no XML base.
     *
     * Use para descobrir quais cabeçalhos a planilha deve ter.
     *
     * Exemplo no controller:
     *   $gerador->carregarBase($caminhoXml);
     *   dd($gerador->listarTagsDisponiveis());
     */
    public function listarTagsDisponiveis(): array
    {
        return array_keys($this->mapaDeNos);
    }

    /**
     * Verifica se os cabeçalhos da planilha existem no XML base.
     * Retorna a lista dos cabeçalhos que NÃO foram encontrados.
     *
     * Útil para mostrar um aviso ao usuário se a planilha não bater com o XML.
     *
     * Exemplo no controller:
     *   $naoEncontrados = $gerador->validarCabecalhos(array_keys($dados[0]));
     *   if (!empty($naoEncontrados)) {
     *       // avisa o usuário
     *   }
     */
    public function validarCabecalhos(array $cabecalhos): array
    {
        $naoEncontrados = [];

        foreach ($cabecalhos as $cabecalho) {
            // Remove o prefixo [loop] antes de verificar
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
