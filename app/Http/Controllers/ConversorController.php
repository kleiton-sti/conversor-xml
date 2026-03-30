<?php

namespace App\Http\Controllers;

use App\Http\Requests\XmlRequest;
use App\Services\XmlGeneratorDinamicoService;
use Illuminate\Routing\Controller;
use App\Services\LoadFileService;
use Illuminate\Support\Facades\Log;

class ConversorController extends Controller
{
    public function index()
    {
        return view('conversor.index');
    }

    public function processar(XmlRequest $request)
    {
        try {
            $xmlBasePath = $request->file('xml_base')->store('temp_uploads', 'local');
            $planilhaPath = $request->file('planilha')->store('temp_uploads', 'local');

            $xmlBaseFullPath = storage_path("app/private/{$xmlBasePath}");
            $planilhaFullPath = storage_path("app/private/{$planilhaPath}");

            $data = (new LoadFileService($planilhaFullPath))->lerComCabecalho();

            if (empty($data)) {
                throw new \RuntimeException('A planilha está vazia ou não possui dados após o cabeçalho.');
            }

            $outputFileName = 'xmlConverted_' . date('Ymd_His') . '.xml';
            $outputPath = storage_path("xml/{$outputFileName}");

            if (!is_dir(storage_path('xml'))) {
                mkdir(storage_path('xml'), 0755, true);
            }

            $generator = new XmlGeneratorDinamicoService();
            $generator->carregarBase($xmlBaseFullPath);
            $xmlContent = $generator->gerar($data, $outputPath);

            @unlink($xmlBaseFullPath);
            @unlink($planilhaFullPath);

            return response()->streamDownload(
                function () use ($xmlContent) {
                    echo $xmlContent; },
                $outputFileName,
                ['Content-Type' => 'application/xml']
            );

        } catch (\Throwable $e) {
            if (isset($xmlBaseFullPath) && file_exists($xmlBaseFullPath))
                @unlink($xmlBaseFullPath);
            if (isset($planilhaFullPath) && file_exists($planilhaFullPath))
                @unlink($planilhaFullPath);

            Log::error('ConversorController::processar — ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->withErrors(['erro' => 'Erro ao gerar o XML: ' . $e->getMessage()]);
        }
    }

}
