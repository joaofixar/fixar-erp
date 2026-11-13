# Task 26 — Análise de experiência, navegação e especificação da interface GNRE

**Situação:** concluída analiticamente em 02/11/2026. Não houve alteração de código, CSS, JavaScript, banco, schema, guia, XML, transmissão, consulta, PDF, fila ou snapshot; não foi chamado o Portal nem iniciada a próxima task.

## Decisão de produto

A GNRE deve ter quatro áreas: **Visão geral**, **Guias**, **Nova guia** e **Configuração técnica**, e uma página de guia com abas persistentes. Dados fiscais, operações do Portal e auditoria deixam de formar uma página longa. A interface apenas apresenta regras do snapshot GNRE007 vinculado: servidor, snapshot e gerador/XML continuam fontes de verdade.

A linguagem visual toma o dashboard-personalizado somente como referência: sidebar azul com logo Hardness, menu com contexto e item ativo, cabeçalho da empresa e cards claros. Não copiar código/telas, criar dependência com o dashboard ou reproduzir o Portal GNRE legado.

## Evidências e diagnóstico

Foram examinados task10-presentation.php, task18-flow.php, task18-ui.js, gnre.css, task10-layout.css e FormDefinitionBuilder.php; index.html/dashboard.css do dashboard foram referência visual.

| Evidência atual | Impacto | Especificação |
| --- | --- | --- |
| Já há Visão geral, Guias, Nova guia e seletor de empresa. | Marca e contexto ainda são pouco informativos. | Sidebar Hardness, ícone existente ou SVG inline mínimo, título, breve contexto e item ativo; cabeçalho único com empresa visível. |
| Criação: UF → snapshot local/consulta explícita → tipo → rascunho. | Não comunica bem progresso ou imutabilidade. | Wizard explícito de quatro passos com resumo de seleção. |
| Uma guia empilha item, XML, transmissão, PDF, resultado e histórico. | Tela extensa mistura edição e operação irreversível. | Abas persistentes, resumo e card de próxima ação. |
| FormDefinitionBuilder entrega N/S/O/unavailable, grupos e limpeza declarada. | Seleção pode exigir recarga; feedback é pouco localizado. | Renderer reativo, foco preservado, aviso antes de limpeza. |
| Payload pendente é reaplicado após erro. | Preservação não é claramente comunicada. | Valores reaparecem; resumo de erros + erros de campo/seção. |
| Ações já dependem de estado técnico. | Bloqueios são dispersos e ações parecem duplicadas. | Uma única próxima ação; razões explícitas para indisponibilidade. |

### Bugs UX concretos

| Prioridade | Problema | Requisito futuro |
| --- | --- | --- |
| P0 | Trocar receita, UF ou estado do destinatário pode não redesenhar os campos imediatamente. | Redesenhar no mesmo evento; limpar só dependências declaradas; servidor revalida. |
| P0 | Erro pode fazer parecer que os dados foram perdidos. | Preservar payload válido, valores, foco e mensagens por campo/seção. |
| P1 | XML, transmissão, consulta e PDF coexistem na edição. | Separar operação e eleger somente a ação principal aplicável. |
| P1 | Blocos fiscais, técnicos e operacionais formam tela longa. | Abas/etapas persistentes e resumo compacto. |
| P1 | Criar, editar, corrigir e reenviar têm distinção insuficiente. | Identificar origem, bloqueio da original e limites de estado. |

## Jornada preservada

    Nova guia
    UF → snapshot GNRE007 local (ou consulta explícita) → tipo oficial
    → RASCUNHO imutável → dados/itens → XML válido → transmitir
    → AGUARDANDO resultado → consultar → APROVADA/EMITIDA → PDF oficial
                                      ├→ REJEITADA → novo rascunho de correção
                                      └→ INCONCLUSIVA → auditoria/consulta aplicável

Não inventar status financeiro/pagamento. Emitida/aprovada exige resultado individual favorável, não só finalização técnica. Ausência de PDF não desfaz aprovação.

## Navegação e shell

Sidebar fixa no desktop, faixa azul com logo_hardness.png; em telas menores, gaveta recolhível acessível.

