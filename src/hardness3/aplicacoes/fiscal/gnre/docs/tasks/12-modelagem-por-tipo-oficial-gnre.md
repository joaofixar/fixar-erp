# Task 12 — Modelagem por tipo oficial de GNRE

## Resultado e escopo

Análise concluída em 29/10/2026, limitada a documentação e modelagem técnica.
Não houve implementação de interface, PHP funcional, banco, XML, tabelas,
migrations, guias, transmissão, consulta, fila nem chamada ao Portal.

Fontes examinadas:

- Manual de Integração de Contribuintes GNRE v2.17, seção 4.3, Quadro II
  (retornos 273 a 304) e leiaute de `TConfigUf` (páginas 27--29 no PDF);
- `dados_gnre_v2.00.xsd`, `lote_gnre_v2.00.xsd` e `config_uf_v1.00.xsd`;
- `src/ConfigUfRules.php`, `src/XmlGenerator.php`, `GNRE001`, `GNRE002` e
  `GNRE003` em `scripts/atualizacaoBase.php`.

## Fatos confirmados

### Tipos oficiais e habilitação

`dados_gnre_v2.00.xsd` documenta e restringe `TDadosGNRE/tipoGnre` a:

| Código | Nome oficial no XSD | Regras explícitas adicionais do Manual |
| --- | --- | --- |
| `0` | GNRE Simples | retorno 273: não pode ter mais de um `item`. |
| `1` | GNRE Múltiplos Documentos de Origem | retornos 274--276: os itens não podem ter receitas diferentes, tipos de documento diferentes, nem documentos de origem iguais. O retorno 294 ainda veda mais de um pagamento com mesmo tipo/número de documento e receita. |
| `2` | GNRE Múltiplas Receitas | retornos 277--278: não pode repetir a mesma receita com o mesmo documento de origem, nem a mesma receita com o mesmo período de referência. |

Os códigos não foram inferidos pelo nome da interface: são as três enumerações
do XSD e a semântica acima está confirmada pelos retornos do Manual.

`TConfigUf/tiposGnreDaUF/tipoGnre` é a lista habilitada para a UF e
`receita/tiposGnre/tipoGnre` é a lista habilitada para aquela receita. Como o
Manual qualifica ambas como “Tipos de GNRE habilitados”, a regra segura é:

```text
tipos selecionáveis da combinação UF + receitas da guia
= tiposGnreDaUF ∩ interseção(tiposGnre de cada receita já usada)
```

Antes de haver itens, a tela mostra `tiposGnreDaUF ∩ tiposGnre` da primeira
receita escolhida. Ao incluir, trocar ou remover uma linha, recalcula a
interseção com todas as receitas existentes. Se ela ficar vazia, a alteração é
inválida e não deve ser gravada. Nunca apresentar tipo que não pertença às duas
listas aplicáveis; ausência de configuração, versão XML `2.00` não habilitada
ou campo/regra desconhecido deve bloquear a validação, não virar regra por UF.

O contrato permite consultar `TConfigUf` com `tiposGnre=S`; essa é a consulta
que deve abastecer a decisão. Os exemplos AL/AP são apenas cenários de UX e não
constituem uma tabela de regras estaduais no código.

### Hierarquia efetiva do XML v2.00

`TDadosGNRE` é uma guia do lote. Seus campos globais são `ufFavorecida`,
`tipoGnre`, `contribuinteEmitente` (quando configurado), `itensGNRE`,
`valorGNRE`, `dataPagamento` e `identificadorGuia`. Portanto emitente e
pagamento são da guia, não de uma linha. `valorGNRE` também é da guia e o
Manual (retorno 290) exige que ele confira com o somatório dos valores dos
itens.

Cada `item` contém receita, detalhamento, documento de origem, produto,
referência, `dataVencimento`, até dez elementos `valor`, convênio,
destinatário, campos extras e controles. O documento de origem é um único
elemento no item (`documentoOrigem` com atributo obrigatório `tipo`), contendo
somente sua identificação. O XSD não atribui a ele vencimento nem valores.
Assim, um documento persistido é referência reutilizável; o vencimento e os
valores são da linha de recolhimento que o referencia.

FCP também é por item, representado nos valores: `12/22` são principal/total
Fundo de Pobreza, e `32/42/52` multa/juros/atualização do Fundo. Os retornos
279--287 confirmam os pares exigidos quando aplicável. Convênio, destinatário
e campos adicionais também pertencem ao item. A obrigatoriedade e listas vêm
da receita em `TConfigUf`: inclusive documento, vencimento, pagamento,
convênio, FCP, emitente, destinatário, adicionais e tipos de documento.

