# Task 59 — Análise e desenho do MVP de geração manual de GNRE a partir de NF

## Resultado e limite

Análise estática concluída em 04/11/2026. O MVP é viável sem acoplar o módulo GNRE às classes internas de NF-e: a tela futura deve consumir um contrato de leitura de dados persistidos, criar explicitamente um rascunho e guardar a NF de origem em `GNRE002`.

Não houve tela, alteração de `D006`, tabela, migration, SQL de alteração, criação de guia, XML, transmissão, consulta, PDF, Portal, contas a pagar ou dados reais. Foram lidos integralmente `PROJECT_STATUS.md`, a revisão de consolidação Task 30 e o inventário Task 58. Preserva-se a divisão vigente: `GNRE007` é o snapshot imutável das regras oficiais; `GNRE001` a `GNRE008` guardam fatos e auditoria; sessão não é fonte fiscal.

## Fluxo futuro: “Gerar guia a partir de notas”

1. O usuário escolhe empresa GNRE e período. A busca consulta somente o contrato de leitura de NF persistida, por empresa e data de emissão.
2. A lista inicial contém apenas NFs autorizadas, não canceladas e com algum `T008` cujo `T008_D006_Id` leve a um `D006` marcado ST, DIFAL ou FCP. `T008` responde exclusivamente pela elegibilidade.
3. A tela apresenta identificação, destinatário/UF, chave/protocolo, marcadores encontrados, vínculo e totais fiscais separados; ela não recalcula nem soma itens.
4. Em **Incluir guia**, o servidor relê, na transação, empresa, autorização, cancelamento, elegibilidade, vínculo, configuração empresa × UF e snapshot `GNRE007`. Nenhum total vindo do navegador é confiável.
5. A inclusão cria somente `GNRE001` em `R`, seu documento `GNRE002` e os itens/valores compatíveis com configuração e snapshot. Dados sem fonte segura permanecem para revisão manual. Não cria XML, transmissão, consulta, PDF ou financeiro.
6. Após o commit abre a guia no fluxo normal. Só **Transmitir GNRE** poderá iniciar operações com Portal.

Se já houver vínculo, a ação abre/informa a guia existente. Conflito de chave concorrente deve ter a mesma resposta, após reler o vínculo vencedor.

## Contrato de leitura de NF: fatos confirmados

O adaptador futuro fica na borda de integração e entrega DTO/array de dados persistidos. O núcleo GNRE não deve importar `VEN012`, `VEN003` ou classes de NF-e.

| Grupo | Campos confirmados | Uso |
| --- | --- | --- |
| Identidade e posse | `T007_Id`, `T007_C004_Id`, `T007_Numero_Nota_Fiscal`, `T007_Serie`, `T007_Data_Emissao`, `T007_D024_Id` | Busca, apresentação e documento de origem. |
| Autorização | `T007_Numero_Protocolo_Nfe`, `T007_Data_Hora_Protocolo_Nfe`, `T007_Chave_Acesso_Nfe` | Protocolo não vazio é requisito; os demais são evidência complementar. |
| Cancelamento | `T007_Flag_Cancelada`, `T007_Numero_Protocolo_Cancelamento_Nfe`, `T007_Protocolo_Inutilizacao` | `Flag_Cancelada='S'` exclui/bloqueia; os demais sinalizam revisão e nunca autorização. |
| Item/CFOP | `T008_T007_Id`, `T008_D006_Id`, `D006_Id`, `D006_Codigo_CFOP`, `D006_Descricao` | `EXISTS` de elegibilidade, sem somar `T008`. |
| Totais | campos `T007` abaixo | Preenchimento inicial por componente. |

`T007_Gravar_Totalizacao_4` confirma que o ERP soma valores dos itens e grava os totais em `T007`; sua atualização ocorre apenas para NF não cancelada, não inutilizada e sem protocolo. A integração GNRE lê os totais finais persistidos em `T007`, sem repetir essa agregação.

### Marcadores em D006

Os marcadores não existem no schema inspecionado. Uma task própria poderá definir, por exemplo, `D006_Flag_Gera_GNRE_ST`, `D006_Flag_Gera_GNRE_DIFAL` e `D006_Flag_Gera_GNRE_FCP`, com `S`/`N` e padrão `N`. Eles não guardam receita, vencimento, dados de Portal ou regras de preenchimento: apenas indicam elegibilidade.

