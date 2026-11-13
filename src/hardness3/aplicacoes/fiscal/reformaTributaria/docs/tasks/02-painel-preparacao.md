# Task 2 — Tela inicial do Painel da Reforma Tributária

## Escopo entregue

- Cabeçalho simples com título e subtítulo orientados ao acompanhamento da evolução no Hardness.
- Seção **Já disponível no Hardness** com check verde, ícone próprio e ação direta para as rotinas utilizáveis de Vendas, Compras e Configurações fiscais.
- Seção **Em desenvolvimento** com ícone de construção e cards informativos não clicáveis para DANFE e documentos fiscais, Split payment, Validador da empresa e Calculadora / simulador, apresentados em grade 2×2 no desktop.
- Cada status é apresentado em quadro próprio, com fundo branco, borda sutil, cantos arredondados e cards filhos em tom discreto. As ações disponíveis são links diretos, sem modal.
- Textos de impacto em linguagem operacional, sem previsão, data, cronograma ou promessa de entrega.
- Remoção do seletor de empresa, dos blocos de preparação, pendências, próximos passos e acessos em preparação.
- Diagnóstico técnico da Task 1 mantido apenas como apoio interno, sem link ou conteúdo técnico na tela inicial.
- Acesso sem C029 e interface somente leitura.

## Testes locais executados

- `php -l` em `bootstrap/app.php`, `public/index.php` e `public/diagnostico-tecnico.php`.
- `git diff --check`.
- Inspeção estática de `public/index.php` para confirmar a ausência de seletor de empresa, menu lateral, links nos cards, dados técnicos e textos de previsão.
- Varredura do módulo para confirmar que a tela inicial continua sem escrita, chamadas de rotina de negócio ou alteração de `Empresa_Atual`.

## Pendências de validação no Hardness

1. Em sessão autenticada, confirmar que um usuário sem C029 acessa o painel sem precisar selecionar empresa.
2. Conferir em navegador os check verde, ícone de construção, espaçamentos e responsividade dos cards, comparando-os ao padrão das aplicações fiscais do Hardness.
3. Confirmar que nenhum card em desenvolvimento é clicável e que não há menu lateral, seletor de empresa, datas, prazos ou conteúdo técnico na tela principal.
4. Confirmar, por log de consultas, que a abertura não produz escrita nem altera `Empresa_Atual`.

## Evolução futura

Quando existirem recursos funcionais, a navegação deverá ser organizada por assunto operacional: Visão geral, Vendas, Compras, Configurações fiscais, Documentos fiscais, Validador da empresa e Calculadora. O menu lateral só deverá ser introduzido quando houver pelo menos três seções úteis e navegáveis. A seleção de empresa deverá ocorrer dentro de Validador, Configurações fiscais ou Calculadora, somente quando exigida pela função.
