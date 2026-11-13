# Task 20 — Consolidação da fonte oficial de municípios

> Investigação documental concluída em 02/11/2026. Esta task não alterou banco,
> interface, guias, snapshots, transmissão, consulta, `GNRE004` ou `GNRE005`, e
> não reenviou guia alguma. A guia #13 permanece apenas como evidência histórica.

> **Conclusão corrigida pela Task 21 (02/11/2026):** D020 não estava
> incompleta. A evidência em `e998` confirmou Fortaleza `2304400`, Fortim
> `2304459` e Curitiba `4106902`, vinculadas por `D020_D018_Id` a `D018_UF`;
> `D020_UF` pode estar vazio. A falha da Task 19 foi a consulta/suposição sobre
> `D020_UF` e o truncamento pelos cinco primeiros caracteres. Fica substituído
> o plano desta task de catálogo/tabelas GNRE próprio: a Task 21 usa D020 → D018,
> preserva IBGE-7 e deriva os últimos cinco dígitos somente para XML.

## Decisão objetiva

Há uma fonte oficial automatizável para a **identidade territorial**: a tabela de
municípios do **IBGE**. Não há, no Manual GNRE v2.17, nos XSDs ou nos WSDLs
disponíveis, serviço ou download do Portal que forneça o subconjunto efetivamente
**cadastrado/aceito pelo Portal por UF favorecida**. Portanto:

1. importar e versionar o catálogo oficial IBGE como referência canônica, com a
   transformação explícita `IBGE-7 = UF-2 + município-5`;
2. manter uma admissão GNRE separada, por UF favorecida e ambiente, somente quando
   houver publicação/confirmação oficial fiscal rastreável; e
3. bloquear seleção/transmissão quando não houver admissão vigente. Não deduzir
   aceitação por formato, por D020, por tentativa de transmissão nem por cidade
   homônima.

O ponto que explica a rejeição da guia #13 é concreto: `4106902` (Curitiba/PR)
tem `41` como UF e `06902` como código municipal de cinco posições. O valor
enviado, `41069`, tomou os **cinco primeiros** algarismos e não é a operação
descrita pelo IBGE/Manual. A regra correta é extrair os cinco algarismos **após**
o código da UF, e deve ser aplicada no importador de uma fonte versionada — nunca
como truncamento implícito do campo operacional do ERP. O exemplo CE confirma o
risco: Fortaleza `2304400` produz `04400`; Fortim `2304459`, `04459`. Ambas
colidem em `23044` se se tomarem os cinco primeiros dígitos.

Mesmo o `06902` correto só comprova identidade IBGE/formato GNRE; não é prova de
que o Portal possua seu cadastro interno atualizado. A admissão do Portal é uma
segunda dimensão de validade.

## Evidências do contrato GNRE

| Evidência | O que demonstra | Limite |
| --- | --- | --- |
| Manual v2.17, mensagens 145/146 | município deve ser “código do IBGE com 5 caracteres numéricos, sem o código da UF” | define representação, não lista valores aceitos |
| `tiposBasicoGNRE_v1.00.xsd`, `TCodMunIBGE` | padrão `[0-9]{5}` e documentação “Código do Município da tabela do IBGE” | XSD aceita sintaxe; não faz consulta cadastral |
| `dados_gnre_v2.00.xsd` | `contribuinteEmitente/municipio` e `item/contribuinteDestinatario/municipio` usam `TCodMunIBGE` | campos são opcionais no schema; obrigatoriedade vem da configuração/regra de negócio |
| Manual v2.17, retornos 247 e 248 | 247: emitente não cadastrado; 248: destinatário não cadastrado **para a UF favorecida** | 248 prova que a aceitação pode variar pela UF favorecida; nenhuma tabela é exposta |
| Manual v2.17, 4.3.2–4.3.3 e `config_uf_v1.00.xsd` | `TConfigUf` fornece receitas, requisitos e listas de campos; o Manual declara que não traz regras específicas verificadas pela UF | não é catálogo municipal |
| WSDLs capturados | expõem somente `GnreLoteRecepcao/processar`, `GnreResultadoLote/consultar` e `GnreConfigUF/consultar` | não há operação municipal/download no contrato disponível |

