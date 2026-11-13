# Task 8 — Acompanhamento e navegação do painel

## Objetivo

Transformar o Painel da Reforma Tributária em uma área leve de acompanhamento, com navegação lateral e uma visão geral da preparação no Hardness. Esta task não implementa calculadora, simulador, fórmulas, infraestrutura, serviços ou integração com a Receita.

## Decisões

- Sidebar com três destinos: Visão geral, Configurações fiscais e Atualizações. Configuração por empresa integra Configurações fiscais como a aba **Empresa**, ao lado de CFOP e NCM.
- CFOP e NCM são globais: os indicadores e a consulta não leem, filtram nem alteram `Empresa_Atual`.
- O preenchimento automático é por empresa. A listagem é direta, somente leitura, respeita C019 e mostra todas as empresas ativas para ROOT.
- A calculadora foi adiada. As Tasks 6 e 7 são preservadas como estudo de viabilidade; a antiga previsão de “Task 8” para spike foi removida do roadmap para evitar conflito de numeração.

## Fluxos

1. O atalho do menu abre a Visão geral.
2. Os cards CFOP e NCM levam à consulta global já na respectiva aba; Configuração por empresa leva à aba Empresa.
3. Configurações fiscais oferece abas CFOP, NCM e Empresa. CFOP/NCM têm totais, filtros, busca, detalhes expansíveis, CSV e guia visual.
4. A aba Empresa apresenta contadores, busca e filtro de status. O detalhe mantém apenas o status e o guia visual para `Configuração → Empresa → Impostos`.
5. Visão geral resume as entregas disponíveis: cálculos de IBS/CBS nos documentos cobertos, configurações CFOP/NCM, leitura do XML da NF-e recebida e notas de crédito/débito. Em desenvolvimento ficam o novo layout da DANFE e Split payment, sem datas ou promessas.
6. Atualizações apresenta cada frente como card clicável; o modal detalha o alcance em linguagem operacional, sem expor estruturas ou rotinas técnicas.
7. A Visão geral traz uma explicação breve de CBS, IBS e Imposto Seletivo, além de links para as páginas oficiais da Receita Federal e do Portal da Tributação sobre Consumo.

## Arquivos alterados

- `bootstrap/app.php`
- `src/CompanyConfiguration.php`
- `public/index.php`
- `public/layout.php`
- `public/configuracoes-fiscais-view.php`
- `public/configuracoes-fiscais.php`
- `public/configuracao-empresa.php`
- `public/configuracao-empresa-view.php`
- `public/atualizacoes.php`
- `docs/README.md`

## Validações

- `php -l` em todos os PHP alterados.
- `git diff --check`.
- Validação manual no Hardness ainda é necessária para confirmar acesso pelo atalho, números reais, navegação dos cards, escopo de empresas, CSV e responsividade em navegador autenticado.

## Pendências

Não há pendência de implementação desta task. A confirmação funcional exige uma base Hardness autenticada com cadastros CFOP/NCM e empresas disponíveis. A calculadora permanece explicitamente adiada.
