# Task 15 — Interface de cadastro por tipo oficial GNRE

## Resultado

Corrigida em 29/10/2026. O cadastro novo usa UF favorecida, configuração
oficial em sessão da empresa GNRE e tipo oficial persistido antes da inclusão
dos itens. Não foram criadas tabelas, migrations ou regras por UF.

## Fluxo implementado

- A UF é escolhida uma vez; a configuração `TConfigUf` é reutilizada na sessão
  da empresa e retorno 102 é mostrado de forma compreensível.
- A segunda etapa mostra somente os tipos 0, 1 e 2 permitidos pela UF, explica
  cada um e grava `GNRE001_Tipo_GNRE`.
- Os códigos de tipo são convertidos explicitamente para string antes da
  comparação estrita contra `uf_types`; assim uma configuração válida com
  `['0','1','2']` renderiza as três opções, e `['0']` somente a opção simples.
- Pagamento, referência e observação ficam no cabeçalho. O total é calculado a
  partir de `GNRE006` e atualizado em `GNRE001_Valor` somente para compatibilidade.
- Receita, vencimento, convênio e destinatário são gravados no item. Valores
  são linhas `GNRE006`, limitadas a dez; `valorExigido=N` não aceita valores.
- Rascunhos sem tipo continuam legados: recebem aviso e não sofrem inferência
  nem alteração silenciosa.
- Todos os formulários do fluxo possuem campos CSRF e `action` fechados
  corretamente. Valores podem ser adicionados/removidos no navegador até dez,
  com subtotal; documentos são criados/removidos no contexto da guia.
- Trocar a receita recompõe selects oficiais, campos adicionais e visibilidade
  dos campos; o servidor revalida permissões, listas e obrigatoriedade antes
  de persistir.
- Na edição, o estado seguro da tela reaplica detalhamento, produto, período,
  tipo documental, destinatário e linhas `GNRE006` após reconstruir os combos.
  Para convênio, `O` é obrigatório e `S` é opcional.
- A etapa de nova guia oferece “Trocar UF / reiniciar nova guia”, que remove
  somente o estado temporário desta empresa GNRE. Configuração sem tipos
  oficiais não avança; é descartada da sessão e somente é consultada de novo
  quando o usuário confirma a UF.
- Rascunho sem histórico técnico pode ser excluído na interface. O servidor
  bloqueia a guia da empresa em transação, recusa status diferente de `R` ou
  tentativa em `GNRE004`, remove filhos na ordem `GNRE006`, `GNRE003`,
  `GNRE002`, `GNRE001` e faz rollback em qualquer falha.
- Correção de regressão: periodosApuracao já era interpretado em
  receipt['periods']; a falha era a tela montar um select vazio quando a
  receita exigia período, deixando o PHP receber period vazio. Para todo combo
  oficial (detalhamento, produto, período e tipo documental), a tela agora
  informa configuração incompleta e bloqueia o submit se S não vier
  acompanhado de opções. O servidor repete essa proteção antes de persistir.
- Em erro de item, o POST fica temporariamente associado ao rascunho/empresa
  na sessão para reconstruir a receita, campos dependentes e valores, sem
  substituir dados persistidos.
- Correção de continuidade: a abertura de uma guia carrega GNRE001, GNRE002,
  GNRE003 e GNRE006 diretamente do banco. Se a configuração da UF tiver sido
  perdida da sessão, a tela mantém documentos, itens e valores visíveis em modo
  de consulta, bloqueia XML/edição dependente e oferece somente recarregamento
  manual da configuração. A abertura nunca consulta o Portal.

## Arquivos e validação manual

Foram adicionados `src/GuidedDraftController.php`, `public/task15-flow.php`,
`public/task15-ui.js` e o teste sintético da task. Teste manualmente os tipos
0, 1 e 2; inclua itens e valores, confirme o total, troque a receita e confira
a limpeza dos campos. Guias sem tipo devem apenas oferecer revisão no fluxo novo.

As suítes 08, 09, 10, 11, 13, 14 e 15 passaram no container `hardness-php7`,
que confirmou DOM ativo. Lint PHP/JS e `git diff --check` também passaram.
