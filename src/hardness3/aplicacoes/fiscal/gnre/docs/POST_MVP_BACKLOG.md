# GNRE — Backlog pós-MVP

Registro de pendências deliberadamente deixadas fora do MVP atual. Este arquivo
não inicia task, não autoriza mudanças e não substitui a revisão de uma task por
vez.

## 1. Consolidar nomes temporários `taskNN`

O módulo ainda contém arquivos, testes, ações de compatibilidade e referências
históricas com nomes como `task10`, `task15` e `task18`.

O inventário da [Task 58](tasks/58-inventario-nomes-tecnicos-ativos.md) separa
os contratos ativos do histórico: shell/central/modal/painel ainda carregados,
compatibilidades `task15_*` e `task18_*`, flashes internos e testes. Ele também
define a sequência incremental: caracterizar e isolar Task 15, encerrar a
janela do adaptador Task 18, migrar flashes, Central, painel, script Task 09,
modal Task 34, shell Task 10 e somente então a suíte. A complementação do
inventário inclui `task10-manual-flow.php`: embora não seja incluído pela
apresentação atual, seus formulários históricos e as actions sem prefixo ainda
possuem handlers a caracterizar. Essa caracterização antecede qualquer
remoção/renomeação e não deve ser misturada com regra fiscal, transmissão ou
ambiente. Não renomear ainda contratos HTTP, URLs, arquivos públicos ou chaves
de sessão sem a respectiva task de compatibilidade e testes.

- Antes de qualquer renomeação, inventariar URLs, POSTs, sessão, scripts e
  testes que dependem desses nomes.
- Manter os documentos históricos em `docs/tasks/` como registro das entregas;
  o objetivo é renomear somente contratos ativos e seus testes por capacidade
  de domínio.
- Isolar e depois remover adaptadores legados somente quando não houver
  consumidor confirmado. Não misturar esta limpeza com alteração fiscal ou de
  transmissão.

## 2. Configuração operacional de ambiente e certificado

A preferência por empresa em `GNRE009` escolhe o ambiente apenas para novas
guias. A guia criada usa sempre o ambiente imutável do snapshot `GNRE007` a ela
vinculado; trocar a preferência depois não altera guias existentes.

Endpoints técnicos atualmente centralizados no módulo:

| Ambiente | Configuração UF | Recepção | Resultado/PDF |
| --- | --- | --- | --- |
| Homologação | `https://www.testegnre.pe.gov.br/gnreWS/services/GnreConfigUF` | `https://www.testegnre.pe.gov.br/gnreWS/services/GnreLoteRecepcao` | `https://www.testegnre.pe.gov.br/gnreWS/services/GnreResultadoLote` |
| Produção | `https://www.gnre.pe.gov.br/gnreWS/services/GnreConfigUF` | `https://www.gnre.pe.gov.br/gnreWS/services/GnreLoteRecepcao` | `https://www.gnre.pe.gov.br/gnreWS/services/GnreResultadoLote` |

Esses são endpoints SOAP do Portal, não uma confirmação de credenciamento.
Produção não é bloqueada artificialmente pelo módulo. Para operar de fato,
cada empresa depende de certificado válido e de habilitação prévia do Web
Service no Portal GNRE; são pré-requisitos operacionais externos, não uma
autorização derivada de `GNRE009`. Não há fallback entre Produção e
Homologação: a nova guia somente pode ser criada com `GNRE007` compatível com
empresa, UF e ambiente escolhido, e transmissão, consulta de resultado, fila e
PDF usam exclusivamente o ambiente imutável desse snapshot. O MVP ainda não
possui tela nem persistência própria de certificado GNRE: isso deve ser uma
task separada, sem reutilizar ou depender de classes da NF-e. A futura tela de
Configuração deve apresentar os links acima e um roteiro objetivo de
pré-requisitos, sem expor certificado, senha ou conteúdo sensível.

## 3. Geração automatizada a partir da NF

Esta automação permanece fora do MVP e depende de levantamento com o cliente.
A GNRE continuará independente da implementação de NF-e: uma futura integração
deve criar os mesmos registros GNRE e seguir o mesmo fluxo de snapshot, XML,
transmissão, resultado e PDF.

Antes de desenhar a task, confirmar com o cliente:

- evento que dispara a necessidade da guia;
- origem da UF favorecida e das receitas, inclusive mais de uma receita/item;
- como são definidos vencimento e pagamento;
- documentos que devem compor cada item;
- regras de revisão, cancelamento, rejeição e reenvio;
- quem aprova a criação antes da transmissão.

Nenhuma receita, UF, regra tributária ou fixture oficial deve ser presumida a
partir da NF-e. O snapshot `GNRE007` continua sendo a única fonte de regras
oficiais da guia.