| Área | Contexto | Conteúdo |
| --- | --- | --- |
| Visão geral | Acompanhe o trabalho pendente | Cards: Rascunhos, Aguardando processamento, Emitidas/aprovadas, Rejeitadas e Inconclusivas; cada card aplica filtro. |
| Guias | Consulte e retome guias | Filtros por situação, UF, tipo, período e identificação disponível; linha mostra situação, tipo, UF, atualização e Abrir. |
| Nova guia | Crie pelo snapshot oficial | Wizard descrito abaixo. |
| Configuração técnica | Administração e rastreabilidade | Snapshots GNRE007, consulta explícita de UF e configurações permitidas, com autorização própria. |

Contadores no menu só existem se agregação barata, autorizada e isolada por empresa os sustentar. Cabeçalho mostra breadcrumb, empresa ativa e seletor; ao trocar empresa, retornar a rota segura equivalente e nunca à guia de outra empresa.

### Rotas conceituais

| Rota | Uso |
| --- | --- |
| /gnre | Visão geral da empresa ativa. |
| /gnre/guias?... | Lista e filtros na URL. |
| /gnre/nova | Wizard antes do rascunho. |
| /gnre/guias/{id}/resumo | Entrada da guia existente. |
| /gnre/guias/{id}/dados | Dados e itens. |
| /gnre/guias/{id}/validacao | XML e validação local. |
| /gnre/guias/{id}/transmissao | Transmissão, consulta e resultado. |
| /gnre/guias/{id}/documento | PDF oficial. |
| /gnre/guias/{id}/historico | Auditoria técnica somente leitura. |
| /gnre/configuracao | Área técnica restrita. |

São contratos de UX, não implementação desta task. O servidor reautoriza empresa, guia e ação em toda requisição.

## Guia individual

Acima das abas: breadcrumb, tipo oficial, badge de situação, UF, snapshot vinculado, aviso de correção e card “Próxima ação”. Aba indisponível fica visível e explica o motivo.

| Aba | Conteúdo e regra |
| --- | --- |
| Resumo | Linha do tempo, resumo fiscal, itens, snapshot, estado, bloqueios e próxima ação. Sempre disponível. |
| Dados e itens | Edição só em rascunho; cards fiscais e lista/resumo de item salvo. |
| Validação/XML | Geração/validação local e XML técnico; transmissão bloqueada até sucesso atual. |
| Transmissão e resultado | Transmitir só rascunho válido; consultar só pendente; resultado individual claro. |
| Documento oficial | Obter em guia aprovada sem documento; baixar/reimprimir só binário preservado. |
| Histórico técnico | Tentativas, recibos, retornos, tempos e origem de correção; somente leitura. |

Guia rejeitada original é somente leitura e oferece somente criar/abrir rascunho de correção. O derivado mostra a origem, mas é nova tentativa. Inconclusiva não libera retransmissão sem regra de servidor.

    [sidebar Hardness | Visão geral | Guias | Nova guia | Configuração]
    Empresa ativa [seletor]                                  [ação contextual]
    Guias / Guia #19                                  [badge APROVADA]
    Tipo 0 · UF PR · snapshot #1                  Próxima ação: Baixar PDF
    [Resumo] [Dados e itens] [Validação/XML] [Transmissão] [Documento] [Histórico]

## Fluxo guiado e interação

1. **UF favorecida:** explicar que define opções oficiais.
2. **Snapshot:** escolher versão local (data/hash) ou consulta explícita; explicar vínculo imutável.
3. **Tipo GNRE:** mostrar somente tipos permitidos e a estrutura de cada um.
4. **Rascunho:** abrir Dados e itens com painel persistente UF/tipo/snapshot.

Todo grupo fiscal terá título, explicação curta e indicação de regra da configuração oficial. Obrigatório, opcional, indisponível e pendente recebem texto e visual distintos. Antes de invalidar dados por seleção, informar os campos a limpar; preservar independentes. Receita, valor e UF/cidade redesenham sem recarga.

Valores alternativos: “Qual valor a configuração aceita?” → Valor principal ou Valor total; mostrar só par/FCP aplicável. Resumo não é cálculo fiscal. Destinatário começa por UF e busca cidade por nome/IBGE; legado incompatível fica visível e exige correção. Emitente segue como prévia ERP, sem novos campos sem modelo/serialização aprovada.

