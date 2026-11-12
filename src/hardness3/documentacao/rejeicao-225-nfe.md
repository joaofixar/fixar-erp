# Rejeição 225 da NF-e — mensagens amigáveis por tag

> **Situação:** implementado na fila de transmissão da NF-e. O tradutor está em `bibliotecas/classes/NFeRejeicao225.php` e é aplicado por `prog/ven/ven012/grid_func-ajax.php` quando o retorno interno é `201/202` e o código da SEFAZ é `225`.

## 1. Objetivo

Quando a SEFAZ retornar o código **225 — Falha no Schema XML da NF-e**, o sistema deve preservar o retorno técnico original e também apresentar uma orientação simples, indicando:

- o campo ou grupo com provável problema;
- onde o usuário deve conferir a informação;
- o formato esperado;
- o item da nota, duplicata ou pagamento, quando for possível identificá-lo.

Esta especificação se aplica à NF-e/NFC-e 4.00. Os formatos e domínios devem ser obtidos do pacote de Schemas vigente no sistema, e não ficar somente gravados em textos fixos.

## 2. Observação técnica importante

O código 225 é uma rejeição de **estrutura ou formato do XML (XSD)**. Algumas inconsistências cadastrais também listadas neste documento — por exemplo, município que não pertence à UF, CEP inexistente, IE inválida, NCM inexistente, CFOP incompatível ou dígito verificador do GTIN — podem passar no Schema e gerar outra rejeição de negócio da SEFAZ.

Assim, o catálogo abaixo deve servir para:

1. traduzir o 225 quando a tag aparecer no retorno;
2. validar o XML localmente antes do envio;
3. futuramente melhorar mensagens de outras rejeições relacionadas.

Não se deve substituir o texto oficial da SEFAZ. A orientação amigável deve ser exibida junto do código e o retorno técnico deve continuar disponível em “Detalhes técnicos”.

## 3. Mensagem padrão ao usuário

> **NF-e rejeitada: há uma informação fora do formato esperado.**
> Campo: `{nome amigável}` (`<{tag}>`)
> Onde verificar: `{tela/grupo/cadastro}`
> Orientação: `{correção sugerida}`
> `{contexto opcional: item, duplicata, pagamento ou grupo}`
> Após corrigir, gere o XML e transmita a nota novamente.

Exemplo:

> **NF-e rejeitada: NCM fora do formato esperado.**
> Campo: NCM (`<NCM>`) — item 3
> Onde verificar: produto do item 3 / cadastro do produto
> Orientação: confira o código enviado. Em regra são 8 algarismos; o Schema também prevê `00` para situações específicas sem produto.
> Detalhes técnicos: 225 — Falha no Schema XML da NF-e.

Se nenhuma tag puder ser reconhecida:

> **NF-e rejeitada por erro na estrutura do XML.** Não foi possível identificar automaticamente o campo. Consulte os detalhes técnicos do retorno 225 e acione o suporte informando a nota e o XML gerado.

## 4. Fluxo de identificação

1. Confirmar que o `cStat` retornado é `225`. No fluxo atual da fila, isso normalmente chega ao front-end como `T007_TX_Resposta_Codigo = 202`, `T007_TX_Resposta_Extra = 225` e o `xMotivo` em `T007_TX_Retorno_Sefaz`.
2. Ler o `xMotivo` e demais detalhes retornados pelo autorizador.
3. Extrair todas as tags citadas, aceitando formatos como `Element '{...}NCM'`, `Elemento 'NCM'`, `<NCM>` e `Expected is (... NCM ...)`.
4. Remover o namespace e manter apenas o nome local da tag.
5. Usar o caminho XML ou a tag pai para diferenciar nomes repetidos, como `CNPJ`, `IE`, `xNome`, `cMun`, `xPed`, `CST` e `nSerie`.
6. Quando houver erro local do XSD, usar a linha indicada para localizar o nó e montar o XPath. Em `<det nItem="...">`, exibir o número do item.
7. Consultar o catálogo por **caminho + tag**; usar somente a tag como segunda opção.
   Quando o retorno trouxer o valor recusado, localizar esse valor no XML e priorizar a tag correspondente quando ela for inequívoca; alguns retornos SAX citam apenas a última tag processada.
