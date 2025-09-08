<?

namespace Conversor_xml\App\Services;

use App\Http\Controllers\LoadFile;

class ConvertToXmlService extends LoadFile
{
    //carregia o xml
    public $xml = simplexml_load_file("conversor_xml/resources/views/xml/xmlPattern.xml");

    //acessa o nó onde você quer adicionar os dados
    public $listaClassificados = $xml->DadosClassificacao->Classificados;

    //adiciona os dados
    public function addClassificados($Classificados) {
        
        foreach ($data as $element) {
            $novaClassificacao = $this->xml->DadosClassificacao->Classificados->addChild('Classificado');
            $novaClassificacao->addChild('cpfClassificado', $element["S"]['cpfClassificado']);
            $novaClassificacao->addChild('nomeClassificado', $element["T"]['nomeClassificado']);
            $novaClassificacao->addChild('ordemClassificacao', $element["U"]['ordemClassificacao']);
        } 

        $this->xml->asXML(storage_path("conversor_xml/resources/views/xml/xmlConverted.xml"));
    }
    
   

}