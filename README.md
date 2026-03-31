# Conversor XML com Excel

## Sobre

Este projeto converte um XML base utilizando dados de uma planilha Excel, gerando um novo XML estruturado automaticamente.

---

## Funcionalidades

* Upload de XML base
* Upload de planilha Excel
* Leitura automática de dados
* Mapeamento dinâmico de tags XML
* Suporte a:

  * Campos fixos
  * Campos repetidos (`[loop]`)
* Geração de XML final para download

---

## Como funciona (resumo)

1. Arquivos são enviados (XML + Excel)
2. Planilha é convertida em array estruturado
3. XML base é carregado e normalizado (UTF-8)
4. Nós do XML são mapeados
5. Dados são inseridos dinamicamente
6. Novo XML é gerado

---

## Campos repetidos

Use o prefixo `[loop]` no cabeçalho da planilha:

```
[loop]nome
[loop]cpf
[loop]telefone
```

---

## Regras importantes

1. Subir XML base

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

## Documentação completa

Acesse o documento detalhado:

👉 `docs/documentacao-descritiva.md`

---

## Tecnologias

* PHP
* Laravel
* PhpSpreadsheet
* SimpleXML
* DOM

---

## Observação

O sistema foi desenvolvido para ser flexível e adaptável a diferentes estruturas XML sem necessidade de alterações no código.