## Matriz de componentes e fontes reais

| Componente | Total final T007 | Origem confirmada no totalizador | Decisão de desenho |
| --- | --- | --- | --- |
| ICMS ST | `T007_Valor_ICMS_Substituicao_Retencao` | `SUM(T008_Valor_ICMS_Substituicao_Retencao)` | Candidato ST; não confundir com `T007_Valor_ICMS_Substituicao`, outro total persistido. |
| DIFAL destino | `T007_Valor_Total_Partilha_ICMS_Destino` | `SUM(T008_Valor_Partilha_ICMS_Destino)` | Candidato DIFAL destino. |
| FCP partilha/DIFAL | `T007_Valor_Total_FCP_Partilha` | `SUM(T008_Valor_FCP_Partilha)` | Componente próprio. |
| FCP regular | `T007_Valor_Total_FCP` | `SUM(T008_Valor_FCP)` | Não é sinônimo de FCP-partilha. |
| FCP-ST | `T007_Valor_FCP_ST` | Total autorizado persistido na NF; `T008_Valor_FCP_ST` apenas para elegibilidade/CFOP | Componente próprio de ST; GNRE usa exclusivamente o total `T007`. |
| FCP-ST retido | `T007_Valor_FCP_ST_Ret` | `SUM(T008_Valor_FCP_Retido)` | Existe; seu uso exige decisão explícita. |

O modelo GNRE já admite valores por item em `GNRE006`, incluindo pares de tipos como `11`/`12` e `21`/`22` para FCP. Receita, tipo de valor e combinação permitida, contudo, devem ser selecionados/validados pelo `GNRE007`; não se inventa regra por UF ou receita. Em DIFAL + FCP destino (inclusive RJ), `T007_Valor_Total_Partilha_ICMS_Destino` e `T007_Valor_Total_FCP_Partilha` entram separados. FCP nunca é somado silenciosamente ao principal.

## Configuração própria por empresa × UF

Propõe-se, apenas como desenho histórico, uma configuração interna conceitual `GNRE010`, com unicidade `(GNRE010_C004_Id, GNRE010_UF_Favorecida)`. Esta proposta foi superada pela coorte real `GNRE010` e suas participantes `GNRE011`, criadas na Task 66; este documento não define mais schema para esses nomes.

Campos mínimos: empresa, UF favorecida, ativo, receita/detalhamento selecionados do snapshot, vencimento padrão, componentes habilitados (`st`, `difal_destino`, `fcp_partilha`, `fcp`, `fcp_st` e, se aprovado, `fcp_st_retido`) e fonte por campo (`NF`, empresa, destinatário ou revisão manual). Histórico/versionamento, se necessário, é evolução posterior.

A configuração escolhe intenção operacional local, não substitui a regra oficial. A receita precisa existir no `GNRE007` da mesma empresa/UF/ambiente; campos permitidos/obrigatórios, tipos GNRE e valores admissíveis continuam no snapshot. Configuração ausente/inativa, receita inválida ou fonte incompleta deve bloquear ou deixar explícita a revisão manual, nunca preencher por suposição.

## Vínculo, schema mínimo e idempotência

`GNRE002` já tem `GNRE002_GNRE001_Id`, `GNRE002_Identificacao`, `GNRE002_Descricao` e índice por guia. A evolução mínima proposta é `GNRE002_T007_Id INT UNSIGNED NULL` e `UNIQUE KEY UQ_GNRE002_T007_Id (GNRE002_T007_Id)`.

Não é proposta uma tabela de vínculo. Como o MVP aceita uma guia por NF, unicidade global de `T007_Id` impede duplicação inclusive entre dois usuários; documentos manuais deixam o campo nulo. A implementação deve auditar duplicidades antes de aplicar a chave e pode adicionar índice de leitura se o único não bastar. Nada disso foi executado.

Na transação, o insert do documento com `T007_Id` trata a colisão como “guia já existente” e consulta a `GNRE001` proprietária. `GNRE002_Identificacao` segue preenchido com identificação de NF exigida pelo snapshot; chave de acesso é candidata, mas tipo/formato dependem da regra oficial.