Após salvar, exibir Item salvo (receita, documento, valor/resumo) e lista quando houver vários. Em erro: banner, erro junto ao campo/seção, foco no primeiro e valores submetidos preservados.

## Matriz por tipo oficial

| Aspecto | Tipo 0 — Simples | Tipo 1 — múltiplos documentos | Tipo 2 — múltiplas receitas |
| --- | --- | --- | --- |
| Entrada | Um item; documento dentro dele se exigido. | Receita/contexto e documentos vinculados, se o modelo suportar. | Lista de itens, cada qual com receita/campos próprios. |
| Resumo | Receita, documento, valor e vencimento. | Receita, contador/documentos e valor aplicável. | Totais por item e resumo da guia, sem cálculo fiscal novo. |
| Ações | Editar; sem adicionar outro item. | Gerir documentos conforme regra/modelo. | Adicionar/editar/remover item conforme servidor. |
| Risco | Procurar lista inexistente. | Confundir documento com receita. | Confundir valor de item com total. |

O Tipo 1 exige confirmação de persistência/XML antes de expor controles; a interface não simula suporte inexistente.

## Responsabilidades

| Camada | Responsabilidade |
| --- | --- |
| Interface/JS | Renderização declarativa, reatividade, foco, mensagens, filtros, máscaras não normativas e estado visual. |
| Servidor | Autorização/CSRF, transições, snapshot, regras, validação, persistência, XML, Portal e PDF; nunca confiar no navegador. |
| Persistência | Apenas domínio/auditoria necessários; aba, expansão e filtro não viram coluna. |
| Visual | Cards, ícones, badges, rótulos e responsividade sem mudar regra/status. |

## Responsividade e acessibilidade

- Desktop: sidebar 280--300 px e abas legíveis; tablet: sidebar recolhível, cards/filtros em duas colunas; celular: gaveta, uma coluna e ação antes do formulário.
- Tabelas viram cards ou mantêm rolagem horizontal identificável.
- Texto acompanha cor; foco visível; aria-current, aria-live e rótulos corretos. Ícones usam o projeto ou SVG inline, sem biblioteca nova.

## Priorização e plano de implementação

1. **P0:** renderer reativo e preservação/erros por campo.
2. **P1:** shell, empresa, breadcrumb e navegação responsiva.
3. **P1:** página por abas e Próxima ação.
4. **P1:** Dados/itens por tipo, após confirmar Tipo 1.
5. **P2:** Visão geral/Guias com filtros e contadores confiáveis.
6. **P2:** Configuração técnica segregada e acessibilidade.

| Task futura | Dependência | Critério de aceite e teste manual |
| --- | --- | --- |
| 26.1 Estados e rotas | Aprovação desta análise | Mapa de aba/ação por estado; abrir rascunho, pendente, aprovada, rejeitada e inconclusiva. |
| 26.2 Shell/empresa | 26.1 | Sidebar, empresa, breadcrumb e mobile; trocar empresa e negar guia alheia. |
| 26.3 Renderer e erros | 26.1 | Receita/valor/UF/cidade sem reload; limpeza declarada, foco e payload após erro. |
| 26.4 Dados por tipo | 26.3 + confirmação Tipo 1 | Criar/editar tipos permitidos sem fingir suporte inexistente. |
| 26.5 Página e operações | 26.1 + 26.4 | Abas/ação principal; XML, homologação autorizada, consulta, PDF e histórico. |
| 26.6 Visão geral/configuração | 26.2 + 26.5 | Filtros, cards, ausência de status financeiro e acesso técnico. |
| 26.7 Regressão/a11y | Todas | 320 px/tablet/desktop, teclado, foco, contraste e mensagens. |

Nenhuma task futura autoriza migration, mudança de regra, Portal ou alteração de guia existente por si só. Dado novo ou transição nova requer task específica aprovada.

## Registro e handoff

Foram alterados somente este documento e docs/PROJECT_STATUS.md. Não há testes executáveis nesta task documental. A próxima ação depende da validação do usuário; o primeiro candidato é 26.1, não uma implementação ampla.

