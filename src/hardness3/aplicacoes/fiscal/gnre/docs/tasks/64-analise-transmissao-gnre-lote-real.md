# Task 64 — Análise técnica de transmissão GNRE em lote real

## Decisão e escopo

Esta task é somente documental: não houve Portal, transmissão, consulta, PDF,
SQL, migration nem mudança operacional.

O Portal suporta um único envio físico com várias guias. A próxima solução não
deve duplicar uma tentativa por guia: precisa de uma entidade de lote, de suas
participantes imutáveis e de uma fila por recibo de lote.

A tela da Task 63 é **processamento sequencial de múltiplas guias**, não
transmissão GNRE em lote real: o navegador chama `gnre_transmit_atomic()` para
cada guia e cada chamada cria `GNRE004`, `GNRE005`, XML e recibo próprios. Ela
permanece inalterada. Recomenda-se renomeá-la para **“Transmitir várias guias
(processamento individual)”**, até a entrega do lote real.

## Contrato GNRE v2.17 revisado

Foram lidos o Manual local v2.17 (seções 4.1, 4.2 e configuração da UF),
`lote_gnre_v2.00.xsd`, `lote_gnre_recibo_v1.00.xsd`,
`lote_gnre_consulta_v1.00.xsd`, `lote_gnre_result_v2.00.xsd`,
`config_uf_v1.00.xsd`, `dados_gnre_v1.00.xsd` e `dados_gnre_v2.00.xsd`.

`TLote_GNRE` tem um contêiner `guias` com `TDadosGNRE` de `minOccurs=1` a
`maxOccurs=unbounded`; a entrada de cada guia declara versão 1.00 ou 2.00 e o
lote tem versão fixa 2.00. É, portanto, uma única mensagem/XML com várias
guias. A recepção devolve um único `TRetLote_GNRE`; sucesso confirmado é código
100 e um único `recibo/numero` de 14 dígitos, com data/hora e estimativa.

O máximo oficial não é uma constante global. `GnreConfigUF` fornece
opcionalmente `qtdMaximas/guiasPorLote`, `itensPorGuia`, `itensPorLote` e
`qtdConsultas`; deve ser lido como inteiro positivo no snapshot da UF. Campo
ausente/inválido bloqueia envio multi-guia. O `maxOccurs=200` de
`resultado/guia` é apenas o limite da estrutura de retorno, não autorização
para ignorar `guiasPorLote`. Também devem ser respeitados os 600 KB máximos da
mensagem. Erro de schema em uma guia rejeita todo o lote na validação de forma.

No resultado, `TResultLote_GNRE/resultado/guia` contém até 200 ocorrências,
cada uma com `situacaoGuia`: `0` sucesso, `1` invalidada pelo Portal, `2`
invalidada pela UF, `3` erro de comunicação e `4` pendência. Pode conter dados
da GNRE, barras, PIX e motivos. `situacaoProcess=402` ou `403` não autoriza
uma guia: somente `situacaoGuia=0` da participante confirma emissão/autorização.

### Correlação segura

O contrato não expõe, em `resultado/guia`, ID do ERP nem atributo de correlação.
Não se deve usar ordem de envio/retorno, e `numeroControle` é opcional e só
aparece no resultado. O manual avisa que `TDadosGNRE` pode faltar em certas
invalidações. Logo, o lote deve persistir antes do envio, por participante: o
fragmento `TDadosGNRE` canônico, seu SHA-256 e uma impressão digital
determinística dos campos de negócio retornáveis. O ordinal é apenas auditoria.

Cada resultado somente será ligado quando essa impressão identificar exatamente
uma participante. `TDadosGNRE` ausente/incompleto, duas candidatas ou guia sem
correspondência geram `INCONCLUSIVO_DE_CORRELACAO`; nenhuma guia é finalizada ou
autorizada por fallback de posição.

Em Homologação, antes da automação, obter evidência com 2+ guias de que os
campos escolhidos retornam em sucesso/rejeição/pendência, de que a ordem não é
necessária e de como uma rejeição sem `TDadosGNRE` pode ser associada. Até isso,
só admitir coortes comprovadamente não ambíguas; as demais ficam no fluxo
individual.

## PDF oficial

`TConsLote_GNRE` consulta o recibo do lote e pode pedir
`incluirPDFGuias=S`. O retorno oferece um único `resultado/pdfGuias`, base64,
descrito como PDF das guias processadas com sucesso. Ele fica no nível do lote;
não existe PDF dentro de cada `resultado/guia` nem chave que associe páginas à
`GNRE001`. A consulta com PDF é mais lenta segundo o Manual.

Não se pode gravar automaticamente o mesmo binário em `GNRE008` para todas as
guias. Homologação precisa preservar a resposta e comprovar: PDF único
multipágina ou arquivos anexados; ordem/marcação de páginas; presença de
controle/barras de cada guia; tratamento de rejeitadas/pendentes; e estabilidade
em nova consulta. Sem relação oficial um-para-um, guardar apenas
**PDF_DO_LOTE**, ligado ao lote. PDF individual automático continua permitido
somente com guia, resultado `0` e PDF inequivocamente relacionados.

## Auditoria e persistência proposta

