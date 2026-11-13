# Task 62 — preenchimento assistido de dados GNRE a partir da NF

## Mapeamento auditado

Fonte exclusiva: `T007` autorizado da empresa da guia. Chave: `T007_Chave_Acesso_Nfe`; emissão: `T007_Data_Emissao`; modelo: `T007_Origem_Modelo`; destinatário: `T007_Cliente_Razao`, `T007_Cliente_CNPJ_CPF`, `T007_Cliente_Inscricao_Estadual`, `T007_Cliente_Estado` e `T007_Cliente_Codigo_Cidade_IBGE`.

O preenchimento usa o `GNRE007_Regras_JSON` vinculado à própria guia. Ele é
executado no limite da mesma transação que grava `GNRE001`, `GNRE002`,
`GNRE003` e `GNRE006`: ST, DIFAL e FCP isolado projetam antes do insert do
item; a composição RJ chama explicitamente `projectionForGuide()` depois dos
dois vínculos e reutiliza `persistItem()` para o item DIFAL, antes de gravar o
valor FCP tipo 12 e do commit da guia única.
Qualquer falha de persistência provoca rollback integral. Preenche data de
pagamento quando exigida, documento somente se o snapshot oferecer opção
oficialmente descrita como NF-e e a NF tiver chave de 44 dígitos/modelo 55,
mês/ano quando a referência é exigida, e CNPJ/CPF, razão e município quando
completos. A IE não é usada como identificação e não recebe situação inferida.

O tipo documental é escolhido somente entre opções do snapshot: uma única
opção compatível com chave NF-e de 44 dígitos/modelo 55, reconhecendo as
descrições oficiais `CHAVE DA NFe`, `CHAVE DO DFe`, `Nota Fiscal Eletrônica` e `Nota Fiscal`.
Nenhum código é
fixado. O resumo do lote prioriza a mensagem retornada pela criação e informa
Criada, Já existente, Bloqueada por regra ou Falha segura de criação.

Correção de integridade: o `INSERT` assistido de `GNRE003` possui 15 valores
efetivos e usa o bind `iissssssssssssi` (guia, documento, 12 textos e usuário).
O contrato executável da Task 62 verifica a igualdade entre placeholders,
assinatura e argumentos para ST, DIFAL, FCP e a composição RJ que reutiliza o
mesmo item DIFAL.

Quando o snapshot possuir exatamente um campo adicional oficial com título
equivalente a **Data de Emissão** — inclusive se a UF o declarar como texto —
ele recebe a data de emissão persistida da NF, nunca a data atual, no formato
XML `aaaa-mm-dd`. Pagamento e vencimento mantêm a data da criação do rascunho.
Na interface, esse campo semântico é apresentado com seletor de data; o tipo
declarado pelo snapshot continua preservado no contrato e na persistência.
Convênio, detalhamento e demais adicionais sem mapeamento
semântico ficam vazios para revisão manual.

Para ST, o principal vem de `T007_Valor_ICMS_Substituicao_Retencao` e o
FCP-ST vem exclusivamente do total autorizado `T007_Valor_FCP_ST`. Quando o
snapshot da receita permite FCP, o segundo é gravado como valor GNRE tipo `12`
no mesmo item; `T008_Valor_FCP_ST`/`D006` servem apenas à elegibilidade por
CFOP e jamais são somados ou usados como valor monetário GNRE. FCP de partilha
continua sendo obrigação distinta, com origem
`T007_Valor_Total_FCP_Partilha`.

No RJ, FCP-partilha só é mantido no plano quando há DIFAL positivo, para compor
a única guia 100102. Sem DIFAL, ele não tenta a receita 100129; FCP-ST segue
na guia ST, quando aplicável.

## Roteiro

1. Gere ST, BA DIFAL, FCP fora do RJ e RJ DIFAL+FCP e confirme receitas/valores já definidos na Task 61.
2. Confira Guia simples fixa, pagamento, chave/documento e referência quando o snapshot exigir.
3. Teste CNPJ, CPF com/sem IE e snapshot que não exige destinatário/referência/documento; nenhum valor deve aparecer por suposição.
4. Na guia, confira origem NF e tributo; complete somente os avisos de revisão antes de qualquer XML.
