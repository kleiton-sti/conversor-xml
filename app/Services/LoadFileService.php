<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;


class LoadFileService {

    public $inputFileName = 'C:\Users\25606\Downloads\PROCURADOR.xlsx';

    // Método para ler o arquivo
    public function reader(): array {
        $excel =IOFactory::load($this->inputFileName);
        $sheet = $excel->getSheet(0);
        $rows = $sheet->toArray();

        $data = [];

        foreach ($rows as $row) {
            //verifica se a linha está vazia
            if (!empty(array_filter($row))) {
                $lines = $this->generateLetter(count($row));
                $data[] = array_combine($lines, $row);
            }

        }

        return $data;
    }

    // Método para gerar as letras, chaves de acesso no array associativo
    private function generateLetter($quantity): array {
        $letters = [];
        $i = 0;

        while (count($letters) < $quantity) {
            $letters[]  = Coordinate::stringFromColumnIndex(++$i);
        }

        return $letters;
    }
}