8. Se o erro disser que um elemento era esperado, informar também a possível tag ausente ou fora de ordem.
9. Escapar todo conteúdo antes de inserir a mensagem no HTML. Nunca renderizar diretamente o `xMotivo` ou valores do XML.

Prioridade sugerida para a mensagem: tag inválida informada pelo XSD → tag esperada/ausente → grupo pai → mensagem genérica.

## 5. Catálogo de mensagens

### 5.1 Identificação da nota — `<ide>`

| Tag | Mensagem amigável e onde verificar |
|---|---|
| `dhEmi` | **Data/hora de emissão inválida.** Verifique a emissão da nota. Envie data, hora, segundos e fuso, por exemplo `2026-07-16T16:00:00-03:00`. |
| `dhSaiEnt` | **Data/hora de saída ou entrada inválida.** Verifique a saída/entrada da nota. A tag contém data e hora em um único valor e exige o fuso horário. Se não houver informação, a tag opcional deve ser omitida. |
| `tpImp` | **Formato de impressão do DANFE inválido.** Verifique o modelo/formato de impressão. No leiaute 4.00 usado atualmente pelo projeto, o domínio XSD é `0`, `1`, `2`, `3`, `4` ou `5`. |
| `finNFe` | **Finalidade da NF-e inválida.** Verifique a finalidade da nota: `1` normal, `2` complementar, `3` ajuste ou `4` devolução/retorno. |
| `refNFe` | **Chave da NF-e referenciada inválida.** Verifique as notas referenciadas. A chave deve ter 44 posições e seguir o padrão do Schema vigente. Com a evolução para CNPJ alfanumérico, não se deve fixar a validação como “44 algarismos” em todos os ambientes. |
| `refCTe` | **Chave do CT-e referenciado inválida.** Verifique os documentos referenciados; não envie espaços, pontuação ou tamanho incorreto. |
| `nECF` | **Número do ECF referenciado inválido.** Verifique o cupom fiscal referenciado e remova formatação não aceita. |
| `nCOO` | **Número do COO referenciado inválido.** Verifique o contador do cupom fiscal e o limite/formato exigido pelo Schema. |
| `indIntermed` | **Indicador de intermediador inválido.** Verifique se a operação ocorreu em site/plataforma de terceiros. Use somente o domínio do Schema (`0` ou `1` no leiaute aplicável). |

### 5.2 Emitente e destinatário — `<emit>` e `<dest>`

| Tag/caminho | Mensagem amigável e onde verificar |
|---|---|
| `emit/CNPJ`, `dest/CNPJ` | **CNPJ fora do formato esperado.** Verifique o cadastro da empresa ou do cliente, conforme o grupo. Não envie máscara, espaços ou valor vazio. Validar pelo Schema vigente: o pacote existente no projeto já prevê 12 posições alfanuméricas seguidas de 2 dígitos. Dígitos verificadores e sequências inválidas exigem validação adicional. |
| `emit/CPF`, `dest/CPF` | **CPF fora do formato esperado.** Verifique o cadastro da empresa/cliente. Envie 11 algarismos, sem pontos ou traço, além de validar os dígitos verificadores antes da geração. |
| `emit/IE`, `dest/IE` | **Inscrição Estadual fora do formato esperado.** Verifique a IE e a UF no cadastro correspondente. Remova pontos e traços. `ISENTO` só deve ser enviado quando o grupo, a UF e a regra fiscal permitirem. |
| `cMun` | **Código do município inválido.** Verifique a cidade no endereço e seu código IBGE. Além do formato, valide se o município existe e pertence à UF informada. |
| `CEP` | **CEP fora do formato esperado.** Verifique o endereço. Envie 8 algarismos, sem hífen; a existência do CEP deve ser uma validação cadastral adicional. |
| `nro` | **Número do endereço não informado ou inválido.** Preencha o número; quando o imóvel não tiver número, use o padrão cadastral definido pelo sistema, como `S/N` ou `0`, desde que aceito pelo Schema vigente. |
| `emit/IM` | **Inscrição Municipal inválida ou ausente.** Verifique os dados municipais do emitente. Se houver CNAE no XML, a sequência do Schema exige a IM antes dele. |
| `emit/CNAE` | **CNAE inválido.** Verifique o cadastro fiscal do emitente. No Schema local, são 7 algarismos e a tag somente pode ser gerada dentro do grupo iniciado pela IM. A IM pode existir sem CNAE; o contrário não. |

