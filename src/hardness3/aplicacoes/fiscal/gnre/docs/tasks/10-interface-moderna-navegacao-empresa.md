# Task 10 — Interface moderna e navegação por empresa

**Situação:** corrigida em 29/10/2026; validação manual de navegador pendente.

## Correção da interface

- A apresentação foi retirada do `register_shutdown_function()` e passou para `public/task10-presentation.php`, incluída diretamente pelo endpoint dentro de `.gnre-content`. Transmissão, consulta elegível, resultado individual persistido, motivos de rejeição e histórico técnico continuam no fluxo principal de uma guia aberta.
- O filtro de vencimento tem o atributo `value` corretamente fechado.
- A tela de requisitos, nova guia e listagem agora são painéis separados, com grade de formulário, labels acima dos controles, ações agrupadas e tabela com contêiner de rolagem horizontal.
- A configuração de UF agora é mantida em sessão por empresa **e por UF**. A nova guia usa apenas a configuração oficial já carregada: troca de UF limpa receita/detalhamento, receita vira combo com código/descrição quando há `TConfigUf`, e fica indisponível com orientação quando não há configuração. Não há receitas fixas nem consulta automática.
- A resposta oficial `102` é apresentada como CNPJ não habilitado no Portal GNRE de homologação, em vez de ausência de receitas.
- A correção pendente restaurou o fluxo manual completo em painéis modernos: documentos `GNRE002`, itens `GNRE003` com edição/remoção, geração/validação local e XML/erros. Os handlers e validações existentes continuam sendo a única regra de negócio. O endpoint prepara formulário, item em edição, regras ativas e prévia de emitente antes de renderizar.
- `task10-presentation.php` publica novamente `gnreTask09Rules` e `gnreTask09Emitter` e inclui o fluxo manual antes da área de transmissão; o comportamento progressivo da Task 09 permanece ativo.

## Entrega

- A GNRE mantém `$_SESSION['gnre']['company_id']` como contexto próprio. A empresa ativa do ERP é somente a sugestão inicial e nunca é alterada pela GNRE.
- A cada requisição, as empresas vêm de `C019`, `C030` (permissão GNRE 310), `C004` e `C007`; somente empresa, usuário e acesso ativos são exibidos e a troca é revalidada.
- A troca limpa configuração UF/receita, filtros e estado temporário GNRE. O navegador avisa antes de descartar formulário alterado.
- A interface inclui barra lateral responsiva, cabeçalho com empresa selecionada no GNRE, cartões de situação e listagem filtrável por situação, UF, receita e vencimento.
- `EMITIDA` é calculada somente a partir do retorno individual SOAP persistido com `situacaoGuia=0`; outro resultado individual final aparece como `REJEITADA`. `F` permanece estado técnico, nunca autorização.
- A aba **Processar** usa PRG para a consulta manual: o feedback é flash por
  empresa/guia, a página relê somente a persistência e mostra uma única ação
  de consulta (ativa ou bloqueada, nunca ambas). A renderização não consulta o
  Portal.
- Não houve transmissão/consulta ao Portal, migration ou alteração de `GNRE004`/`GNRE005`; a guia #4 não é tocada.

## Arquivos

- `bootstrap/app.php`: contexto GNRE isolado, catálogo autorizado e limpeza de estado.
- `public/index.php`: navegação, cartões, listagem/filtros e classificação apresentada.
- `public/gnre.css`, `public/gnre-ui.js`: visual responsivo e aviso de rascunho não salvo.

## Testes e roteiro

- Executados: `php -l` em `bootstrap/app.php`, `public/index.php`, `public/task10-presentation.php` e `public/task10-manual-flow.php`; `node --check public/gnre-ui.js`; `tests/task08-result-interpreter-synthetic.php`, `tests/task09-progressive-ui.js`, `tests/task10-company-context-synthetic.php` e `git diff --check`.
- O `task09-config-uf-synthetic.php` continua indisponível neste container pela ausência da extensão PHP DOM; não há container PHP 7 disponível aqui. Não houve chamada ao Portal.
- Abrir com empresa ERP autorizada, trocar para outra permitida e confirmar que `Empresa_Atual` não muda e que a configuração/filtros GNRE foram limpos.
- Tentar empresa sem permissão GNRE e confirmar 403.
- Conferir cartões com SOAP persistido: só `situacaoGuia=0` é emitida; rejeições e motivos seguem disponíveis em detalhes.
- Confirmar que carregar requisitos da UF continua uma ação explícita e que nenhuma consulta é feita ao abrir a tela.

## Pendência

Validação visual no navegador com duas empresas autorizadas e uma não autorizada antes de tarefa posterior.
