# Task 43 — validação de pagamento e vencimentos por item

## Entrega

Foi criada a política pura `GuidePaymentDueDatePolicy`. A guia mantém uma única
`GNRE001_Data_Pagamento`; cada `GNRE003_Data_Vencimento` continua pertencendo ao
seu item. Múltiplos vencimentos por item são permitidos. A única restrição é que
o pagamento global não seja posterior ao menor vencimento informado.

No POST de inclusão/edição, a política recebe os vencimentos persistidos sob
`FOR UPDATE` mais o vencimento proposto e roda dentro da transação já existente,
antes de qualquer alteração em `GNRE001`, `GNRE003`, `GNRE006` ou `GNRE002`.
Uma falha lança erro público e o rollback existente preserva todos os registros.

O `GnreXmlGenerator` aplica a mesma política aos dados persistidos. Como a
transmissão gera/valida o XML antes de criar `GNRE004` e antes de chamar o
cliente de recepção, uma guia legada inconsistente é bloqueada tanto na geração
quanto na transmissão, sem ajuste automático de datas.

Não há regra por UF, receita ou tipo nesta política. Não houve migration,
alteração de schema, banco, guia real, XML persistido nem chamada ao Portal.

## Testes e roteiro manual

`task43-payment-due-date-synthetic.php` cobre igualdade, menor data, ausência
de vencimento, data inválida, rollback simulado, XML e bloqueio legado. Sem
Portal: crie dois itens com vencimentos 10/11 e 15/11, salve pagamento em 10/11
(aceito), tente 11/11 (erro e formulário preservado), gere XML e confira que
há dois `dataVencimento` e um único `dataPagamento`.

## Correção de diálogo e isolamento visual

O diálogo “Corrigir vencimento” é identificado exclusivamente pela constante
`GuidePaymentDueDatePolicy::PAYMENT_AFTER_DUE_MESSAGE`, marcada no flash pelo
servidor. Outros erros continuam visíveis dentro do modal sem instrução sobre
vencimento. As transformações visuais temporárias por `ob_start`/regex foram
removidas desta correção; não houve alteração fiscal, Portal ou persistência.
