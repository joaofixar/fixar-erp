# Task 08 — Interpretação e exibição do resultado individual da GNRE

## Entrega

A tela da guia agora lê exclusivamente `GNRE005_Ultima_Consulta_SOAP`, já
persistido, e mostra separadamente o retorno do lote e o resultado de cada
`guia` devolvida pelo Portal. Não há consulta ao Portal, execução de worker ou
alteração de estado durante a leitura da página.

`GNRE001_Status=F` e `GNRE005_Situacao=FINALIZADA` continuam sendo estados
técnicos de consulta concluída. Eles não são apresentados como autorização. A
mensagem de negócio é derivada de `situacaoGuia`:

| `situacaoGuia` | Significado oficial | Apresentação |
| --- | --- | --- |
| 0 | Processada com sucesso | Guia emitida/aprovada pelo Portal. |
| 1 | Invalidada pelo Portal | Não emitida/autorizada. |
| 2 | Invalidada pela UF | Não emitida/autorizada. |
| 3 | Erro de comunicação | Não emitida/autorizada. |
| 4 | Pendência de processamento | Ainda em processamento; não emitida/autorizada. |

O bloco de resultado mostra: situação do lote (`situacaoProcess.codigo` e
`descricao`), situação individual, mensagem clara de negócio e, quando houver,
uma tabela dos `motivosRejeicao.motivo` com código, descrição e campo. SOAP
ausente, inválido ou com `Fault` é apresentado como não interpretável, sem
afirmar emissão.

## Fontes oficiais verificadas

- Manual de Integração de Contribuintes GNRE v2.17, item 4.2.2, pp. 23–24:
  estrutura `TResultLote_GNRE`, `situacaoProcess`, `resultado/guia`, os cinco
  valores de `situacaoGuia`, e `motivosRejeicao/motivo/{codigo,descricao,campo}`.
- Manual v2.17, item 4.2.3, p. 25, Quadro IV: situações do lote: 400 recebido,
  401 em processamento, 402 processado com sucesso, 403 processado com
  pendência (inclusive guias em situação 4) e 404 erro no processamento.
- `referencias/gnre-v2.17/lote_gnre_result_v2.00.xsd`, elementos
  `situacaoProcess` (linhas 150–173), `situacaoGuia` (179–199) e
  `motivosRejeicao` (97–131).
- `referencias/gnre-v2.17/lote_gnre_result_consulta_v2.00.xsd`, mesmos
  elementos para a consulta usada pelo módulo (175–202 e 228–271).

Não se deduziu autorização a partir de HTTP, `situacaoProcess` ou `F`: a fonte
oficial determina que o resultado do lote contém a validação de cada GNRE, e a
situação individual é exibida tal como retornada.

## Arquivos alterados

- `src/ResultInterpreter.php`: interpretação reutilizável e somente leitura do
  SOAP. Usa DOM quando disponível e um fallback sem expansão de entidades para
  instalações PHP locais sem `ext-dom`.
- `public/index.php`: carrega o SOAP de `GNRE005`, chama o interpretador e
  renderiza os dados individuais.
- `tests/task08-result-interpreter-synthetic.php`: cenários sintéticos.
- `docs/PROJECT_STATUS.md`: registro da conclusão desta tarefa.

Não houve migration, tabela nova, mudança em `GNRE004`, cópia de SOAP ou
alteração de estados persistidos.

## Testes executados

`php aplicacoes/fiscal/gnre/tests/task08-result-interpreter-synthetic.php`

O teste cobre SOAPs de guia em processamento (`4`), final sem rejeições (`0`),
final rejeitada com motivos (`1`) e SOAP inválido/ausente. Também foram
executados `php -l` nos três arquivos PHP e `git diff --check`.

## Validação pela interface — guia #4

1. Abra a guia #4 da empresa de homologação, sem acionar **Consultar resultado**.
2. Em **Resultado da guia**, confira que a situação do lote é `403 — Lote
   Processado com pendencias` (texto armazenado pelo Portal).
3. Confira a situação individual e a mensagem **não foi emitida/autorizada**;
   `F` deve aparecer somente como estado técnico, jamais como autorização.
4. Em **Motivos de rejeição**, confira os itens retornados no SOAP persistido:
   emitente ausente; razão social do emitente ausente; UF/município do emitente
   ausentes; e receita não gerada pela UF favorecida — sempre com o código,
   descrição e campo exatamente enviados pelo Portal.

Esta conferência reutiliza a linha existente em `GNRE005`; não requer nova
chamada externa.

## Limitações conhecidas

- A relação atual é uma guia por recibo; se um SOAP futuro contiver mais de uma
  `guia`, todas são exibidas e identificadas pela sua ordem no retorno, pois o
  leiaute retornado não inclui um identificador local da `GNRE001`.
- Esta tarefa não implementa campos dinâmicos, preenchimento de emitente,
  validação por ConfigUF, PDF, pagamento ou automação de fila.
