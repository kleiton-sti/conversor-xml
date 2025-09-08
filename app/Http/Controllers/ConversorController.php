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

    public function addClassificados() {
    
    // Caminho para o arquivo XML
    $xmlFilePath = base_path("storage/xml/xmlPattern.xml");

    // Carrega o arquivo XML em um objeto DOM bem estruturado
    $dom = new \DOMDocument('1.0','UTF-8');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->load($xmlFilePath);

    // Carrega XML com SimpleXML para manipulação mais simples
    $this->xml = simplexml_load_file($xmlFilePath);

    // Recupera namespaces
    $this->namespace = $this->xml->getNamespaces(true);

    // Navega pelos nós usando namespaces
    $classificacao = $this->xml->children($this->namespace['cpe'])->Classificacao ?? null;
    $classificados = $classificacao->children($this->namespace['cpe'])->Classificados ?? null;

    $data = $this->data;

    for ($i = 0; $i < count($data); $i++) {
        $novo = $classificados->addChild('lcl:Classificado', null, $this->namespace['lcl']);
        $cpf = $novo->addChild('lcl:cpfClassificado', null, $this->namespace['lcl']);
        
        // Adiciona o atributo Tipo="02" no nó cpfClassificado
        $cpfDom = dom_import_simplexml($cpf);
        $cpfDom->setAttribute('Tipo', '02');

        $cpf->addChild('gen:Numero', $data[$i]["S"], $this->namespace['gen']);

        $novo->addChild('lcl:nomeClassificado', $data[$i]["T"], $this->namespace['lcl']);
        $novo->addChild('lcl:ordemClassificacao', $data[$i]["U"], $this->namespace['lcl']);
    }

    // Salva o XML modificado temporariamente em string
    $xmlString = $this->xml->asXML();

    // Carrega a string XML no DOM para aplicar a formatação (identação)
    $dom->loadXML($xmlString);

    // Salva o XML formatado no arquivo desejado
    $dom->save(base_path("storage/xml/xmlConverted.xml"));

    dd('XML gerado com sucesso em resources/views/xml/xmlConverted.xml');
}

    
}