<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\LoadFileService;

class ConversorController extends Controller {
    public array $data;
    public $xml;
    public $namespace;

    public function __construct() {
        //Carrega dados da planilha
        $this->data = (new LoadFileService())->reader();
    }


    
    //adiciona os dados
    public function addClassificados() {

        //carrega padrão AUDESP XML
        $this->xml = simplexml_load_file(base_path("resources/views/xml/xmlPattern.xml"));

        $this->namespace = $this->xml->getNamespaces(true);

        // Acessa os nós usando os namespaces corretamente
        $classificacao = $this->xml->children($this->namespace['cpe'])->Classificacao ?? null;
        $classificados = $classificacao->children($this->namespace['cpe'])->Classificados ?? null;
        $listaClassificados = $classificados->children($this->namespace['lcl']);
        $data = $this->data;

        
        foreach ($listaClassificados as $classificado) {
            
            $classificado->cpfClassificado = $data[0]["S"];
            $classificado->nomeClassificado = $data[1]["T"];
            $classificado->ordemClassificacao = $data[2]["U"];
        } 

        for ($i = 1; $i < count($data); $i++) {
            $novo = $listaClassificados->addChild("lcl:Classificado", null, $this->namespace['lcl']);
            $cpf = $novo->addChild("lcl:cpfClassificado", null, $this->namespace['lcl']);
            $cpf->addChild("gen:Numero", $data[$i]["S"], $this->namespace['gen']);

            $novo->addChild("lcl:nomeClassificado", $data[$i]["T"], $this->namespace['lcl']);
            $novo->addChild("lcl:ordemClassificacao", $data[$i]["U"], $this->namespace['lcl']);
        }

        $this->xml->asXML(base_path("resources/views/xml/xmlConverted.xml"));
        dd($this->xml);
    }
    
}