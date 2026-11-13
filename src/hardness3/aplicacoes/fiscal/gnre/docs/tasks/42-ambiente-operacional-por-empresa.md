# Task 42 — Ambiente operacional da GNRE por empresa

## Resultado

`GNRE009_Ambiente` define o ambiente pretendido somente quando uma nova guia é
iniciada. A busca e a consulta de configuração usam esse valor para selecionar
ou criar exclusivamente um snapshot `GNRE007` da mesma empresa, UF e ambiente.
A resposta oficial com ambiente divergente é recusada antes de qualquer
persistência de guia.

Depois da criação, `GNRE001_GNRE007_Id` é a referência imutável: transmissão,
consulta manual, fila e PDF leem `GNRE007_Ambiente` do snapshot vinculado. Uma
alteração posterior em `GNRE009` não muda guias já existentes.

## Segurança operacional

O resolvedor continua sendo a allowlist fechada de endpoints de Produção (`1`)
e Homologação (`2`); nenhuma URL de POST, banco ou parâmetro externo é aceita.
A validação operacional aceita ambos os ambientes e não há fallback entre eles.
Os clientes GNRE continuam reutilizando o certificado A1 configurado para a
empresa no ERP, com as validações já existentes antes de qualquer chamada.

Produção não possui bloqueio artificial pelo módulo. A operação real nesse
ambiente requer, como pré-requisitos externos, certificado válido da empresa e
habilitação prévia do Web Service no Portal GNRE. Isso não altera a regra de
seleção: `GNRE009` apenas orienta novas guias, e transmissão, consulta de
resultado, fila e PDF derivam exclusivamente o ambiente do `GNRE007` vinculado
à guia.

Não houve chamada ao Portal, nem alteração de banco, snapshot, guia, XML,
transmissão, consulta ou PDF real durante esta implementação.

## Correção de caminhos legados

Os helpers `gnre_rules` e `gnre_store_rules`, a seleção local de `GNRE007` e o
cliente de configuração passaram a exigir ambiente explícito, sem padrão para
Homologação. `start_new_guide` e a consulta administrativa leem `GNRE009`
explicitamente; `reload_guide_config` usa o `GNRE007_Ambiente` já vinculado.
`attach_guide_config`, a criação legada e as ações `task15_*` são bloqueadas
com mensagem controlada quando não há um contexto de snapshot inequívoco.
Assim, nenhuma rota restante infere Produção ou Homologação por ausência de
parâmetro.
# Correção de criação de rascunho — 03/11/2026

O fluxo `guide_draft_create` passou a ler o estado transitório da empresa e
validar `environment` por `assertOperationalAllowed` antes de carregar o
snapshot por UF/empresa/ambiente e antes do `INSERT INTO GNRE001`. Não existe
fallback entre produção e homologação, nem consulta ao Portal nesse caminho.
Falhas de criação agora mostram mensagem pública de rascunho; falhas de item
mantêm a mensagem específica de item. Não houve migration, banco, guia real,
Portal ou endpoint alterado.