### 5.3 Produtos — `<det>/<prod>`

| Tag | Mensagem amigável e onde verificar |
|---|---|
| `NCM` | **NCM fora do formato esperado.** Verifique o cadastro do produto e o item indicado. Normalmente são 8 algarismos; o Schema local também admite `00` em situações específicas sem produto. A existência do código exige validação de tabela. |
| `CEST` | **CEST fora do formato esperado.** Envie 7 algarismos, sem pontos, espaços ou traço. Verifique se o produto realmente está sujeito ao CEST. |
| `CFOP` | **CFOP fora do formato esperado.** Envie 4 algarismos e confira a operação do item. O Schema valida o formato; existência e compatibilidade fiscal devem ser validadas à parte. |
| `cEAN`, `cEANTrib` | **GTIN/código de barras fora do formato esperado.** Use `SEM GTIN` quando aplicável ou um GTIN no tamanho aceito pelo Schema, sem letras ou formatação. Validar o dígito verificador antes do envio. |
| `uCom`, `uTrib` | **Unidade de medida inválida.** Verifique as unidades comercial e tributável do item. Respeite o limite do Schema (6 caracteres no Schema local) e prefira texto simples, como `M3` ou `UN`. |
| `qCom`, `vUnCom` | **Quantidade ou valor unitário fora do formato.** Gere o XML com ponto decimal, sem separador de milhar, e respeite o total de casas permitido para cada tag. |
| `xPed` em `prod` | **Número do pedido do item inválido.** Verifique o pedido de compra vinculado ao produto. No Schema local, o limite é 15 caracteres. |
| `nItemPed` | **Número do item do pedido inválido.** Informe de 1 a 6 algarismos, sem `#`, `&`, espaços ou outros símbolos. |

### 5.4 Impostos — `<ICMS>`, `<PIS>`, `<COFINS>` e `<IPI>`

| Tag/grupo | Mensagem amigável e onde verificar |
|---|---|
| `pICMS`, `vICMS` e demais valores/alíquotas | **Valor de imposto fora do formato esperado.** Verifique a tributação do item. Use ponto decimal e respeite a quantidade de inteiros e decimais definida para a tag. |
| `CST` | **CST inválido ou no grupo tributário incorreto.** Verifique o imposto e o regime tributário do item. Como `CST` aparece em vários impostos, a mensagem deve incluir o grupo pai (`ICMS`, `IPI`, `PIS` ou `COFINS`). |
| `CSOSN` | **CSOSN inválido ou no grupo de ICMS incorreto.** Verifique o regime tributário da empresa e a tributação do item. |
| `IPI`, `IPITrib`, `IPINT` | **Estrutura do IPI inválida.** Verifique o enquadramento e o grupo tributado/não tributado. O XML deve conter somente a opção de filhos permitida pelo Schema e na ordem correta. |
| `cEnq` | **Código de enquadramento legal do IPI inválido.** Verifique a tributação do produto e envie o código no formato exigido pelo Schema. |

### 5.5 Transporte — `<transp>`

| Tag | Mensagem amigável e onde verificar |
|---|---|
| `modFrete` | **Modalidade do frete inválida.** Verifique o transporte da nota. No leiaute local, os valores válidos são `0`, `1`, `2`, `3`, `4` ou `9`; não é qualquer valor entre 0 e 9. |
| `placa` | **Placa do veículo fora do formato esperado.** Envie a placa sem hífen ou espaços e confira o padrão antigo/Mercosul aceito pelo Schema. |

### 5.6 Informações adicionais — `<infAdic>`

