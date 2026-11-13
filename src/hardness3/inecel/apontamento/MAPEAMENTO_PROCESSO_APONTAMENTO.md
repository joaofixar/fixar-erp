# Mapeamento do processo atual de apontamento

## Entrada do processo

- Abertura da janela atual acontece no grid customizado `9b8d034de74ca129a436691678201069`.
- Arquivo: `ide/personalizacaoGrid/9b8d034de74ca129a436691678201069-js`
- Funcao encontrada:

```js
function lancarApontamento(T256_Id, produto, refresh_Id) {
    abrirJanela(false, '[[divIdRoot]]', '[[divId]]', unique(), '', 'Lancar Apontamentos', '/sistema/personalizacao/content/5596e8ebdffbc20bd3ce68675d2b64cc/', '&T256_Id=' + encodeURIComponent(T256_Id) + '&produto=' + encodeURIComponent(produto) + '&refresh_Id=' + encodeURIComponent(refresh_Id), [300,200]);
}
```

## Tela aberta pela URL informada

- URL atual: `/sistema/personalizacao/content/5596e8ebdffbc20bd3ce68675d2b64cc/`
- Metadata localizada em:
  - `ide/personalizacaoContent/contents/5596e8ebdffbc20bd3ce68675d2b64cc`
- Nome da personalizacao:
  - `apontamento detalhado op`

## Formulario usado na tela

- Formulario localizado em:
  - `ide/personalizacaoForm/e6bf403b984d0333cf12c5d303292cdb`
  - `ide/personalizacaoForm/forms/8fb5e8d7769bb20b78b809d0fbc16ee0`
- Nome do formulario:
  - `apontamento producao`

### Campos exibidos

- `idT256`
  - hidden
  - valor vindo de `r_T256_Id`
- `produto`
  - somente leitura
  - valor vindo de `r_produto`
- `pesoPallet`
- `QtdeChapa`
- `qtdeApontar`
- `pedido`
- `T257A_Observacao`
- `enviar`

### Layout atual do formulario

- O editor visual mostra todos os campos em uma unica linha principal.
- Arquivo de layout:
  - `ide/personalizacaoForm/formEditorData-sistema-personalizacao-form-8fb5e8d7769bb20b78b809d0fbc16ee0-geral`

## Acao ao enviar

- O envio do formulario faz AJAX JSONP para:
  - `/sistema/personalizacao/outro/9d2b823565df5c83e61145a23931ecdc/`
- Metadata do callback:
  - `ide/personalizacaoOutro/outros/9d2b823565df5c83e61145a23931ecdc`
- Esse metadata aponta para o codigo:
  - `ide/personalizacaoOutro/06331faffe0be5ac552601c21cc8e453`

## Regra de negocio do lancamento

Arquivo:
- `ide/personalizacaoOutro/06331faffe0be5ac552601c21cc8e453`

### Parametros recebidos

- `T256_Id`
- `qtdeApontar`
- `pesoPallet`
- `T005_Id`
- `QtdeChapa`
- `T257A_Observacao`

### Fluxo executado

1. Inicia transacao.
2. Busca dados da OP e tipo de controle do produto a partir de `T256`.
3. Busca a ultima sequencia usada em `T257A` para a OP.
4. Monta o lote:
   - se `D001A_Tipo_Controle == 'D'`, usa `T255_Id - Sequencia`
   - senao, grava lote em branco
5. Insere um apontamento em `T257` com:
   - item da OP (`T257_T256_Id`)
   - usuario logado (`T257_C007_Id`)
   - data atual
   - quantidade apontada
6. Insere detalhes em `T257A` com:
   - sequencia
   - lote
   - peso
   - pedido cliente
   - quantidade de chapas
   - observacao
7. Atualiza `T256`:
   - `T256_Quantidade`
   - `T256_Quantidade_Produzida`
   - ambas viram a soma de `T257_Quantidade` do item
8. Atualiza `T255`:
   - `T255_Quantidade`
   - `T255_Quantidade_Produzida`
   - ambas viram a soma das quantidades dos itens `T256` da OP
9. Faz `COMMIT`.
10. Em caso de erro, faz `ROLLBACK` e retorna erro em JSONP.

## Comportamento apos salvar

- O callback do formulario fecha/atualiza pela div atual via:
  - `divRefresh(id, true);`
- O parametro `refresh_Id` chega na abertura, mas no fluxo atual ele nao esta sendo efetivamente usado no callback final de insercao.

## Fluxo complementar de edicao

- Existe um grid complementar chamado `apontamento detalhado op`.
- Metadata do grid:
  - `ide/personalizacaoGrid/grids/50e190305e30c2e0429b3ecc82435fba`
- JS do grid:
  - `ide/personalizacaoGrid/bdaf24435d2a4de25cd4e6d26c1f8a32-js`
- Esse grid abre a tela:
  - `/sistema/personalizacao/form/215e7edf47a52a80656f026f310d9114/`
- Campos de edicao identificados:
  - `T257_Quantidade`
  - `T257A_Peso`
  - `T257A_T005_Id`
  - `QtdeChapa`
  - `T257A_Observacao`

## Entidades envolvidas

- `T255`
  - cabecalho da OP
- `T256`
  - item da OP
- `T257`
  - apontamento principal
- `T257A`
  - detalhes adicionais do apontamento
- `D001A`
  - tipo de controle usado para montar lote

## Observacoes importantes para a nova tela

- A nova tela em `apontamento` pode reproduzir esse fluxo sem depender da janela legacy.
- O minimo necessario para a primeira versao funcional e:
  - listar itens `T256`
  - abrir formulario de apontamento
  - gravar em `T257` e `T257A`
  - recalcular `T256` e `T255`
  - listar historico de apontamentos do item
- Vale revisar a nomenclatura dos campos:
  - `Quantide Apontar` no form atual tem erro de digitacao
  - `pedido` pode virar `Pedido do cliente`
- Tambem vale definir se a nova tela deve:
  - apenas lancar apontamentos
  - ou ja incluir edicao e historico na mesma pagina
