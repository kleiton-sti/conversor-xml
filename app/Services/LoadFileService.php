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

    // converter em um objeto manipulável
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
   
}
