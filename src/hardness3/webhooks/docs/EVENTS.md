# Eventos

| Evento | Visibilidade | Schema | Aggregate | Payload obrigatório |
| --- | --- | --- | --- | --- |
| `financial_analysis.requested` | `PUBLIC` | `1` | `order` | `order.id`, `order.total`, `customer.id`, `customer.name` e `customer.document` |

O corpo do POST é o payload específico abaixo: não existe envelope JSON adicional. Os cabeçalhos técnicos de entrega (por exemplo, tipo e identificadores do evento) são enviados separadamente e não fazem parte deste JSON.

```json
{
  "order": { "id": 12345, "total": 1500.90 },
  "customer": { "id": 987, "name": "EMPRESA EXEMPLO LTDA", "document": "12345678000190" }
}
```

`aggregate_id` é derivado de `order.id`; a repetição é intencional, pois o envelope técnico identifica o agregado e o payload específico permanece autocontido. O tenant vem de `T005_C004_Id`, nunca de um parâmetro da requisição.

O produtor chama `Webhook::publishForCompany($companyId, 'financial_analysis.requested', $orderId)`. O builder interno consulta T005/D024 com ambos os filtros, monta o único contrato oficial e o Catalog o valida antes da persistência. Nenhum produtor ou cliente fornece o payload público; novos campos compatíveis serão incluídos somente nesse contrato oficial.

As fontes do ERP são `T005.T005_Id`, `T005.T005_Valor_Total` (`DECIMAL(10,2)`) e o cliente `D024` ligado por `T005.T005_D024_Id`. `D024_Nome_Empresa` é o nome integrado para pessoa física e jurídica; `D024_Nome_Fantasia` não é enviado. Para pessoa física (`D024_Flag_Pessoa_Fisica_Juridica = 'F'`) o documento vem de `D024_Cpf`; nos demais casos, de `D024_Cnpj`. Ambos são `varchar(20)` no banco e são publicados como string contendo somente dígitos, preservando zeros iniciais. Campos ausentes ou nulos resultam em rejeição do payload, evitando uma delivery com dados incompletos.

O builder executa uma consulta preparada adicional por publicação, com `T005_Id` e `T005_C004_Id`; não há N+1. Não há evento `financial_analysis.approved` no catálogo atual.

## Documentação administrativa

Cada evento do `Catalog` pode declarar metadata pública de `payload`: formato do corpo, exemplo fictício e campos (caminho, tipo, descrição, obrigatoriedade e nulabilidade). A projeção administrativa expõe essa metadata para a tela de Webhooks; o JavaScript a renderiza genericamente e não mantém contratos por tipo de evento. O contrato do payload é único para todos os consumidores e não pode ser personalizado por endpoint.