## Matriz por tipo

| Aspecto | `0` Simples | `1` Múltiplos documentos de origem | `2` Múltiplas receitas |
| --- | --- | --- | --- |
| Itens XML | Exatamente 1. | Mais de um, sujeitos às regras 274--276. | Mais de um, sujeitos às regras 277--278. |
| Receita | Uma receita no único item. | A mesma receita em todos os itens. | Linhas de receita/recolhimento; não se pode repetir receita no mesmo documento nem na mesma referência. |
| Documento de origem | Conforme exigência da receita; no máximo um no item. | Cada item representa seu documento; documentos distintos e mesmo tipo documental. | Conforme a receita de cada item; unicidade receita+documento/referência. |
| Valores e vencimento | Da única linha. | De cada item; não pertencem ao cadastro do documento. | De cada item; não pertencem ao cadastro do documento. |
| Total da guia | Soma dos `valor` de seu único item. | Soma de todos os valores de todos os itens. | Soma de todos os valores de todos os itens. |
| Globais | UF, tipo, emitente, pagamento, identificador e total. | Idem. | Idem. |

O Manual/XSD não diz que o tipo `2` dispense documento ou referência: isso
continua condicionado à regra da receita. Tampouco se deve concluir só pelo
nome que toda guia do tipo `2` precisa de receitas numericamente distintas;
as restrições documentadas são as dos retornos 277--278. A UX deve aplicar
somente essas restrições e a configuração retornada.

## Diagnóstico do modelo atual

`GNRE001` é um cabeçalho operacional útil (empresa, UF, status, auditoria),
mas contém receita/detalhamento, vencimento, valor e referência “padrão”.
Esses quatro últimos têm nível de item no XSD; não podem ser fonte única de
verdade em uma guia de tipos `1` e `2`. `GNRE002` armazena identificação e
descrição livre do documento; é compatível como catálogo/vínculo de documento,
mas não deve receber valor ou vencimento. `GNRE003` já representa bastante do
`item`, com vínculo opcional para documento, receita, referência, um valor,
destinatário e extras.

`XmlGenerator` emite corretamente quase todos esses dados como item, mas:

- calcula `tipoGnre` por contagem/vínculos (`1` quando houver mais de um
  documento, senão `2` para vários itens), em vez de persistir/selecionar o
  tipo permitido e validar suas regras oficiais;
- põe `GNRE001_Data_Vencimento` em **todos** os itens, embora
  `dataVencimento` esteja no item;
- limita cada item a um `GNRE003_Valor`, embora o XSD aceite até 10 valores e
  FCP pode exigir pares de valores;
- não emite `convenio` nem `dataPagamento`; a primeira é item e a segunda é
  guia;
- não valida as regras de combinação dos retornos 273--278/294, nem os limites
  `qtdMaximas` devolvidos pela configuração;
- trata o tipo efetivo como derivado mesmo após `ConfigUfRules` ler
  `uf_types` e `receipt.types`. Hoje ela só verifica a lista da UF, não a
  interseção com `tiposGnre` da(s) receita(s).

### Lacunas e alteração mínima futura

