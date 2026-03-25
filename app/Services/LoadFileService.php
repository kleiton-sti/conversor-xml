<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class LoadFileService {

    protected string $inputFileName;

    public function __construct(string $inputFileName) {
        $this->inputFileName = $inputFileName;
    }

    /**
     * Lê o arquivo Excel e retorna array associativo com letras como chaves (A, B, C...)
     * Ignora a primeira linha (cabeçalho).
     *
     * Usado pelo XmlGeneratorService (versão com mapeamento fixo).
     */
    public function reader(): array {
        $excel = IOFactory::load($this->inputFileName);
        $sheet = $excel->getSheet(0);
        $rows  = $sheet->toArray(null, true, true, false);

        $data = [];

        // Pula a primeira linha (cabeçalho)
        foreach (array_slice($rows, 1) as $row) {
            if (!empty(array_filter($row, fn($v) => $v !== null && $v !== ''))) {
                $letters  = $this->generateLetter(count($row));
                $data[]   = array_combine($letters, $row);
            }
        }

        return $data;
    }

    /**
     * Lê o arquivo Excel e retorna array associativo usando os cabeçalhos da
     * primeira linha como chaves.
     *
     * Usado pelo XmlGeneratorDynamicService (versão genérica/dinâmica).
     *
     * Exemplo de retorno:
     *   [
     *     ['AnoExercicio' => '2024', 'Municipio' => 'São Paulo', '[loop]nomeClassificado' => 'João'],
     *     ['AnoExercicio' => '2024', 'Municipio' => 'São Paulo', '[loop]nomeClassificado' => 'Maria'],
     *   ]
     */
    public function readerWithHeader(): array {
        $excel = IOFactory::load($this->inputFileName);
        $sheet = $excel->getSheet(0);
        $rows  = $sheet->toArray(null, true, true, false);

        if (empty($rows)) {
            return [];
        }

        // Primeira linha = cabeçalhos
        $headers = array_map('strval', $rows[0]);

        $data = [];

        foreach (array_slice($rows, 1) as $row) {
            // Pula linhas completamente vazias
            if (empty(array_filter($row, fn($v) => $v !== null && $v !== ''))) {
                continue;
            }

            // Garante que a linha tenha o mesmo número de colunas que o cabeçalho
            $row = array_pad($row, count($headers), null);
            $row = array_slice($row, 0, count($headers));

            $data[] = array_combine($headers, $row);
        }

        return $data;
    }

    /**
     * Gera array de letras de coluna (A, B, ..., Z, AA, AB...) para uso como chaves.
     */
    private function generateLetter(int $quantity): array {
        $letters = [];
        for ($i = 1; $i <= $quantity; $i++) {
            $letters[] = Coordinate::stringFromColumnIndex($i);
        }
        return $letters;
    }
}
