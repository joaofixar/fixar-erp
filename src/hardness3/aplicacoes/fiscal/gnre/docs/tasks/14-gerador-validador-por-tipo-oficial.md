# Task 14 — Gerador e validador por tipo oficial GNRE

## Resultado

Concluída em 29/10/2026. A geração local do XML v2.00 agora usa somente o
tipo oficial persistido em `GNRE001_Tipo_GNRE`; não há inferência pela
quantidade de itens ou documentos. Esta task não alterou banco, migrations,
rascunhos, interface guiada, transmissão, consulta, fila, certificado ou
Portal.

## Arquivos alterados

- `src/ConfigUfRules.php`: valida tipo, interseção UF/receitas, regras 0/1/2,
  limite `itensPorGuia`, datas/convênio e FCP.
- `src/XmlGenerator.php`: lê valores filhos `GNRE006`, totaliza, emite campos
  no nível XSD correto e valida o lote contra o XSD local.
- `public/index.php`: o carregamento dos itens inclui `GNRE006`; não grava nem
  migra dados.
- `tests/task14-gerador-validador-synthetic.php`: fixtures sintéticas locais.
- `tests/task09-config-uf-synthetic.php`: ajustado ao contrato novo.

## Contrato interno aplicado

- Guia: `GNRE001_Tipo_GNRE` e `GNRE001_Data_Pagamento`.
- Item: `GNRE003_Data_Vencimento` e `GNRE003_Convenio`.
- Valores: `item['GNRE006']`, com `GNRE006_Tipo_Valor` e
  `GNRE006_Valor`; são emitidos até dez vezes por item.
- `GNRE001_Valor` permanece apenas compatibilidade: divergência contra a soma
  de `GNRE006` bloqueia XML com mensagem explícita, sem atualização automática.
- `valorExigido` usa os tipos oficiais: `11` é principal e `21` é total;
  `12` e `22` são os pares FCP. `P`, `T` e `A` exigem respectivamente
  principal, total e ambos; `PO`, `TO` e `AO` tornam-nos opcionais. Em `N`,
  nenhum `valor` é aceito no item e `valorGNRE` é omitido, pois a UF calcula o
  valor.
- A compatibilidade de `GNRE001_Valor` continua ativa para toda guia com
  valores informados: qualquer divergência contra a soma de `GNRE006` bloqueia
  XML sem alterar o legado. A omissão de `valorGNRE` ocorre somente se **todos**
  os itens forem `valorExigido=N` e não houver valores filhos. A mistura de
  receita calculada pela UF com receita que aceita/exige valores é bloqueada,
  pois o Manual não confirma o tratamento do total global nesse caso.

## Regras confirmadas aplicadas

- Tipo deve ser `0`, `1` ou `2`, habilitado tanto na UF quanto em todas as
  receitas usadas.
- Tipo 0 tem exatamente um item. Tipo 1 requer múltiplos documentos,
  uma receita, um tipo documental e documentos distintos. Tipo 2 não repete
  receita+documento nem receita+referência.
- O máximo oficial `qtdMaximas/itensPorGuia`, quando numérico, é aplicado.
- Pagamento é global; vencimento e convênio são do item. Para `exigeConvenio`
  e `exigeValorFecp`, `N` significa não exigido (mas informado é aceito), `S`
  opcional e `O` obrigatório. FCP informado exige o valor tributário par; em
  `O`, os pares principal/total requeridos por `valorExigido` também são
  exigidos como `12`/`22`.
- Destinatário segue `exigeContribuinteDestinatario`: `S` obrigatório, `O`
  opcional e `N` não permitido. Quando presente, é serializado dentro do item
  com identificação CNPJ/CPF/IE, razão social e município opcionais. A
  validação local restringe CNPJ a 14 dígitos numéricos; CPF e IE seguem os
  padrões XSD.

## Guias legadas

Rascunho com `GNRE001_Tipo_GNRE` nulo é bloqueado com: “Esta guia foi criada
no modelo anterior e precisa ser revisada no fluxo por tipo GNRE.” Não ocorre
inferência, migration, backfill ou releitura de guias transmitidas.

## Testes

- PHP lint dos arquivos PHP alterados.
- Suítes existentes das Tasks 08, 09, 10, 11 e 13, mais a Task 14,
  executadas localmente no PHP 7.4 do ERP com DOM; as Tasks 12 e 13 de
  modelagem/DDL não possuem suíte executável adicional além da estática 13.
- Task 14 cobre tipos válidos/inválidos, interseções, datas, todos os modos de
  `valorExigido`, convênio/FCP em N/S/O, destinatário obrigatório/opcional,
  múltiplos valores, compatibilidade de total, guia somente N, bloqueio de
  mistura N+valor, CNPJ alfanumérico inválido, limite de dez, legado e XSD.
- `git diff --check` executado sem apontamentos.

## Limitações até a Task 15

Não há interface para selecionar o tipo, editar valores `GNRE006`, pagamento,
vencimento ou convênio. Por isso rascunhos existentes são corretamente
bloqueados para XML até a revisão explícita no fluxo por tipo; esta task não
inicia a Task 15.
