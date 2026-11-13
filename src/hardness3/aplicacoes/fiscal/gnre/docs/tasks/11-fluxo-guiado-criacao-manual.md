# Task 11 — Fluxo guiado para criação manual

**Situação:** concluída em 29/10/2026.

## Entrega

- A nova guia abre no passo **1. UF**, contendo exclusivamente a UF favorecida
  e o botão **Continuar**. A alteração do seletor não executa consulta.
- Continuar é uma ação POST explícita. A configuração oficial é reutilizada da
  sessão quando já existe para a empresa GNRE e UF; caso contrário usa o fluxo
  anterior de consulta. O erro 102 permanece no passo inicial com explicação
  sobre CNPJ não habilitado em homologação.
- A sessão temporária da nova guia é indexada pela empresa GNRE. A troca de
  empresa a limpa pelo mecanismo existente; uma gravação bem-sucedida também
  a remove. Empresa_Atual não é alterada.
- O passo **2. Receita e dados da guia** preserva a UF e oferece somente as
  receitas retornadas pela TConfigUf, com código e descrição. Não há lista
  fiscal por UF no código nem mapeamento novo da modalidade interna.
- A receita atualiza o detalhamento conforme a regra oficial: obrigatório,
  opcional ou oculto/proibido, limpando o valor proibido. A validação PHP e as
  regras oficiais continuam sendo a fonte de verdade.
- Falhas esperadas de criação/edição ficam no layout GNRE, com valores
  submetidos disponíveis para correção. Bloqueios de segurança continuam
  bloqueando a operação.
- Documentos, itens progressivos da Task 09, XML, transmissão e consulta
  continuam no detalhe do rascunho. Não houve migration, chamada de Portal,
  mudança em fila, tabelas ou regras fiscais.

## Arquivos

- public/index.php: estado temporário por empresa, continuação explícita e
  tratamento de erro da guia.
- public/task10-presentation.php, public/gnre-ui.js e
  public/task10-layout.css: etapas e formulário oficial.
- tests/task11-guided-manual-flow-synthetic.php: cenários sem rede.

## Verificação

- Lint PHP 7 e JavaScript, testes sintéticos Tasks 08–11 e git diff --check.
- Roteiro manual: abrir Nova guia, trocar UF sem rede, continuar com UF em
  sessão, trocar receita e verificar limpeza/visibilidade; provocar campo
  obrigatório ausente e confirmar erro no painel; gravar e conferir documentos,
  itens, XML, transmissão e consulta.

## Correção pós-entrega — rascunho sem configuração em sessão

- A edição nunca depende do cache para exibir os valores de GNRE001: UF,
  receita, detalhamento, vencimento, valor, referência e observação continuam
  visíveis.
- Sem TConfigUf em sessão, a receita persistida aparece selecionada como
  “configuração oficial não carregada nesta sessão”. O usuário recebe aviso e
  o botão explícito **Carregar/recarregar requisitos da UF**; abrir a guia não
  consulta o Portal.
- A recarga usa a UF persistida da guia e, em sucesso, volta ao combo e regras
  oficiais. Sem configuração, alteração de guia ou item é bloqueada no layout
  e dados existentes permanecem visíveis.
