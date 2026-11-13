# Task 76 — piloto de lote físico GNRE pela Fila

Implementada localmente em 06/11/2026, aguardando revisão técnica e validação
manual explícita em Homologação. A **Fila de transmissão** continua tendo seu
botão operacional de processamento individual, sem mudança de chamadas, regras
ou textos do seu ciclo sequencial. A mesma seleção agora oferece a ação
secundária **Transmitir lote real — piloto**.

## Ação única pela Fila

Para uma seleção nova, esse botão executa o ciclo do piloto na mesma solicitação:
valida e separa os grupos, transmite cada grupo físico em Homologação e, quando
há recibo, persiste a primeira consulta para 30 segundos depois. Uma falha de um grupo não
interrompe os demais. O modal mostra o resultado público por grupo (enviado,
recibo recebido, pendente, autorizado, rejeitado ou inconclusivo), sem uma
segunda confirmação técnica de grupos recém-preparados. Grupos já preparados
antes dessa alteração permanecem preservados para auditoria e não são enviados
automaticamente.

## Premissa corrigida: lote nacional

A divisão anterior por UF, receita, snapshot ou regra estadual foi uma medida
conservadora inicial, mas não é uma exigência do Manual GNRE v2.17 nem do XSD
local. `GnreLoteRecepcao` é nacional e `TLote_GNRE/guias/TDadosGNRE` aceita
várias guias, inclusive destinadas a UFs distintas. Cada `TDadosGNRE` continua
derivado do XML individual validado pelo snapshot imutável da própria guia.

O piloto agrupa somente por empresa GNRE, ambiente e a versão técnica XML que
o contrato exigir. Ele forma lotes físicos de no máximo 30 guias e 600 KB,
criando lote 2, lote 3 etc. antes da transmissão quando necessário. Cada lote
é persistido em `GNRE010`/`GNRE011` antes da recepção, com XML completo, hash,
chave de idempotência, usuário e data/hora. O XML físico é validado contra
`lote_gnre_v2.00.xsd`. Não há migration, SQL manual, consulta ou transmissão
real executada nesta task.

O limite `max.itensPorGuia` do snapshot é aplicado individualmente antes da
preparação: uma guia que o exceda é exibida como bloqueada, sem impedir as
demais. A antiga rota de piloto homogêneo foi retirada da navegação e redireciona
com segurança para a Fila; o botão da Fila é o único ponto operacional do lote.

O fragmento congelado preserva o namespace oficial de `TDadosGNRE`. Fragmentos
preparados antes desse ajuste, que o herdavam do XML individual, são
normalizados somente na montagem local do lote para continuar válidos no XSD;
nenhuma guia ou histórico individual é alterado.

## Elegibilidade e reenvio

Pode entrar uma guia nova sem evidência individual ou uma participante de lote
anterior conclusivamente rejeitada (`situacaoGuia` 1 ou 2, ou recepção
confirmadamente rejeitada). Não se cria nem altera `GNRE004`, `GNRE005`,
`GNRE008`, recibos, SOAP ou Histórico individual.

Ficam bloqueadas guias autorizadas, com recibo, timeout/falha técnica/SOAP
ambíguo, `situacaoGuia=4`, lote físico ativo (`PREPARADO`, `ENVIANDO`,
`RECEBIDO`, `PENDENTE`, `INCONCLUSIVO`), arquivo, empresa diversa ou snapshot
incompatível. Rejeição comprovada permite novo grupo; incerteza técnica não.

## Resultado e apresentação

Após o recibo, o modal mostra o contador “O Portal GNRE está processando o
lote. A primeira consulta será feita em XX segundos, conforme regra do Portal
GNRE.” A data/hora é persistida em `GNRE010_Proxima_Tentativa_Consulta`; ao
vencer, a consulta é acionada automaticamente. `400` e `401` com retorno de
recepção são tratados como processamento com recibo, nunca como rejeição.

O modal único da Fila lista todos os grupos e suas guias. `situacaoGuia=0` só
autoriza após correlação inequívoca por `TDadosGNRE`; `1` e `2` registram os
motivos; `4` mantém guia e lote em `PENDENTE` para nova consulta. Ausência,
divergência ou ambiguidade ficam inconclusivas e bloqueiam retransmissão. A
rejeição de recepção mantém código e descrição pública no lote. A projeção da
Central/Fila não altera `GNRE001`: ela continua usando o histórico individual,
enquanto a situação do lote é mostrada no acompanhamento do piloto.

Produção permanece explicitamente desativada. Sua futura habilitação depende
de validação manual explícita deste piloto em Homologação.

## Correção pós-revisão

O bloqueio da Fila sequencial foi ampliado para todos os lotes ativos, tanto na
consulta quanto na revalidação transacional. O acompanhamento da própria Fila
permite consultar grupos `RECEBIDO` e `PENDENTE`, e o envio de vários grupos
isola falhas para preservar o resumo dos demais. A extração do retorno preserva
todos os campos de `TDadosGNRE` usados na correlação. A espera visual da Fila
sequencial continua usando o próximo horário persistido; os dois segundos são
somente o intervalo visual após esse horário vencer, em Homologação e Produção.

Reenvio pelo lote real é agora avaliado por política conservadora: somente
`situacaoGuia` individual única `1`/`2`, recepção comprovadamente rejeitada sem
recibo ou participante de lote real rejeitada. Recibo, falha técnica, SOAP
ausente/ambíguo, `0`, `4` e qualquer lote ativo bloqueiam o novo grupo.

## Identificador interno por participante — teste controlado em Homologação

Para os próximos lotes físicos preparados pela Fila, o fragmento congelado de
cada participante recebe `identificadorGuia` com o próprio `GNRE001_Id`. O
valor é positivo, decimal e limitado a dez dígitos pelo contrato oficial; uma
guia com ID inválido é bloqueada isoladamente. O campo é incluído antes do
hash e da persistência de `GNRE011_Fragmento_Canonico`, portanto permanece
auditável no XML físico enviado.

Se o Portal devolver esse campo, ele só correlaciona diretamente quando aponta
para uma única participante do mesmo lote. Ausência mantém a correlação
conservadora existente; valor inválido, desconhecido ou colidido não autoriza
nenhuma guia. Lotes já existentes, em especial o lote #7, não são alterados,
retransmitidos nem reprocessados por esta mudança. A confirmação de que o
Portal ecoa o campo depende de um novo teste manual em Homologação.

## Idempotência de tentativa física

A chave única de `GNRE010` identifica a tentativa física, não somente o
payload: ela inclui empresa, `GNRE010_Id`, a lista ordenada de participantes e
o SHA-256 do XML físico. Assim, uma nova tentativa permitida após rejeição de
recepção sem recibo pode reutilizar as mesmas guias e o mesmo XML de um lote
histórico sem colidir com ele. A exclusão mútua do próprio lote continua na
transição transacional de `PREPARADO` para `ENVIANDO`; duas chamadas para o
mesmo `GNRE010_Id` não produzem duas transmissões.