O Manual separa explicitamente validação de área de dados (inclusive formato) da
validação de regras de negócio do Portal. A guia #13, recebida e depois
invalidada com 247/248 para `41069`, é a evidência local desta separação: XML
válido não equivale a município cadastrado.

## D018/D020: finalidade e cobertura real

`D018` é o cadastro ERP de UF/estado (inclui, além da sigla/código IBGE,
parâmetros comerciais e fiscais do ERP). `D020` é o cadastro ERP de cidade
vinculado a `D018`, com nome, indicadores operacionais e `D020_Codigo_IBGE`
`int(7)`. O dump de referência `prog/etc/etc008/ERP.sql` declara 29 D018 e
5.592 D020, inclusive `D020_Codigo_IBGE=4106902` para Curitiba; logo o formato
armazenado é o geocódigo IBGE de **sete** dígitos. A própria estrutura contém
campos de IPI, ICMS, ISS, SUFRAMA, região e prazos: sua finalidade é operacional
do ERP, não contrato GNRE.

O dump é um seed de referência, não certificação da base em execução nem uma
publicação do Portal. No ambiente investigado, a lista apresentada pela rotina
GNRE derivada de D020 para CE continha, por exemplo, Fortim e Penaforte e não
Fortaleza, enquanto o Portal de homologação exibia Fortaleza. Isso demonstra que
D020 não pode ser presumido completo, vigente ou equivalente ao catálogo do
Portal. A leitura corrente da aplicação (`GnreMunicipality`) ainda cria opções a
partir de `D018 LEFT JOIN D020`; ela pode continuar servindo para localizar a
empresa/destinatário, mas não para afirmar aceitação GNRE.

Uso permitido após a próxima implementação: D020 sugere a cidade e entrega o
IBGE-7 para reconciliação com o catálogo IBGE. Uso proibido: gerar o código XML
por `substr(..., 0, 5)`, preencher uma admissão GNRE automaticamente ou inferir
que uma cidade está cadastrada no Portal.

## Fontes externas oficiais consultadas

Consulta documental em **02/11/2026**; não foi usada para transmitir, consultar
resultado ou criar guia.

| Fonte | URL | Finalidade | Resultado e limite |
| --- | --- | --- | --- |
| IBGE — Códigos dos Municípios | https://www.ibge.gov.br/explica/codigos-dos-municipios.php | confirmar composição e disponibilidade da tabela | informa código de 7 dígitos, sendo os dois primeiros a UF, e oferece download; é a fonte canônica de identidade, não do cadastro GNRE |
| IBGE — Divisão Territorial Brasileira | https://www.ibge.gov.br/geociencias/organizacao-do-territorio/estrutura-territorial/23701-divisao-territorial-brasileira.html | identificar publicação versionável | disponibiliza relatórios de municípios/códigos por edição; adequado para importação auditável |
| Portal GNRE — endereços oficiais já documentados | https://www.testegnre.pe.gov.br/gnreWS/services/GnreConfigUF e https://www.gnre.pe.gov.br/gnreWS/services/GnreConfigUF | conferir o escopo do único serviço de configuração conhecido | Manual/WSDL local documentam apenas `consultar` configuração de UF. Não há contrato de município. A navegação pública sem certificado retornou 403; isso não demonstra inexistência de tela privada, apenas não fornece catálogo utilizável |

Não foi encontrada, até esta data, URL oficial publicamente documentada de
download, tabela ou endpoint GNRE que liste municípios aceitos. A conclusão é
“**não há fonte oficial automatizável identificada para a admissão Portal/UF**”,
e não “o Portal não possui cadastro”. Caso a SEFAZ/Portal entregue URL, arquivo,
API ou protocolo oficial, ele deve ser registrado como nova fonte e reavaliado;
não se deve fazer descoberta por transmissão de guias.

## Quatro conceitos que não podem ser confundidos