| Tag | Mensagem amigável e onde verificar |
|---|---|
| `infCpl` | **Informações complementares fora do formato esperado.** Verifique o texto adicional, seu tamanho e caracteres de controle. Quebras de linha devem ser normalizadas conforme a regra do gerador. `&`, `<` e `>` podem existir no conteúdo somente quando corretamente escapados no XML (`&amp;`, `&lt;`, `&gt;`); `@` e `%` não são, por si sós, inválidos no XSD. |

O gerador deve usar uma biblioteca XML para criar o texto, nunca concatenar conteúdo do usuário diretamente nas tags.

### 5.7 Comércio exterior e compras — `<exporta>`, detalhes de exportação e `<compra>`

| Tag/caminho | Mensagem amigável e onde verificar |
|---|---|
| `exporta/UFSaidaPais` | **UF de saída do país inválida.** Verifique a UF de embarque ou transposição de fronteira e use a sigla prevista no domínio do Schema. |
| `exporta/xLocExporta` | **Local de exportação inválido.** Preencha o local e respeite o limite de tamanho (1 a 60 caracteres no Schema local). |
| `exporta/xLocDespacho` | **Local de despacho inválido.** Revise o texto e respeite o limite do Schema; a tag opcional vazia deve ser omitida. |
| `detExport/cExportador` | **Código do exportador inválido.** Verifique o código no item exportado e respeite o tamanho/formato do Schema vigente. |
| `compra/xNEmp` | **Nota de empenho fora do formato esperado.** Verifique a nota de empenho; no Schema local o limite é 22 caracteres. |
| `compra/xPed` | **Pedido de compra fora do formato esperado.** Verifique o pedido geral da nota; no Schema local o limite é 60 caracteres. Não confundir com `prod/xPed`, cujo limite local é 15. |

### 5.8 Produtos específicos — `<veicProd>`, `<rastro>`, `<med>`, `<arma>` e `<comb>`

| Tag/caminho | Mensagem amigável e onde verificar |
|---|---|
| `veicProd/chassi` | **Chassi fora do formato esperado.** Verifique os dados do veículo e o tamanho/formato exigido pelo Schema vigente. |
| `veicProd/nSerie` | **Número de série do veículo inválido.** Verifique os dados específicos do veículo; no Schema local, o limite é de 1 a 9 caracteres. |
| `rastro/nLote` | **Número do lote inválido.** Verifique a rastreabilidade do produto/medicamento. No leiaute 4.00 local, o lote fica no grupo `<rastro>`, não diretamente em `<med>`. Não envie tag vazia; remova espaços nas extremidades e respeite o limite de 20 caracteres. |
| `med/vPMC` | **Preço Máximo ao Consumidor inválido.** Use ponto decimal e no máximo as casas permitidas pelo Schema. |
| `arma/nSerie` | **Número de série da arma inválido.** Verifique o cadastro específico; no Schema local, o limite é de 1 a 15 caracteres. |
| `comb/cProdANP` | **Código ANP fora do formato esperado.** Verifique o combustível e envie o código no tamanho exigido pelo Schema. A existência/vigência deve ser conferida na tabela ANP. |

### 5.9 Cobrança e pagamento — `<cobr>` e `<pag>`

| Tag/caminho | Mensagem amigável e onde verificar |
|---|---|
| `dup/nDup` | **Número da duplicata inválido.** Verifique as parcelas, remova espaços nas extremidades e respeite o limite do Schema (até 60 caracteres no Schema local). |
| `dup/dVenc` | **Data de vencimento inválida.** Use uma data real no formato `AAAA-MM-DD`. |
| `dup/vDup` | **Valor da duplicata inválido.** Use ponto decimal, sem separador de milhar, e no máximo duas casas decimais. |
| `detPag/tPag` | **Forma de pagamento fora do formato esperado.** A tag tem dois algarismos no Schema local, mas o código também deve existir na tabela vigente; portanto, não considerar automaticamente todo o intervalo `01` a `99` válido. |
| `card/tpIntegra` | **Tipo de integração do pagamento inválido.** Use `1` para integrado ou `2` para não integrado. |
| `card/CNPJ` | **CNPJ da instituição de pagamento inválido.** Verifique a credenciadora/instituição, sem máscara ou espaços, conforme o Schema vigente. |

