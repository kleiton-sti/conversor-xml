<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class LoadFileService
{
    // Guarda o caminho do arquivo Excel que será lido
    protected string $caminhoArquivo;

    public function __construct(string $caminhoArquivo)
    {
        $this->caminhoArquivo = $caminhoArquivo;
    }

    // -------------------------------------------------------------------------
    // MÉTODO ORIGINAL — usado pelo XmlGeneratorService (mapeamento fixo A, B, C...)
    // -------------------------------------------------------------------------

    /**
     * Lê a planilha e devolve cada linha como array associativo
     * onde as chaves são letras de coluna: A, B, C, D...
     *
     * A primeira linha (cabeçalho) é IGNORADA.
     *
     * Exemplo de retorno:
     *   $data[0] = ['A' => '2024', 'B' => '01', 'C' => 'Prefeitura...']
     *   $data[1] = ['A' => '2024', 'B' => '01', 'C' => 'Prefeitura...']
     */
    public function lerPorLetras(): array
    {
        // Carrega o arquivo Excel
        $excel = IOFactory::load($this->caminhoArquivo);

        // Pega a primeira aba da planilha (índice 0)
        $aba = $excel->getSheet(0);

        // Converte a aba inteira para um array PHP simples
        // Parâmetros: valor para célula vazia, calcular fórmulas, formatar valores, índice numérico
        $linhas = $aba->toArray(null, true, true, false);

        $dados = [];

        // array_slice($linhas, 1) → pula a linha 0 (cabeçalho) e começa da linha 1
        foreach (array_slice($linhas, 1) as $linha) {

            // array_filter verifica se a linha tem pelo menos uma célula preenchida
            // Se a linha estiver completamente vazia, pula ela
            $linhaNaoVazia = array_filter($linha, fn($celula) => $celula !== null && $celula !== '');
            if (empty($linhaNaoVazia)) {
                continue;
            }

            // Gera as letras [A, B, C...] com base na quantidade de colunas da linha
            $letras = $this->gerarLetras(count($linha));

            // array_combine junta as letras com os valores da linha
            // Resultado: ['A' => valor1, 'B' => valor2, 'C' => valor3...]
            $dados[] = array_combine($letras, $linha);
        }

        return $dados;
    }

    // -------------------------------------------------------------------------
    // MÉTODO NOVO — usado pelo XmlGeneratorDinamicoService
    // -------------------------------------------------------------------------

    /**
     * Lê a planilha e devolve cada linha como array associativo
     * onde as chaves são os CABEÇALHOS da primeira linha da planilha.
     *
     * A primeira linha É LIDA e usada como nome das colunas.
     *
     * Exemplo:
     *   Planilha:
     *     Linha 1 (cabeçalho): | AnoExercicio | Municipio | [loop]nomeClassificado |
     *     Linha 2 (dados):     | 2024         | São Paulo | João                   |
     *     Linha 3 (dados):     | 2024         | São Paulo | Maria                  |
     *
     *   Retorno:
     *     $dados[0] = ['AnoExercicio' => '2024', 'Municipio' => 'São Paulo', '[loop]nomeClassificado' => 'João']
     *     $dados[1] = ['AnoExercicio' => '2024', 'Municipio' => 'São Paulo', '[loop]nomeClassificado' => 'Maria']
     */
    public function lerComCabecalho(): array
    {
        $excel  = IOFactory::load($this->caminhoArquivo);
        $aba    = $excel->getSheet(0);
        $linhas = $aba->toArray(null, true, true, false);

        if (empty($linhas)) {
            return [];
        }

        // Pega os cabeçalhos da primeira linha e garante que todos são string
        $cabecalhos = array_map('strval', $linhas[0]);

        $dados = [];

        // Percorre as linhas a partir da segunda (índice 1 em diante)
        foreach (array_slice($linhas, 1) as $linha) {

            // Pula linhas completamente vazias
            $linhaNaoVazia = array_filter($linha, fn($celula) => $celula !== null && $celula !== '');
            if (empty($linhaNaoVazia)) {
                continue;
            }

            // Se a linha tiver menos colunas que o cabeçalho, preenche com null no final
            $linha = array_pad($linha, count($cabecalhos), null);

            // Se a linha tiver mais colunas que o cabeçalho, corta o excesso
            $linha = array_slice($linha, 0, count($cabecalhos));

            // Combina cabeçalho com valores: ['AnoExercicio' => '2024', ...]
            $dados[] = array_combine($cabecalhos, $linha);
        }

        return $dados;
    }

    // -------------------------------------------------------------------------
    // MÉTODOS ANTIGOS (mantidos para não quebrar nada que já usa eles)
    // -------------------------------------------------------------------------

    /** @deprecated Use lerPorLetras() */
    public function reader(): array
    {
        return $this->lerPorLetras();
    }

    /** @deprecated Use lerComCabecalho() */
    public function readerWithHeader(): array
    {
        return $this->lerComCabecalho();
    }

    // -------------------------------------------------------------------------
    // AUXILIAR PRIVADO
    // -------------------------------------------------------------------------

    /**
     * Gera um array de letras de coluna no estilo Excel: A, B, C... Z, AA, AB...
     *
     * Exemplo: gerarLetras(3) → ['A', 'B', 'C']
     *          gerarLetras(28) → ['A', 'B', ..., 'Z', 'AA', 'AB']
     *
     * O método Coordinate::stringFromColumnIndex() é da biblioteca PhpSpreadsheet
     * e converte número em letra: 1→A, 2→B, 26→Z, 27→AA, etc.
     */
    private function gerarLetras(int $quantidade): array
    {
        $letras = [];

        for ($i = 1; $i <= $quantidade; $i++) {
            $letras[] = Coordinate::stringFromColumnIndex($i);
        }

        return $letras;
    }
}
