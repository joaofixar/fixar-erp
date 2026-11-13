# Painel da Reforma Tributária

## Objetivo

Visão simples e somente leitura da evolução da Reforma Tributária no Hardness. A tela inicial mostra o que já pode ser usado e as frentes que ainda estão em desenvolvimento, sempre em linguagem operacional.

O painel não substitui análise fiscal ou jurídica e é somente leitura: não altera regras fiscais, documentos, cadastros, cálculos ou a empresa ativa do ERP.

## Uso e acesso

- Está disponível a todo usuário autenticado e ativo do Hardness, sem C029 própria.
- Não há seleção de empresa nesta etapa. Quando uma função futura precisar desse contexto, ela o solicitará dentro da própria função, sem alterar `Empresa_Atual`.
- A navegação lateral contém somente Visão geral, Configurações fiscais e Atualizações. A configuração por empresa está na aba **Empresa** de Configurações fiscais, ao lado de CFOP e NCM.
- A tela não expõe tabelas, campos, functions, procedures, diagnósticos internos, datas, prazos ou promessas de entrega.

## Estados exibidos

Na tela inicial há duas seções: **Já disponível no Hardness**, identificada com check verde e restrita a recursos realmente utilizáveis, e **Em desenvolvimento**, identificada com ícone de construção. Vendas, Compras e Configurações fiscais estão disponíveis nas rotinas do Hardness; as demais frentes não são promovidas como funcionalidades antes de estarem prontas.

## Diagnóstico técnico

O inventário técnico da Task 1 foi preservado como apoio interno em `public/diagnostico-tecnico.php`. Ele não é exibido nem vinculado pela tela inicial.

## Entregas

- [Task 1 — estrutura e diagnóstico técnico](tasks/01-estrutura-e-diagnostico.md)
- [Task 2 — tela inicial do painel](tasks/02-painel-preparacao.md)
- [Task 3 — configuração da empresa](tasks/03-configuracao-empresa.md)
- [Task 4 — configurações fiscais globais](tasks/04-configuracoes-fiscais-globais.md)
- [Task 5 — padronização visual](tasks/05-padronizacao-visual.md)
- [Task 6 — descoberta e arquitetura da Calculadora](tasks/06-descoberta-calculadora.md) — concluída; a recomendação é integrar somente o componente oficial local, após validar artefato, licença, contrato, operação e cenários homologados.
- [Task 7 — viabilidade da Calculadora Oficial RTC](tasks/07-viabilidade-calculadora-oficial.md) — concluída; recomendação: **viável com dependências externas ou internas**. Não autoriza instalação: runtime, licença, rede privada, operação e homologação fiscal continuam bloqueantes.
- [Task 8 — acompanhamento e navegação do painel](tasks/08-acompanhamento-e-navegacao.md) — concluída; reorganiza o painel como área de acompanhamento, sem calculadora ou integração.

## Próximo passo

A calculadora foi adiada e não possui task de implementação ou instalação aberta. Os estudos das Tasks 6 e 7 permanecem preservados como evidência de viabilidade, sem autorização para Docker, Java, serviços, chamadas à Receita, fórmulas próprias ou integração. Uma eventual retomada deverá receber nova numeração e escopo aprovado.

## Regras de leitura do painel

- CFOP e NCM são indicadores e cadastros **globais**; não usam nem representam a `Empresa_Atual`.
- O tipo de preenchimento automático é uma configuração **por empresa**. A lista respeita as empresas visíveis para o usuário; ROOT visualiza todas as empresas ativas.