| Conceito | Exemplo | Pode validar localmente? | Prova aceitação do Portal? |
| --- | --- | --- | --- |
| IBGE do ERP | `D020_Codigo_IBGE=4106902` | sim, se corresponde à versão importada do IBGE | não |
| Código permitido pelo XSD | cinco dígitos, por exemplo `06902` | sim, padrão e existência no catálogo IBGE | não |
| Município aceito pelo Portal GNRE | par `(ambiente, UF favorecida, código-5)` admitido | somente se há registro oficial vigente de admissão | sim, dentro de sua vigência/evidência |
| Regras da receita/UF | PR + 100102 + requisitos TConfigUf | sim, no snapshot GNRE007 | não substitui o cadastro municipal |

Para 248, a chave de admissão é necessariamente ao menos `(ambiente, UF
favorecida, código_municipio_5)`. Para 247, o Manual não explicita a chave;
adotar a mesma chave conservadora até evidência oficial permitir generalização.
Receita não entra na chave inicialmente, mas deve ser guardada como contexto da
evidência porque regras futuras podem restringi-la.

## Alternativas avaliadas

| Alternativa | Prós | Contras | Decisão |
| --- | --- | --- | --- |
| Catálogo IBGE importado/versionado + admissão GNRE auditada | determinístico, reproduzível, elimina colisão e preserva origem; permite atualização sem mexer no ERP | não automatiza a admissão enquanto Portal/SEFAZ não publicar a lista | **recomendada** |
| Consulta oficial sob demanda | seria a fonte mais atual e reduziria manutenção | não há endpoint/contrato municipal identificado; não implementar sondagem | habilitar somente quando houver contrato oficial |
| Seleção manual controlada | viabiliza cidade excepcional sem inventar regra | esforço fiscal e risco se faltar evidência | fallback obrigatório para alimentar admissão auditada, nunca campo livre transmissível |
| D018/D020 como fonte GNRE | já existe e facilita UX | cobertura variável, código de sete dígitos e sem vínculo com Portal; truncamento gera erro/colisão | somente sugestão/localização |

## Modelo mínimo proposto para a próxima task

Não criar nesta task. Nomes são indicativos e devem respeitar o padrão das
migrations quando aprovados.

`GNRE008_Fonte_Municipio`: `id`, `tipo` (`IBGE`/`PORTAL_SEFAZ`), `url_ou_protocolo`,
`arquivo_nome`, `sha256`, `versao_publicada`, `obtido_em`, `vigencia_inicio`,
`vigencia_fim`, `observacao`, usuário/auditoria de criação. Um registro é
imutável.

`GNRE009_Municipio_Referencia`: `fonte_id`, `uf_ibge_2`, `uf_sigla`,
`codigo_ibge_7`, `codigo_municipio_5`, `nome_oficial`, `vigente`; unicidade por
fonte e IBGE-7, e por fonte/UF/código-5. O `codigo_municipio_5` é derivado e
armazenado no importador com validação de que os dois primeiros dígitos do
IBGE-7 são a UF; não por substring aplicada à guia.

`GNRE010_Admissao_Municipio`: `fonte_id`, `ambiente`, `uf_favorecida`,
`municipio_referencia_id`, `status` (`CONFIRMADO`/`SUSPENSO`), vigência,
`evidencia_local_ou_protocolo`, `confirmado_por`, timestamps e motivo. Deve
preservar histórico; a unicidade de registro vigente evita duas decisões
conflitantes para a mesma chave.

Atualização: importar nova edição IBGE como nova fonte, validar diferenças,
publicar após revisão fiscal e manter as versões antigas consultáveis. Para
admissão, importar arquivo/API oficial se e quando existir; sem ele, registrar
confirmação fiscal com protocolo/URL/cópia da evidência. Fallback seguro: não
oferecer cidade sem admissão vigente e encaminhar à confirmação fiscal; nunca
usar “última cidade aceita”, completar por prefixo ou reenviar guia.

Na interface, D020 pode pré-selecionar/sugerir o par IBGE; a lista transmissível
vem exclusivamente de `GNRE010` ligada a `GNRE009`, filtrada por UF favorecida e
ambiente. Se a cidade do ERP não tiver mapeamento/admissão, mostrar o nome e
código ERP como contexto, bloquear a transmissão e oferecer solicitação de
validação fiscal com os dados mínimos da evidência. Nada é gravado como
aceito por escolha manual do usuário comum.

