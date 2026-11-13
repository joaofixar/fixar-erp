# Task 38 — Configuração mínima de ambiente por empresa GNRE

## Entrega

`GNRE009` é a única tabela nova. Ela contém `GNRE009_Id`,
`GNRE009_C004_Id` e `GNRE009_Ambiente`, com chave única por empresa e padrão
`'2'`. A ausência de registro também é tratada pelo serviço como homologação.

A tela `?view=config`, protegida pela permissão GNRE já existente e pelo
contexto de empresa revalidado, permite salvar somente `1` (Produção) ou `2`
(Homologação), com CSRF. A preferência é isolada por `C004_Id`.

## Correção visual

Em 03/11/2026, a rota deixou de usar uma página HTML isolada e passou a
renderizar o painel dentro do mesmo shell GNRE das listas e guias: sidebar,
cabeçalho, empresa ativa, área central e responsividade são preservados. O
painel inclui retorno explícito para a lista de guias.

## Limite deliberado

Produção é somente uma preferência persistida nesta etapa. Nenhum fluxo
operacional a consome: `ConfigUfClient`, `ReceptionClient`, `ResultClient`,
snapshots `GNRE007`, guias, XML, fila, transmissão, consulta e PDF continuam
em homologação. Não há alteração de `GNRE007_Ambiente`, nem criação de
`GNRE010`, auditoria adicional, endpoints ou material de certificado.

## Próxima condição

A próxima task deve centralizar a resolução de ambiente e endpoints, ainda sem
chamar produção. Somente depois dessa centralização poderá haver uma decisão
separada sobre liberar qualquer operação produtiva.

## Roteiro manual sem Portal

1. Abra `?view=config` para uma empresa GNRE e confirme Homologação como padrão.
2. Salve Produção e confirme apenas a preferência persistida.
3. Abra uma guia existente e confirme que ela continua em Homologação.
4. Troque a empresa GNRE e confirme que a preferência é isolada.
5. Envie valor inválido ou tente empresa sem acesso e confirme o bloqueio.