| Dado/regra | Nível correto e evidência | Situação/campo atual | Próxima alteração mínima (não executada) | Rascunhos e efeito XML/validação |
| --- | --- | --- | --- | --- |
| Tipo oficial escolhido | Guia; `TDadosGNRE/tipoGnre` e `TConfigUf/tiposGnre*`. | Não há coluna; é inferido de `GNRE003`. | Novo `GNRE001_Tipo_GNRE` nullable inicialmente. | Preservar rascunhos: derivar apenas para exibição/migração assistida e exigir escolha explícita na próxima edição. XML usa o valor persistido; valida interseção UF/receitas e matriz. |
| Receita/detalhamento padrão do cabeçalho | Item; `dados_gnre_v2.00.xsd/item`. | `GNRE001_Codigo_Receita` e detalhamento duplicam `GNRE003`. | Manter como resumo legado, não fonte do XML; futuramente tornar resumo derivado da primeira linha ou “múltiplas”. | Não apagar nem reinterpretar rascunhos. XML continua somente de `GNRE003`; valida duplicidade para evitar divergência silenciosa. |
| Vencimento | Item; `item/dataVencimento`. | Só `GNRE001_Data_Vencimento`; generator replica. | Novo `GNRE003_Data_Vencimento` nullable, preenchido inicialmente do cabeçalho para compatibilidade. | Todo rascunho atual mantém o mesmo resultado. XML passa a emitir por item; validar obrigatoriedade por receita e coerência de pagamento. |
| Valores tributários e FCP | Item; até 10 `item/valor`; tipos 11--52 e retornos 279--287. | Um par `GNRE003_Tipo_Valor`/`GNRE003_Valor`. | Nova estrutura filha de valor (por exemplo `GNRE006`, com item, tipo, valor) ou representação equivalente normalizada. | Manter o valor atual como uma linha migrada. Total passa a somar todas as linhas de valor; validar `valorExigido`, pares FCP e `valorGNRE`. |
| Convênio | Item; `item/convenio`; `exigeConvenio`. | Ausente. | Novo campo de item, por exemplo `GNRE003_Convenio`. | Nulo para rascunhos. XML somente quando informado/exigido; distinguir os significados documentados N/não exigido, S/opcional, O/obrigatório. |
| Data de pagamento | Guia; `TDadosGNRE/dataPagamento`; `exigeDataPagamento`. | Ausente; a validação hoje falha fechada. | Novo `GNRE001_Data_Pagamento` nullable. | Nulo preserva rascunhos; XML emite uma vez após `valorGNRE`; validar contra o menor vencimento conforme retorno 293. |
| Emitente | Guia; `TDadosGNRE/contribuinteEmitente`. | Derivado do cadastro ERP por `CompanyEmitter`, sem cópia em GNRE. | Sem nova coluna necessária enquanto o contrato permanecer derivado; registrar no XML/auditoria da transmissão já existente. | Preserva rascunhos. Validar para todas as receitas usadas, não somente a primeira. |
| Destinatário, produto, referência, extras | Item; XSD e regras por receita. | `GNRE003` já comporta; extras em JSON. | Sem campo obrigatório novo para o contrato atual; considerar normalização de extras só se surgir requisito de consulta. | Preservar formato atual. Validar a regra da receita de cada linha e limite/forma do XSD. |
| Documento de origem e tipo | Item no XML; identificação persiste em `GNRE002`. | `GNRE002` + FK lógica opcional em `GNRE003`, tipo em item. | Sem novo campo para identificação/tipo; adicionar validação de unicidade/mesmo tipo conforme tipo 1/2. | Nunca dar ao documento valor/vencimento. XML segue emitindo o par a partir do item vinculado. |
| Limites oficiais | Guia/lote; `TConfigUf/qtdMaximas`. | Lidos em `rules.max`, não aplicados. | Sem banco novo. | Validar itens por guia antes de XML e totalizar pelo modelo de valores. |

`GNRE001_Modalidade` (`INDIVIDUAL`/`APURACAO`) é classificação interna
anterior e não substitui `tipoGnre` oficial. Ela não aparece no XSD. Mantê-la
para compatibilidade até decisão explícita de produto, sem fazê-la decidir o
XML.

## Jornada futura recomendada

Fluxo genérico, sem condicionais codificadas por sigla de UF:

```text
UF -> TConfigUf -> tipo permitido -> emitente -> linhas/documentos conforme tipo
   -> dados globais exigidos -> totalização -> XML local -> transmissão
```

1. Selecionar UF e carregar `TConfigUf` com tipos. Escolher a primeira receita
   para calcular os tipos disponíveis; só então mostrar o seletor de tipo.
2. Informar/revisar emitente global se qualquer receita o permitir/exigir. Não
   duplicá-lo por item. Mostrar pagamento global somente se a configuração de
   alguma receita aplicável o requerer; a regra final deve ser validada por
   linha e consolidada no cabeçalho.
3. **Tipo 0:** mostrar um painel “recolhimento” com uma única receita e, se a
   receita permitir/exigir, um seletor de documento. Não mostrar grade de
   documentos nem ação “adicionar linha”.
4. **Tipo 1:** mostrar primeiro a receita e o tipo de documento comum, depois
   uma grade “documentos e recolhimento”: uma linha por documento, sem repetir
   identificação. Bloquear mudança de receita/tipo documental que divergir da
   primeira linha. Não oferecer uma grade independente que permita documento
   sem item, pois isso induz a duplicação.