### 5.10 Retirada e entrega — `<retirada>` e `<entrega>`

| Tag | Mensagem amigável e onde verificar |
|---|---|
| `xNome` | **Nome do local de retirada/entrega inválido.** Verifique o grupo indicado, remova espaços nas extremidades e respeite o tamanho. Aspas e acentos graves não são necessariamente inválidos quando o XML é gerado corretamente. |
| `xBairro` | **Bairro não informado ou fora do tamanho esperado.** Verifique o endereço de retirada/entrega; no Schema local, o texto deve ter de 2 a 60 caracteres. |
| `cPais` | **Código do país fora do formato esperado.** Verifique o país no endereço e utilize o código oficial configurado para a NF-e (por exemplo, `1058` para Brasil). A existência deve ser validada pela tabela vigente. |

### 5.11 Intermediador — `<infIntermed>`

| Tag | Mensagem amigável e onde verificar |
|---|---|
| `CNPJ` | **CNPJ do intermediador inválido.** Verifique a plataforma, marketplace ou intermediador informado; não confundir com o CNPJ do emitente, destinatário ou cartão. |
| `idCadIntTran` | **Identificador no intermediador inválido.** Verifique o cadastro usado na plataforma. No Schema local, o valor deve ter de 2 a 60 caracteres e não pode começar ou terminar com espaço. |

## 6. Regras de implementação

- Manter o retorno bruto em `T007_TX_Retorno_Sefaz` para auditoria e suporte.
- Gerar a orientação amigável em uma camada própria, sem alterar a resposta oficial armazenada.
- Aplicar o tradutor no endpoint que prepara a resposta da fila de NF-e, hoje concentrado em `prog/ven/ven012/grid_func-ajax.php` e exibido por `bibliotecas/funcoesCallback/txSefaz.php`. O gatilho atual deve considerar `T007_TX_Resposta_Codigo = 202` junto de `T007_TX_Resposta_Extra = 225`.
- Reaproveitar o diagnóstico na validação local: `hardness/class/nfe4lote.php` já executa `DOMDocument::schemaValidate()` e reúne os erros da `libxml` antes do envio em lote. Esses erros, hoje tratados como código interno `109`, podem receber o mesmo catálogo amigável.
- Validar o XML com o mesmo pacote XSD vigente no autorizador. O projeto contém Schemas em `hardness/class/PL/PL_Nfe`, mas eles precisam de processo de atualização/versionamento.
- Não mapear apenas pelo nome `CNPJ`, `CST`, `xNome`, `cMun`, `xPed` ou `nSerie`; usar o caminho XML.
- Não expor valores sensíveis completos na mensagem. CNPJ/CPF e chaves devem ser mascarados quando aparecerem no diagnóstico.
- Limitar a mensagem principal a uma correção objetiva. Quando houver vários erros, mostrar o primeiro e disponibilizar uma lista em “Ver todos os problemas”.
- Registrar em log: nota, `cStat`, tags extraídas, XPath, item, mensagem escolhida e retorno técnico, sem duplicar dados pessoais desnecessários.

Estrutura sugerida do retorno interno:

```php
[
    'codigo' => 225,
    'titulo' => 'NCM fora do formato esperado',
    'tag' => 'NCM',
    'caminho' => 'NFe/infNFe/det/prod/NCM',
    'item' => 3,
    'ondeVerificar' => 'Produto do item 3 / cadastro do produto',
    'orientacao' => 'Confira o código NCM e gere o XML novamente.',
    'retornoTecnico' => $xMotivo,
]
```

## 7. Critérios de aceite

