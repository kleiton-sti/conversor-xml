# Documentação - Conversor XML

## Visão Geral

Este projeto tem como objetivo converter um XML base utilizando dados provenientes de uma planilha Excel, gerando um novo XML estruturado para download.

---

## 1. Controller - `ConversorController`

A função `processa`:

* Recebe:

  * XML base (`xml_base`)
  * Planilha Excel

### Upload dos arquivos

* Ambos os arquivos são armazenados em:

  * `storage/app/temp_uploads`
* Retorna:

  * Caminho relativo dos arquivos

### Caminho absoluto

* Utiliza `storage_path` para obter o caminho absoluto
* O caminho relativo é enviado para:

  * `LoadFileService::lerComCabecalho`

---

## 2. Service - `LoadFileService`

Responsável por ler e transformar a planilha.

### Leitura do arquivo

* Utiliza `IOFactory` do **PhpSpreadsheet**
* Método `load()` detecta automaticamente o tipo do arquivo

### Processamento da planilha

* Captura a primeira aba:

  * `getSheet(0)`
* Converte para array com:

  * `nullValue = null`
  * `calculateFormulas = true`
  * `formatData = true`
  * `returnRef = false`

### Tratamento dos dados

1. Cabeçalho:

   * Primeira linha (`linha[0]`)

2. Iteração das linhas:

   * Remove linhas vazias (`array_filter`)
   * Preenche valores faltantes (`array_pad`)
   * Remove excedentes (`array_slice`)

3. Resultado final:

   * Array associativo (tipo dicionário)
   * Chave = cabeçalho
   * Valor = célula correspondente

---

## 3. Geração do XML de saída

* Nome:

  ```
  xmlConverted_data(Ymd_His).xml
  ```

* Diretório:

  * `storage/xml`

### Verificação do diretório

* Se não existir:

  * Cria com permissão `0755`

---

## 4. Service - `XmlGeneratorDinamicoService`

### Método `carregarBase`

#### Etapas:

1. Leitura do XML:

   * `file_get_contents`

2. Verificação de encoding:

   * Regex para identificar encoding
   * Padronização para **UTF-8**

3. Conversão de encoding:

   * `mb_convert_encoding`

4. Ajuste da declaração:

   * `preg_replace`

5. Conversão para objeto:

   * `simplexml_load_string`
   * Utiliza `LIBXML_NOCDATA`

---

### Namespaces

* Capturados com:

  ```
  getNamespaces(true)
  ```

* Importante para evitar conflitos entre tags

---

### Mapeamento de nós

#### Função: `varrerNosTerminais`

* Função recursiva
* Percorre toda a árvore XML

#### Objetivo:

* Capturar apenas **nós folha (sem filhos)**

#### Regras:

* Considera namespaces e ausência deles
* Evita sobrescrever nós repetidos

---

## 5. Geração do XML

### Método `gerar`

Recebe:

* Dados da planilha
* Caminho de saída

---

### Separação de campos

* `obterCamposFixos`
* `obterCamposRepetidos`

#### Campos fixos:

* Sem prefixo `[loop]`

#### Campos repetidos:

* Com prefixo `[loop]`

---

### Preenchimento de campos fixos

* Verifica se a tag existe no XML
* Define valor via:

  ```
  $n[0] = valor
  ```

### Formatação

#### `formatarValor(nomeTag, valor)`

* Detecta:

  * Datas
  * CPF

#### `formatarData(valor)`

* Converte:

  * Excel serial → `Y-m-d`
  * Outros formatos → normalização

---

## 6. Campos Repetidos

### Método `preencherCamposRepetidos`

#### Processo:

1. Identifica:

   * Container
   * Bloco repetido
   * Namespace

2. Remove conteúdo antigo

3. Reconstrói estrutura

4. Itera sobre linhas da planilha

5. Só cria blocos com dados válidos

---

### Estrutura interna

* Uso de:

  * DOM (para navegação)
  * SimpleXML (para escrita)

* Controle de blocos intermediários:

  * Cache para evitar duplicações

---

## 7. Finalização do XML

* Remove espaços desnecessários
* Aplica indentação
* Converte para string
* Salva no caminho definido

---

## 8. Fluxo final

1. Upload arquivos
2. Leitura da planilha
3. Leitura e normalização do XML
4. Mapeamento de nós
5. Inserção de dados
6. Geração do XML final
7. Retorno para download

---

## 9. Como usar

1. Subir XML base (AUDESP)

   * Não usar modelos preenchidos

2. Subir planilha Excel

3. Garantir que:

* Cabeçalho = nomes das tags XML

4. Ajustes necessários:

* Remover prefixos:

  ```
  ns1:, ns2:, ...
  ```

5. Campos repetidos:

* Usar prefixo `[loop]`

Exemplo:

```
[loop]nome
[loop]cpf
[loop]telefone
```

---

## Considerações finais

* O sistema é altamente dinâmico
* Baseado na estrutura do XML
* Evita hardcode de tags
* Permite reaproveitamento para diferentes layouts XML