5. **Tipo 2:** mostrar uma grade de recolhimentos. Cada linha escolhe sua
   receita e exibe somente seus campos configurados; documento é exibido
   somente se permitido/exigido por aquela receita. Bloquear a combinação
   receita+documento e receita+referência repetida. Não deduzir o tipo depois
   das linhas.
6. Em todos os tipos, o editor de linha contém receita/detalhamento, produto,
   referência, vencimento, valores (inclusive FCP quando configurado),
   convênio, destinatário e extras segundo a receita. O painel global contém
   UF, tipo, emitente, pagamento, identificador e total; nunca receita/valor
   “padrão” editável concorrendo com a linha.
7. Antes do XML, apresentar resumo: tipo selecionado, receitas, documentos
   distintos, vencimento por item, subtotal por item/receita, FCP separado,
   `valorGNRE`, dados globais e pendências bloqueantes. Só habilitar validar
   quando total e regras de tipo/configuração estiverem consistentes.

Para AL, se `TConfigUf` retornar mais de um tipo, o mesmo seletor exibirá a
interseção permitida e habilitará a grade correspondente, inclusive múltiplos
documentos. Para AP, se retornar apenas `0`, só o painel único será possível.
Não há condição `if UF == AL/AP`: a adaptação vem integralmente das listas e
indicadores recebidos em `TConfigUf`.

## Pontos ainda não confirmados

- Não há amostra versionada de `TConfigUf` de AL/AP nesta tarefa; portanto não
  se afirma quais códigos esses estados retornam, além dos cenários fornecidos.
- O Manual/XSD confirmam as restrições citadas, mas não especificam um
  algoritmo de transição quando o usuário troca de tipo com linhas já
  preenchidas. Recomenda-se confirmação explícita e remoção lógica/limpeza de
  dados incompatíveis na futura interface.
- A semântica textual de `exigeConvenio` e `exigeValorFecp` no Manual é
  excepcional (`N` não exigido, `S` opcional, `O` obrigatório), ao passo que
  vários outros indicadores usam S/N/O como sim/não/opcional. A futura task
  deve cobrir isso por testes de fixtures oficiais antes de alterar a regra.
- O XSD autoriza até dez `valor` por item, mas o Manual não detalha nesta
  referência uma composição adicional além dos tipos 11--52. O modelo deve
  preservar a cardinalidade do XSD, sem inventar totais fiscais.

## Riscos de compatibilidade

- Transformar imediatamente campos de cabeçalho em campos de item alteraria
  rascunhos e XMLs historicamente reproduzíveis. As novas colunas devem ser
  nulas/aditivas e a migração deve copiar vencimento/valor antigo para linhas
  apenas sob regra controlada, mantendo o cabeçalho legado como histórico.
- Inferir novamente o tipo para guias antigas pode classificá-las diferente do
  fluxo que as originou. Rascunhos existentes precisam de estado “tipo ainda
  não selecionado” e confirmação ao editar; guias já transmitidas não devem ser
  remodeladas.
- Mudar `GNRE003` de um valor para múltiplos exige atualizar totalização,
  auditoria e geração no mesmo incremento; não é uma alteração isolada de tela.
- A configuração em sessão é dinâmica. A futura validação deve guardar no
  rascunho/auditoria uma referência suficiente à configuração usada para
  explicar bloqueios, sem congelar regras estaduais no código.

## Próximas tasks sugeridas (não iniciadas)

1. **Task 13 — Contrato de modelo e migração compatível:** definir precisamente
   colunas/tabela de valores, tipo oficial, vencimento por item, pagamento e
   convênio; estratégia de leitura/migração de rascunhos e testes sem alterar
   interface ou Portal.
2. **Task 14 — Gerador/validador por tipo:** adaptar `ConfigUfRules` e
   `XmlGenerator` ao tipo persistido, interseção UF/receitas, cardinalidades,
   múltiplos valores/FCP, pagamento, convênio e limites, com fixtures locais
   XSD. Não alterar transmissão/consulta/fila.
3. **Task 15 — Interface guiada por tipo:** somente após os contratos acima,
   implementar os painéis e resumo descritos, com limpeza explícita de dados
   incompatíveis e preservação de rascunhos.

Nenhuma dessas tasks deve começar antes da validação desta modelagem.

## Validações da Task 12

- Referências cruzadas revisadas contra Manual, XSDs, `ConfigUfRules`,
  `XmlGenerator` e DDL atual.
- Não foi alterado arquivo funcional, DDL, migration existente, XML, integração
  ou guia; somente este documento e o status do projeto.
- `git diff --check` executado ao final da tarefa.
