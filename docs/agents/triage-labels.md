# Rótulos de triagem

Os skills falam de cinco papéis canônicos. Este arquivo mapeia cada papel para a string de rótulo usada no rastreador deste repositório.

As strings não se traduzem: o skill grava e procura exatamente estes tokens na linha `Status:`.

| Papel no skill   | Rótulo neste repositório | Significado                                      |
| ---------------- | ------------------------ | ------------------------------------------------ |
| `needs-triage`   | `needs-triage`           | Quem mantém o projeto ainda precisa avaliar      |
| `needs-info`     | `needs-info`             | Esperando mais informação de quem reportou       |
| `ready-for-agent`| `ready-for-agent`        | Especificado, pronto para um agente sem supervisão |
| `ready-for-human`| `ready-for-human`        | Exige implementação de uma pessoa                |
| `wontfix`        | `wontfix`                | Não será feito                                   |

Quando um skill cita um papel (por exemplo, "apply the AFK-ready triage label"), use a string da coluna do meio.

A coluna do meio é o vocabulário real do rastreador. Só mude se o time já usar outros nomes, para o skill aplicar o rótulo existente em vez de criar um duplicado.