`GNRE004` pertence a uma `GNRE001`, guarda XML/recibo de uma tentativa;
`GNRE005` é única por `GNRE004` e consulta esse recibo. Replicar o XML e o
recibo compartilhados em N linhas cria N donos aparentes de uma chamada e N
filas concorrentes para o mesmo recibo. Não preserva seleção indivisível,
correlação nem estado do envio físico. O modelo atual, portanto, não basta.

Manter `GNRE004/GNRE005/GNRE008` para o histórico individual existente. O
menor modelo novo, a definir em task de schema sem reaproveitar recibo/XML por
guia, é:

| Estrutura proposta | Responsabilidade mínima |
| --- | --- |
| `GNRE010_Lote` | Uma intenção/envio físico: empresa, UF, ambiente, versão e `GNRE007`/assinatura de compatibilidade, usuário, chave idempotente, hash da seleção, XML integral/SHA-256/tamanho, estado, recepção bruta/HTTP/erro, recibo único, agenda e lease de consulta. É a dona do XML e recibo. |
| `GNRE011_Lote_Guia` | Participação imutável: lote, `GNRE001`, ordinal, fragmento/hash/impressão de correlação, estado individual, resultado/motivos/controles retornados e decisão/data. Não copia recibo ou XML de lote. |
| `GNRE012_Lote_Evento_Consulta` | Evidência imutável de cada consulta/recuperação: lote, horário, HTTP/código/descrição, SOAP/hash, erro e claim/lease. Impede que tentativas de consulta sobrescrevam a auditoria. |

Restrições a detalhar: chave idempotente única por empresa; uma participação por
`(lote, guia)` e uma participação ativa por guia; recibo único no escopo de
empresa + ambiente; e índices de empresa/estado/agenda. A chave é criada antes da chamada externa e,
em retry HTTP, reapresenta o mesmo lote persistido: nunca autoriza reenvio após
incerteza.

Admissão e lock transacional exigem mesma empresa, ambiente, UF, versão e
snapshot `GNRE007` (ou assinatura explicitamente aprovada), além de limites de
guias/itens e 600 KB. A recomendação inicial é exigir o mesmo snapshot, não
tentar compatibilizar snapshots diferentes. Isso bloqueia lote misto de
empresas, ambientes e regras incompatíveis.

## Operação segura

1. Criar `PREPARADO`, congelar participantes/hashes/coorte e bloquear edição,
   reabertura, remoção e retransmissão das participantes.
2. Persistir `ENVIANDO` antes da única chamada. Código 100 mais recibo cria
   `RECEBIDO` e agenda consulta do lote após no mínimo 30 segundos.
3. Timeout, TLS, HTTP não-2xx, Fault, SOAP inválido/incompleto ou resposta sem
   recibo fora de rejeição formal viram `INCONCLUSIVO_RECEPCAO`: consultar e
   recuperar; nunca reenviar cegamente.
4. Enquanto 400/401, reagendar. Em 403, consultar participantes `4`; o Manual
   informa média de 20 min e máximo de 1 h. Código `3`, resultado ausente ou
   correlação ambígua também conservam bloqueio.
5. Só `0`, `1` ou `2` individual e inequivocamente correlacionado fecha a
   participante; apenas `0` confirma emitida/autorizada. Lote pode terminar
   misto. `404` exige preservação/investigação antes de uma nova intenção; não
   é permissão para reenvio cego.

Enquanto houver `PREPARADO`, envio, recibo pendente, consulta pendente,
inconclusão ou correlação incerta, nenhuma participante pode voltar a transmitir
(em lote ou individual). A retomada encontra o mesmo lote pela chave idempotente
e apenas consulta/recupera com lease.

## Matriz

| Aspecto | Task 63 atual | Lote GNRE real |
| --- | --- | --- |
| Chamada física | Uma por guia | Uma com N `TDadosGNRE` |
| Recibo | Um por `GNRE004` | Um compartilhado, dono do lote |
| Resultado individual | Consulta espera uma guia | `resultado/guia`, correlacionado por evidência, nunca por posição |
| PDF | Individual, `GNRE008` | `pdfGuias` do lote; individual só após prova um-para-um |
| Idempotência | Tentativa/bloqueio individual | Intenção, coorte/XML, recibo e recuperação do mesmo lote |
| Persistência | `GNRE004` + `GNRE005` por guia | Lote, participantes e eventos/fila; sem duplicar XML/recibo |
| Timeout | Bloqueia uma guia | Bloqueia todas as participantes |

## Tasks futuras, pequenas e em ordem

1. **Task 65 — correlação e ensaio de Homologação:** vetor canônico, critérios
   de ambiguidade e evidências, sem schema ou automação.
2. **Task 66 — schema/auditoria:** DDL revisado para lote, participantes e
   eventos, sem transmissão.
3. **Task 67 — persistência e estados locais:** migrations/locks/idempotência,
   sem Portal.
4. **Task 68 — XML multi-guia:** montar e validar `TLote_GNRE`/coorte/limites,
   sem transmitir.
5. **Task 69 — recepção e recuperação:** envio único, recibo comum e timeout
   seguro, inicialmente Homologação.
6. **Task 70 — consulta/correlação/projeção:** fila por lote e resultados por
   participante.
7. **Task 71 — PDF de lote em Homologação:** evidência e decisão de associação.
8. **Task 72 — interface real/nomenclatura:** expor lote real e só então
   renomear/substituir a tela da Task 63.

## Validação

Revisão documental e `git diff --check` apenas.
