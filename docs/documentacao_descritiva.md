# Documentação - Conversor XML

## Contexto

Este documento foi construído conforme o entendimento do funcionamento do código e suas modificações ao longo do desenvolvimento. A ideia aqui não é apenas explicar o que o sistema faz, mas também registrar o raciocínio por trás das decisões tomadas.

---

## 1 - Controller `ConversorController`

Na função `processa`, o sistema recebe dois arquivos:

* Um XML base
* Uma planilha Excel

### Upload dos arquivos

Inicialmente, o XML enviado com o name `xml_base` é capturado e armazenado na pasta:

```
storage/app/temp_uploads
```

Esse armazenamento respeita o disco configurado em `config/filesystem.php`.

O mesmo processo é feito para a planilha.

O retorno dessa etapa é o **caminho relativo** dos arquivos armazenados.

---

### Caminho absoluto

Ainda dentro da função, utilizo `storage_path` para obter o caminho absoluto desses arquivos.

Esse caminho relativo é então passado para a função:

```
lerComCabecalho
```

do service `LoadFileService`.

---

## 2 - Service `LoadFileService`

Aqui começa o processamento da planilha.

### Leitura do arquivo

Utilizo a classe `IOFactory` do PhpSpreadsheet, que é responsável por lidar com diferentes formatos de planilha.

O método `load` identifica automaticamente a extensão do arquivo e escolhe o leitor adequado.

---

### Conversão da planilha

Com:

```
getSheet(0)
```

capturo a primeira aba da planilha.

Depois disso, converto o conteúdo para array utilizando parâmetros específicos:

* `nullValue = null`
  → células vazias viram null

* `calculateFormulas = true`
  → retorna o valor da fórmula, não a fórmula

* `formatData = true`
  → mantém formatação original (datas e números)

* `returnRef = false`
  → não usa letras de coluna como índice

---

### Estrutura dos dados

O resultado é um **array de arrays**, onde cada item representa uma linha da planilha.

---

### Tratamento do cabeçalho

A primeira linha (`linha[0]`) é armazenada como cabeçalho.

Depois disso, inicio um `foreach` nas demais linhas.

---

### Remoção de linhas vazias

Uso `array_filter` para verificar se a linha possui algum valor.

Se todos os valores forem nulos ou vazios, a linha é ignorada.

---

### Ajuste de tamanho das linhas

Aqui entram duas funções importantes:

#### `array_pad`

Serve para preencher células quando a linha tem menos colunas que o cabeçalho.

* Se faltar valor → completa com `null`
* Se estiver igual ou maior → não altera

---

#### `array_slice`

Garante que a quantidade de elementos da linha seja igual ao cabeçalho.

Se tiver valores excedentes, eles são descartados.

---

### Resultado final

Ao final do processo, combino cabeçalho + linha e gero um array associativo (tipo dicionário):

```
[
  "nome" => "João",
  "cpf" => "123..."
]
```

---

## 3 - Geração do XML de saída

De volta ao controller, começa a segunda fase.

O XML gerado terá o nome:

```
xmlConverted_data(Ymd_His).xml
```

E será armazenado em:

```
storage/xml
```

---

### Criação do diretório

Existe uma verificação:

* Se o diretório não existir → ele é criado
* Permissão: `0755`

Ou seja:

* Dono: total acesso
* Outros: leitura e execução

---

## 4 - Service `XmlGeneratorDinamicoService`

### Método `carregarBase`

Aqui começa o processamento do XML base.

---

### Leitura do arquivo

Uso `file_get_contents` para ler o XML como string.

Faço isso para conseguir analisar elementos como encoding.

---

### Verificação de encoding

Utilizo regex para identificar se o XML possui declaração de encoding.

Se encontrado, armazeno o valor e padronizo para maiúsculo.

---

### Conversão de encoding

Se o encoding não for `UTF-8`, faço a conversão com:

```
mb_convert_encoding
```

Parâmetros:

* conteúdo
* encoding destino
* encoding origem

---

### Ajuste da declaração

Uso `preg_replace` para garantir que o XML declare:

```
encoding="UTF-8"
```

---

### Conversão para objeto

Uso:

```
simplexml_load_string
```

Com a flag:

```
LIBXML_NOCDATA
```

Isso transforma CDATA em texto normal, evitando problemas de leitura.

---

## Namespaces

Armazeno todos os namespaces com:

```
getNamespaces(true)
```

Isso é essencial, pois sem namespace não é possível acessar corretamente as tags.

Entendi que namespace funciona como um “rótulo” que identifica a qual grupo aquela tag pertence, evitando conflitos.

---

## Mapeamento de nós

Antes de iniciar o mapeamento, limpo o array:

```
mapaDeNos = []
```

Para evitar mistura de execuções anteriores.

---

### Função `varrerNosTerminais`

Essa função é recursiva e percorre toda a árvore do XML.

---

### Lógica da função

* Cria um array com todos os namespaces + vazio
* Itera sobre cada namespace
* Captura filhos com `children()`

Se houver filhos:
→ continua recursivamente

Se não houver filhos:
→ é um nó terminal (folha)

---

### Armazenamento

Se o nó não tem filhos:

* Armazena:

  * nome da tag
  * objeto SimpleXML

Se houver nomes repetidos:
→ ajusta para evitar sobrescrita

---

## 5 - Geração do XML (método `gerar`)

Recebe:

* Dados da planilha
* Caminho de saída

---

### Separação de campos

Divido os campos em:

#### Campos fixos

* Sem prefixo `[loop]`

#### Campos repetidos

* Com prefixo `[loop]`

---

### Preenchimento de campos fixos

Para cada campo:

* Verifico se existe no mapa de nós
* Se existir:

  ```
  $n[0] = valor
  ```

---

### Formatação de valores

#### `formatarValor`

* Detecta tipo da tag:

  * data
  * cpf

#### `formatarData`

* Trata datas vindas do Excel (serial)

* Converte para formato:

  ```
  Y-m-d
  ```

* Também aceita formatos como:

  * dd/mm/yyyy

---

## Campos repetidos

### Método `preencherCamposRepetidos`

Aqui está a parte mais complexa.

---

### Objetivo

Identificar e reconstruir blocos repetidos no XML com base nos dados da planilha.

---

### Processo inicial

* Identifica:

  * container
  * bloco repetido
  * namespace

Uso:

```
removerPrefixoLoop
```

para limpar os nomes.

---

### Conversão para DOM

Converto SimpleXML → DOM

Motivo:

* DOM facilita subir na árvore (pai, avô, etc.)

---

### Identificação da estrutura

Capturo:

* tag pai
* tag avô (bloco repetido)
* container

---

### Limpeza

Removo todos os filhos do container para reconstruir do zero.

---

### Reconstrução

* Converto de volta para SimpleXML
* Busco prefixo de namespace
* Recrio o bloco base

---

### Inserção dos dados

Para cada linha da planilha:

* Verifico se existe algum valor válido (`temDado`)
* Só cria bloco se houver dados

---

### Estrutura interna

Uso:

* cache de blocos intermediários
* controle para evitar duplicação

---

### Inserção final

* Remove `[loop]`
* Localiza nó correspondente
* Monta estrutura respeitando:

  * hierarquia
  * namespace

---

## 6 - Finalização

Após montar o XML:

* Remove espaços desnecessários
* Aplica indentação
* Converte para string
* Salva no caminho definido

---

## 7 - Retorno ao controller

O arquivo final é disponibilizado para download.

---

## 8 - Como usar

1. Subir XML base (AUDESP)

   * Não usar modelos preenchidos

2. Subir planilha Excel

3. Garantir que:

* Cabeçalho = tags do XML

---

### Ajustes obrigatórios

Remover prefixos:

```
ns1:, ns2:, ...
```

---

### Campos repetidos

Usar prefixo `[loop]`

Exemplo:

```
[loop]nome
[loop]cpf
[loop]telefone
```

---

## Considerações finais

O sistema foi construído de forma dinâmica:

* Não depende de estrutura fixa
* Se adapta ao XML base
* Evita hardcode
* Permite reutilização para diferentes layouts

Este documento reflete tanto a implementação quanto o entendimento adquirido durante o desenvolvimento.
