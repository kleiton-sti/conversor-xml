<?

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\LoadFileService;

class ConversorController extends Controller
{
    public array $data;
    public $xml;

    public function __construct() {

        //Carrega dados da planilha
        $this->data = (new LoadFileService())->reader();

        //carrega padrão AUDESP XML
        $this->xml = simplexml_load_file("conversor_xml/resources/views/xml/xmlPattern.xml");
    }
    
    //acessa o nó onde adicionar os dados
    public $listaClassificados = $this->xml->DadosClassificacao->Classificados->children();

    //adiciona os dados
    public function addClassificados($listaClassificados, $data) {
        
        foreach ($listaClassificados as $classificado) {
            
            $classificado->cpfClassificado.$data["S"];
            $classificado->nomeClassificado.$data["T"];
            $classificado->ordemClassificacao.$data["U"];
        } 

        for($i = 1; $i < count($data); $i++) {
            $this->listaClassificados->addChild("Classificado")->addChild("cpfClassificado", $data[$i]["S"]);
            $this->listaClassificados->addChild("Classificado")->addChild("nomeClassificado", $data[$i]["T"]);
            $this->listaClassificados->addChild("Classificado")->addChild("ordemClassificacao", $data[$i]["U"]);
        }

        $this->xml->asXML(storage_path("conversor_xml/resources/views/xml/xmlConverted.xml"));
        dd($this->xml);
    }
    
}