1. Dado retorno 225 contendo `NCM`, o sistema informa “NCM fora do formato esperado” e o item, quando localizável.
2. Dado retorno 225 contendo `CNPJ` dentro de `<card>`, a mensagem aponta a instituição de pagamento, não o emitente.
3. Dado erro “elemento esperado”, o sistema informa a tag ausente ou fora de ordem.
4. Dado retorno com mais de uma tag, o sistema apresenta o primeiro problema e permite consultar os demais.
5. Dado retorno sem tag reconhecível, o sistema mostra a mensagem genérica e preserva os detalhes técnicos.
6. Textos do XML e da SEFAZ não são interpretados como HTML ou JavaScript.
7. A mensagem amigável não substitui nem modifica `T007_TX_Retorno_Sefaz`.
8. Os testes cobrem ao menos: data/hora, tag fora de ordem, valor decimal com vírgula, NCM, CNPJ em três contextos, imposto/CST, pagamento e `infCpl` com caracteres XML.

## 8. Referências técnicas

- Portal Nacional da NF-e — Manual de Orientação ao Contribuinte, Anexo I, leiaute e regras de validação da NF-e/NFC-e.
- Portal Nacional da NF-e — pacote de Schemas XML NF-e/NFC-e vigente.
- Schemas atualmente presentes no projeto: `hardness/class/PL/PL_Nfe/nfe_v4.00.xsd`, `leiauteNFe_v4.00.xsd` e `tiposBasico_v4.00.xsd`.

> Revisar este catálogo sempre que o pacote de Schemas ou uma Nota Técnica alterar tags, tamanhos, padrões ou domínios.

## 9. Plano de teste

### 9.1 Teste determinístico da mensagem, sem transmitir

Este deve ser o primeiro teste. Em uma base exclusivamente de desenvolvimento, simular para uma nota descartável o mesmo estado gravado pelo fluxo real:

- `T007_TX_Resposta_Codigo = 202`;
- `T007_TX_Resposta_Extra = 225`;
- `T007_TX_Retorno_Sefaz` contendo uma mensagem técnica com a tag, por exemplo: `Element '{http://www.portalfiscal.inf.br/nfe}NCM': [facet 'pattern'] The value '123' is not accepted by the pattern.`

Abrir a fila e verificar se a interface apresenta “NCM fora do formato esperado”, o local de correção e os detalhes técnicos. Guardar os valores anteriores e restaurá-los ao final. Nunca executar essa simulação em produção.

### 9.2 Teste ponta a ponta com a SEFAZ

Para obter um 225 real:

1. Confirmar que a empresa e a nota estão no ambiente de homologação: `T007_Ambiente_NFe = 2`.
2. Não confundir homologação com o modo de treinamento (`C020_NFe_Treinamento = S`), pois o fluxo de treinamento pode gerar protocolo fictício e não transmitir à SEFAZ.
3. Criar uma NF-e modelo 55 descartável, sem protocolo e ainda não transmitida.
4. Escolher um item dessa nota e guardar seu NCM correto.
5. Alterar somente o campo do item da nota `T008_Classificacao_Fiscal` para `123`. Não alterar o cadastro mestre do produto, evitando afetar outras notas.
6. Gerar e transmitir normalmente. O XML deve conter `<NCM>123</NCM>` e ser assinado já com esse conteúdo.
7. Conferir o retorno persistido: código interno `202`, extra `225` e o motivo técnico em `T007_TX_Retorno_Sefaz`.
8. Verificar a mensagem amigável, o item indicado e a opção de consultar os detalhes técnicos.
9. Corrigir o NCM, solicitar a regeneração completa do XML e retransmitir, ou descartar a nota de homologação conforme o procedimento interno.

Não editar manualmente o XML depois da assinatura. Qualquer alteração no XML assinado invalida a assinatura digital e pode gerar uma rejeição diferente, impedindo o teste do 225.

### 9.3 Resultado esperado

> **NF-e rejeitada: NCM fora do formato esperado.**
> Campo: NCM (`<NCM>`) — item `{nItem}`
> Onde verificar: produto do item / cadastro fiscal do produto
> Orientação: confira o NCM informado e gere o XML novamente.
> Detalhes técnicos: 225 — Falha no Schema XML da NF-e.

Depois do NCM, repetir a matriz de testes sem depender da SEFAZ, usando retornos simulados para `dhEmi`, `CNPJ` em diferentes grupos, `CST`, `modFrete`, `tPag` e `infCpl`.
