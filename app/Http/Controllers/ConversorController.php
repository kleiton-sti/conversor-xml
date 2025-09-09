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

    // Cria um objeto DOM bem estruturado
    $dom = new \DOMDocument('1.0','UTF-8');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    

    // Carrega XML com SimpleXML para manipulação mais simples
    $this->xml = simplexml_load_file($xmlFilePath);

    // Recupera namespaces
    $this->namespace = $this->xml->getNamespaces(true);

    // Descritor do XML
    $descritor = $this->xml->children($this->namespace['cpe'])->Descritor ?? null;
    $descritor->children($this->namespace['gen'])->DetaCriacaoXML = date('Y-m-d');

    // IdentificacaoConcursoPublicoEfetivo do XML
    $identificacaoConcursoPublicoEfetivo = $this->xml->children($this->namespace['cpe'])->IdentificacaoConcursoPublicoEfetivo ?? null;
    $identificacaoConcursoPublicoEfetivo->children($this->namespace['ap'])->numeroProcessoSelecao = $this->data[0]["F"];
    $identificacaoConcursoPublicoEfetivo->children($this->namespace['ap'])->anoProcessoSelecao = $this->data[0]["G"];

    // Dados do concurso do XML
    $dadosConcursoPublicoEfetivo = $this->xml->children($this->namespace['cpe'])->DadosConcursoPublicoEfetivo ?? null;
    $dadosConcursoPublicoEfetivo->children($this->namespace['cpe'])->pctVagasEspeciaisAfro = $this->data[0]["H"];
    $dadosConcursoPublicoEfetivo->children($this->namespace['cpe'])->pctVagasEspeciaisDef = $this->data[0]["I"];
    $dadosConcursoPublicoEfetivo->children($this->namespace['cpe'])->codigoFatorArredondamento = $this->data[0]["J"];

    $dadosConcursoPublicoEfetivo->children($this->namespace["cpe"])->prazoValidadeInicial ?? null;
    $dadosConcursoPublicoEfetivo->children($this->namespace["ap"])->anoPrazo = $this->data[0]["K"];
    $dadosConcursoPublicoEfetivo->children($this->namespace["ap"])->mesPrazo = $this->data[0]["L"];
    $dadosConcursoPublicoEfetivo->children($this->namespace["ap"])->diaPrazo = $this->data[0]["M"];

    $dadosConcursoPublicoEfetivo->children($this->namespace["cpe"])->prazoPrevistoProrrogacao ?? null;
    $dadosConcursoPublicoEfetivo->children($this->namespace["ap"])->anoPrazo = $this->data[0]["N"];
    $dadosConcursoPublicoEfetivo->children($this->namespace["ap"])->mesPrazo = $this->data[0]["O"];
    $dadosConcursoPublicoEfetivo->children($this->namespace["ap"])->diaPrazo = $this->data[0]["P"];

    $dadosConcursoPublicoEfetivo->children($this->namespace["cpe"])->edital ?? null;
    $dadosConcursoPublicoEfetivo->children($this->namespace["cpe"])->dataPublicacaoEdital = $this->data[0]["Q"];
    $dadosConcursoPublicoEfetivo->children($this->namespace["cpe"])->meioPublicacaoEdital = $this->data[0]["R"];



    // Lista de cargos do XML
    $listaCargos = $this->xml->children($this->namespace['cpe'])-> CargoEdital ?? null;
    $listaCargos->children($this->namespace['ap'])->CodigoCargo = $this->data[1]["U"];
    $listaCargos->children($this->namespace['ap'])->EntidadePrevista;
    $listaCargos->children($this->namespace['ap'])->CodigoEntidadePrevista = $this->data[0]["S"];
    $listaCargos->children($this->namespace['ap'])->CargoMunicipioEntidadePrevista = $this->data[0]["T"];

    $listaCargos->children($this->namespace['cpe'])->permiteAtribPontoTitulo = $this->data[0]["V"];
    $listaCargos->children($this->namespace['cpe'])->numVagasCargoFuncao = $this->data[0]["W"];

    // a finalizar restante dos campos de cargos


    // Classificados do XML
    $classificacao = $this->xml->children($this->namespace['cpe'])->Classificacao ?? null;
    $classificados = $classificacao->children($this->namespace['cpe'])->Classificados ?? null;

    $data = $this->data;

    for ($i = 0; $i < count($data); $i++) {
        $novo = $classificados->addChild('lcl:Classificado', null, $this->namespace['lcl']);
        $cpf = $novo->addChild('lcl:cpfClassificado', null, $this->namespace['lcl']);
        
        // Adiciona o atributo Tipo="02" no nó cpfClassificado
        $cpfDom = dom_import_simplexml($cpf);
        $cpfDom->setAttribute('Tipo', '02');

        $cpf->addChild('gen:Numero', $data[$i]["AI"], $this->namespace['gen']);

        $novo->addChild('lcl:nomeClassificado', $data[$i]["AJ"], $this->namespace['lcl']);
        $novo->addChild('lcl:ordemClassificacao', $data[$i]["AK"], $this->namespace['lcl']);
    }

    // Salva o XML modificado temporariamente em string
    $xmlString = $this->xml->asXML();

    // Carrega a string XML no DOM para aplicar a formatação (identação)
    $dom->loadXML($xmlString);

    // Salva o XML formatado no arquivo desejado
    $dom->save(base_path("storage/xml/xmlConverted.xml"));

    dd('XML gerado com sucesso');
}

    
}