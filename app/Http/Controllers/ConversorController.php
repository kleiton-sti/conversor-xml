<?php

namespace App\Http\Controllers;

use App\Http\Requests\XmlRequest;
use App\Services\GerarXmlDinamicoService;
use Illuminate\Routing\Controller;
use App\Services\LoadFileService;
use App\Services\XmlGeneratorService;
use Illuminate\Http\Request;

class ConversorController extends Controller {

    /**
     * Exibe o formulário de upload
     */
    public function index() {
        return view('conversor.index');
    }

    /**
     * Recebe o XML base + planilha, gera o XML de saída e retorna para download
     */
    public function processar(XmlRequest $request) {

        try {
            // ── Salva arquivos temporários ────────────────────────────────────
            $xmlBasePath  = $request->file('xml_base')->store('temp_uploads', 'local');
            $planilhaPath = $request->file('planilha')->store('temp_uploads', 'local');

            $xmlBaseFullPath  = storage_path("app/private/{$xmlBasePath}");
            $planilhaFullPath = storage_path("app/private/{$planilhaPath}");

            // ── Lê a planilha ─────────────────────────────────────────────────
          $data = (new LoadFileService($planilhaPath))->readerWithHeader();

            if (empty($data)) {
                throw new \RuntimeException('A planilha está vazia ou não possui dados após o cabeçalho.');
            }

            // ── Gera o XML ────────────────────────────────────────────────────
            $outputFileName = 'xmlConverted_' . date('Ymd_His') . '.xml';
            $outputPath     = storage_path("xml/{$outputFileName}");

            // Garante que o diretório existe
            if (!is_dir(storage_path('xml'))) {
                mkdir(storage_path('xml'), 0755, true);
            }

         $generator = new GerarXmlDinamicoService();
            $generator->CarregaXmlBase($xmlBaseFullPath);
            $xmlContent = $generator->generate($data, $outputPath);

            // ── Remove temporários ────────────────────────────────────────────
            @unlink($xmlBaseFullPath);
            @unlink($planilhaFullPath);

            // ── Retorna para download ─────────────────────────────────────────
            return response()->streamDownload(
                function () use ($xmlContent) { echo $xmlContent; },
                $outputFileName,
                ['Content-Type' => 'application/xml']
            );

        } catch (\Throwable $e) {
            // Remove temporários em caso de erro
            if (isset($xmlBaseFullPath) && file_exists($xmlBaseFullPath))  @unlink($xmlBaseFullPath);
            if (isset($planilhaFullPath) && file_exists($planilhaFullPath)) @unlink($planilhaFullPath);

            return back()
                ->withInput()
                ->withErrors(['erro' => 'Erro ao gerar o XML: ' . $e->getMessage()]);
        }
    }
}