Migração: não reescrever `GNRE003_Destinatario_Codigo_Municipio`, `C004`, D018
ou D020; não tocar XML, snapshots ou eventos históricos. Registros existentes
permanecem legados e são exibidos com sua origem/valor histórico. Novas guias
só usam catálogo novo depois da publicação da primeira fonte/admissão. Uma
eventual reconciliação é relatório não destrutivo, com decisão fiscal explícita.

Validações locais: formato de cinco dígitos, consistência IBGE-7/UF/código-5,
existência/vigência do catálogo e admissão, e requisitos provenientes do snapshot
TConfigUf. Permanecem do Portal: estado atual de seu cadastro e regras internas
da UF, autorização, demais campos de negócio e resultado de homologação.

## Matriz inicial de aceitação — PR / receita 100102

Esta matriz é um roteiro de pré-condições; não declara município algum aceito e
não autoriza transmissão nesta task.

| Dimensão | Evidência exigida antes de homologar | Validação local | Resultado esperado na futura homologação |
| --- | --- | --- | --- |
| UF favorecida | snapshot TConfigUf homologação vigente para PR | PR e receita 100102 presentes no snapshot | processamento sujeito ao Portal |
| Emitente | cadastro da empresa + município IBGE importado + admissão vigente para PR | identificação/endereço conforme snapshot; código-5 ligado ao IBGE-7 | ausência de 247 somente se a admissão oficial estiver correta |
| Destinatário | quando a receita/snapshot o exigir: identificação, razão social e município com admissão PR | obrigatoriedade e sintaxe; cidade filtrada por PR | ausência de 248 somente se a admissão oficial estiver correta |
| Município | fonte IBGE versionada e evidência Portal/SEFAZ da chave `(homologação, PR, município-5)` | não aceitar D020 isolado, sete dígitos nem prefixo | não afirmar cidade aceita antecipadamente |
| Campos extras | snapshot PR/100102 e evidência fiscal do valor | código/tipo/tamanho/obrigatoriedade do snapshot | demais rejeições só podem ser avaliadas pelo Portal |
| Valores | valor e tipo exigidos pelo snapshot, origem fiscal e regras da guia | número positivo, tipo permitido e consistência de totais | não usar valor de teste inventado; resultado global depende do Portal |

O critério de sucesso municipal da futura execução autorizada é: retorno individual
sem 247 e 248 para as chaves formalmente admitidas. Isso não equivale a afirmar
guia emitida ou aprovada em todos os demais requisitos.

## Plano da Task 21 — implementação (não iniciada)

1. Submeter a presente decisão e a primeira evidência oficial de admissão à
   aprovação fiscal; sem ela, não iniciar migration.
2. Criar somente as tabelas de fonte/catálogo/admissão e importador idempotente
   de uma edição IBGE identificada por hash, sem alterar D018/D020 ou dados GNRE
   existentes.
3. Remover o truncamento `substr(..., 0, 5)` do caminho GNRE e trocar a origem
   transmissível pela admissão versionada; D020 permanece sugestão.
4. Adaptar a interface para estados “sugerido”, “mapeado” e “sem admissão”, com
   bloqueio explicável e auditoria da confirmação fiscal.
5. Cobrir com testes: Curitiba `4106902 → 06902`, Fortaleza/Fortim sem colisão,
   ausência de admissão, vigência, ambiente e preservação de guias legadas.
6. Somente após aprovação expressa, executar uma nova guia de homologação; não
   alterar/retransmitir a #13. Registrar seu resultado como evidência limitada.

## Handoff

Arquivos alterados: este documento e `docs/PROJECT_STATUS.md`. Não houve
migration, teste funcional, chamada SOAP, alteração de banco ou artefato de
guia. A Task 19 não é corrigida nesta task; sua descrição de “cinco primeiros”
fica formalmente superada por esta análise e deve ser ajustada apenas na task de
implementação aprovada, junto com o código que a materializará.
