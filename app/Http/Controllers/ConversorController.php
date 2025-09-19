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

    // Dados da planilha
    $data = $this->data;
    
    // Caminho para o arquivo XML
    $xmlFilePath = base_path("storage/xml/se_ListaClassificação_2025_modelo.xml");

    // Cria um objeto DOM bem estruturado
    $dom = new \DOMDocument('1.0','UTF-8');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    

    // Carrega XML com SimpleXML para manipulação mais simples
    $this->xml = simplexml_load_file($xmlFilePath);

    // Recupera namespaces
    $this->namespace = $this->xml->getNamespaces(true);

    // Descritor do XML
    $descritor = $this->xml->children($this->namespace['lcl'])->Descritor ?? null;
    $descritor->children($this->namespace['gen'])-> AnoExercicio = $this->data[0]["A"];
    $descritor->children($this->namespace['gen'])-> TipoDocumento = $this->data[0]["B"];
    $descritor->children($this->namespace['gen'])-> Entidade = $this->data[0]["C"];
    $descritor->children($this->namespace['gen'])-> Municipio = $this->data[0]["D"];
    $descritor->children($this->namespace['gen'])-> DataCriacaoXML = date('Y-m-d');

    //Tipo Processo Selecao do XML
    $tipoProcessoSelecao = $this->xml->children($this->namespace['lcl'])->TipoProcessoSelecao ?? null;
    $tipoProcessoSelecao = $this->data[0]["F"];

    // IdentificacaoConcursoPublicoEfetivo do XML
    $IdentificacaoProcessoSelecao = $this->xml->children($this->namespace['lcl'])->IdentificacaoProcessoSelecao ?? null;
    $IdentificacaoProcessoSelecao->children($this->namespace['ap'])->numeroProcessoSelecao = $this->data[0]["G"];
    $IdentificacaoProcessoSelecao->children($this->namespace['ap'])->anoProcessoSelecao = $this->data[0]["H"];

    // Dados do concurso do XML
    // $dadosConcursoPublicoEfetivo = $this->xml->children($this->namespace['cpe'])->DadosConcursoPublicoEfetivo ?? null;
    // $dadosConcursoPublicoEfetivo->children($this->namespace['cpe'])->pctVagasEspeciaisAfro = $this->data[0]["H"];
    // $dadosConcursoPublicoEfetivo->children($this->namespace['cpe'])->pctVagasEspeciaisDef = $this->data[0]["I"];
    // $dadosConcursoPublicoEfetivo->children($this->namespace['cpe'])->codigoFatorArredondamento = $this->data[0]["J"];

    // $prazoValidadeInicial = $dadosConcursoPublicoEfetivo->children($this->namespace["cpe"])->prazoValidadeInicial ?? null;
    // $prazoValidadeInicial->children($this->namespace["ap"])->anoPrazo = $this->data[0]["K"];
    // $prazoValidadeInicial->children($this->namespace["ap"])->mesPrazo = $this->data[0]["L"];
    // $prazoValidadeInicial->children($this->namespace["ap"])->diaPrazo = $this->data[0]["M"];

    // $prazoPrevistoProrrogacao = $dadosConcursoPublicoEfetivo->children($this->namespace["cpe"])->prazoPrevistoProrrogacao ?? null;
    // $prazoPrevistoProrrogacao->children($this->namespace["ap"])->anoPrazo = $this->data[0]["N"];
    // $prazoPrevistoProrrogacao->children($this->namespace["ap"])->mesPrazo = $this->data[0]["O"];
    // $prazoPrevistoProrrogacao->children($this->namespace["ap"])->diaPrazo = $this->data[0]["P"];

    // $edital = $dadosConcursoPublicoEfetivo->children($this->namespace["cpe"])->edital ?? null;
    // $edital->children($this->namespace["cpe"])->dataPublicacaoEdital = $this->data[0]["Q"];
    // $edital->children($this->namespace["cpe"])->meioPublicacaoEdital = $this->data[0]["R"];



    // // Lista de cargos do XML
    // $listaCargos = $this->xml->children($this->namespace['cpe'])-> ListaCargos ?? null;
    // $cargo = $listaCargos->children($this->namespace['cpe'])-> Cargo ?? null;
    // $cargoEdital = $cargo->children($this->namespace['cpe'])-> CargoEdital ?? null;
 
    // $entidadePrevista = $cargoEdital->children($this->namespace['ap'])-> EntidadePrevista;
    // $entidadePrevista->children($this->namespace['ap'])->CodigoEntidadePrevista = $this->data[0]["S"];
    // $entidadePrevista->children($this->namespace['ap'])->CodigoMunicipioEntidadePrevista = $this->data[0]["T"];

    // $cargoEdital->children($this->namespace['ap'])-> codigoCargo = $this->data[0]["U"];

    // $cargo->children($this->namespace['cpe'])->permiteAtribPontoTitulo = $this->data[0]["V"];
    // $cargo->children($this->namespace['cpe'])->numVagasCargoFuncao = $this->data[0]["W"];
   
    // Classificados do XML
    $classificacao = $this->xml->children($this->namespace['lcl'])-> Classificacao ?? null;
    $dadosClassificacao = $classificacao->children($this->namespace['lcl'])-> DadosClassificacao ?? null;
    $cargoFuncaoEdital = $dadosClassificacao->children($this->namespace['lcl'])-> CargoFuncaoEdital ?? null;
    $entidadePrevista = $cargoFuncaoEdital->children($this->namespace['ap'])-> EntidadePrevista ?? null;
    $entidadePrevista->children($this->namespace['ap'])-> CodigoEntidadePrevista = $this->data[0]["I"];
    $entidadePrevista->children($this->namespace["ap"])-> CodigoMunicipioEntidadePrevista = $this->data[0]["J"];

    $cargoFuncaoEdital->children($this->namespace['ap'])-> codigoFuncao = $this->data[0]["K"];

    $dadosClassificacao->children($this->namespace['lcl'])-> dataPublicacaoListaClassificacao = $this->data[0]["L"];
    $dadosClassificacao->children($this->namespace['lcl'])-> dataAtoHomologacaoConcurso = $this->data[0]["M"];
    $dadosClassificacao->children($this->namespace['lcl'])-> dataValidadeInicial = $this->data[0]["N"];
    $dadosClassificacao->children($this->namespace['lcl'])-> dataPublicacaoHomologacao = $this->data[0]["O"];
    
    $cpfResponsavelHomologacao = $dadosClassificacao->children($this->namespace['lcl'])-> cpfResponsavelHomologacao ?? null;
    $cpfResponsavelHomologacao->children($this->namespace['gen'])-> Numero = $this->data[0]["Q"];
    $dadosClassificacao->children($this->namespace['lcl'])-> codigoCargoResponsavelHomologacao = $this->data[0]["R"];
    // $dadosClassificacao->children($this->namespace['lcl'])-> codigoFuncaoResponsavelHomologacao = $this->data[0]["AH"];

    $classificados = $classificacao->children($this->namespace['lcl'])-> Classificados ?? null;

   
    // Adiciona os classificados do array ao XML
    for ($i = 0; $i < count($data); $i++) {
        $novo = $classificados->addChild('lcl:Classificado', null, $this->namespace['lcl']);
        $cpf = $novo->addChild('lcl:cpfClassificado', null, $this->namespace['lcl']);
        
        // Adiciona o atributo Tipo="02" no nó cpfClassificado
        $cpfDom = dom_import_simplexml($cpf);
        $cpfDom->setAttribute('Tipo', '02');

        $cpf->addChild('gen:Numero', $data[$i]["T"], $this->namespace['gen']);

        $novo->addChild('lcl:nomeClassificado', $data[$i]["U"], $this->namespace['lcl']);
        $novo->addChild('lcl:ordemClassificacao', $data[$i]["V"], $this->namespace['lcl']);
    }


    // // Dados do prazo de prorrogação do XML
    // $prazoProrrogacao = $this->xml->children($this->namespace['cpe'])-> PrazoProrrogacao ?? null;
    // $dtPrazoProrrogValidade = $prazoProrrogacao->children($this->namespace['pro'])-> dtPrazoProrrogValidade ?? null;
    // $dtPrazoProrrogValidade->children($this->namespace['ap'])-> anoPrazo = $this->data[0]["N"];
    // $dtPrazoProrrogValidade->children($this->namespace['ap'])-> mesPrazo = $this->data[0]["O"];
    // $dtPrazoProrrogValidade->children($this->namespace['ap'])-> diaPrazo = $this->data[0]["P"];


    

    // Salva o XML modificado temporariamente em string
    $xmlString = $this->xml->asXML();

    // Carrega a string XML no DOM para aplicar a formatação (identação)
    $dom->loadXML($xmlString);

    // Salva o XML formatado no arquivo desejado
    $dom->save(base_path("storage/xml/xmlConverted.xml"));

    dd('XML gerado com sucesso');
}

    
}