## Critérios e reavaliação

| Situação | Busca/inclusão | Antes de transmitir |
| --- | --- | --- |
| Autorizada, não cancelada, elegível e livre | Lista e permite incluir. | Relê autorização, cancelamento, elegibilidade, totais e configuração. |
| Já vinculada | Abre/informa a guia existente. | Mantém vínculo. |
| Cancelada | Não elegível; vinculada fica sinalizada na Central. | Bloqueia; não apaga/sobrescreve guia, XML, tentativas ou auditoria. |
| Alterada após rascunho | Aviso somente quando houver comparação persistida. | Bloqueia até revisão manual; nunca sobrescreve a guia. |
| Sem protocolo/autorização | Não lista nem inclui. | Continua bloqueada. |
| Inutilizada/evidência contraditória | Nunca tratada como autorizada. | Bloqueia e pede revisão. |

Para detectar alteração, evolução futura deve persistir uma fotografia ou hash dos fatos usados: protocolo, chave, cancelamento, CFOPs elegíveis, UF, totais separados e configuração/snapshot. O vínculo sozinho não basta. Divergência é barreira de transmissão e sinalização de revisão, não sincronização automática.

## Financeiro e eventos de NF

Contas a pagar ficam manuais e somente após resultado individual persistido `situacaoGuia=0`; status `F` isolado não autoriza. A ação futura poderá receber guia, resultado/tentativa e NF de origem como contexto de um caso de uso financeiro próprio. Há integrações existentes com `T002_T007_Id`, mas nenhum contrato GNRE→financeiro foi encontrado para reutilização segura.

| Evento localizado | Constatação | Posição |
| --- | --- | --- |
| `VEN012->gerarNotaFiscal` | Cria `T007`/itens em fluxo longo e transacional de faturamento. | Não criar GNRE síncrona; no futuro, apenas solicitação pós-commit e de melhor esforço por contrato persistido. |
| `VEN003->continuarLiberacao` | Fluxo de continuação com estoque, financeiro e transação; lê `T007`/`T008`. | Também não criar GNRE síncrona; pode ser observação pós-commit desacoplada. |

Em ambos, falha de GNRE precisa virar alerta/pendência e jamais impedir, reverter ou tornar dependente a emissão da NF.

## Riscos e decisões adiadas

- Cliente/contábil e Portal devem confirmar receita, detalhamento, tipo de valor, documento de origem e vencimento por empresa × UF × componente.
- É preciso confirmar quando ST usa o total de retenção e se FCP regular, partilha, ST e ST retido integram a guia de cada configuração.
- Referência, convênio, destinatário, município e formato de documento exigem fonte confirmada e validação do snapshot; falta de fonte exige revisão.
- Não automatizar eventos VEN012/VEN003, financeiro, XML, Portal, PDF, consulta ou transmissão nesta linha de trabalho.

## Próximas tasks pequenas

1. Contrato de leitura de NF e fixtures de autorização/cancelamento/CFOP/totais.
2. Cadastro dos três marcadores em D006, sem receita ou geração de guia.
3. Configuração empresa × UF compatível com `GNRE007`.
4. Vínculo `GNRE002_T007_Id`, saneamento prévio e teste de concorrência.
5. Tela de busca e inclusão somente de rascunho, com valores separados.
6. Fotografia de origem e bloqueio de transmissão para NF alterada/cancelada.
7. Ação financeira manual após `situacaoGuia=0`; depois, avaliar evento pós-commit de NF isolado e não bloqueante.

## Evidências e validação

Foram feitas buscas `rg` em `configuracoes/`, `scripts/sql/`, `ide/personalizacaoConf/`, `dados_usuarios/` e `aplicacoes/fiscal/gnre/`, com inspeção de `scripts/sql/T007_Gravar_Totalizacao_4.sql`, `scripts/atualizacaoBase.php`, `VEN012.PHP-alterarMetodos.php` e `VEN003.PHP-alterarMetodos.php`. Nenhuma consulta/alteração foi executada em banco, Portal ou guia real. A validação final é `git diff --check`.
