<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class LoadFileService
{
    protected string $caminhoArquivo;

    public function __construct(string $caminhoArquivo)
    {
        $this->caminhoArquivo = $caminhoArquivo;
    }

    public function lerPorLetras(): array
    {
        try {
            $excel  = IOFactory::load($this->caminhoArquivo);
            $aba    = $excel->getSheet(0);
            $linhas = $aba->toArray(null, true, true, false);

            $dados = [];

            foreach (array_slice($linhas, 1) as $linha) {
                $linhaNaoVazia = array_filter($linha, fn($celula) => $celula !== null && $celula !== '');
                if (empty($linhaNaoVazia)) {
                    continue;
                }

                $letras  = $this->gerarLetras(count($linha));
                $dados[] = array_combine($letras, $linha);
            }

            return $dados;
        } catch (\Throwable $e) {
            throw new \RuntimeException('Erro ao ler a planilha (lerPorLetras): ' . $e->getMessage(), 0, $e);
        }
    }

    public function lerComCabecalho(): array
    {
        try {
            $excel  = IOFactory::load($this->caminhoArquivo);
            $aba    = $excel->getSheet(0);
            $linhas = $aba->toArray(null, true, true, false);

            if (empty($linhas)) {
                return [];
            }

            $cabecalhos = array_map('strval', $linhas[0]);
            $dados      = [];

            foreach (array_slice($linhas, 1) as $linha) {
                $linhaNaoVazia = array_filter($linha, fn($celula) => $celula !== null && $celula !== '');
                if (empty($linhaNaoVazia)) {
                    continue;
                }

                $linha   = array_pad($linha, count($cabecalhos), null);
                $linha   = array_slice($linha, 0, count($cabecalhos));
                $dados[] = array_combine($cabecalhos, $linha);
            }

            return $dados;
        } catch (\Throwable $e) {
            throw new \RuntimeException('Erro ao ler a planilha (lerComCabecalho): ' . $e->getMessage(), 0, $e);
        }
    }

    private function gerarLetras(int $quantidade): array
    {
        $letras = [];

        for ($i = 1; $i <= $quantidade; $i++) {
            $letras[] = Coordinate::stringFromColumnIndex($i);
        }

        return $letras;
    }
}
