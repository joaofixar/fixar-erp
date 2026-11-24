---
status: accepted
---

# O shell novo mora fora dos arquivos da publicação

`trazer --sobrescrever` fica com a publicação no arquivo em que os dois lados mudaram e, em seguida, reaplica só os trechos que `adaptar-hardness.py` conhece. O shell do FX ERP mora em arquivos que a publicação não envia. `index.tpl` recebe um gancho, e o adaptador recoloca esse gancho depois do trazer. Se a âncora sumiu, o trazer para, no mesmo critério dos ajustes de PHP 8.

## Considered Options

- Editar `index.tpl`, `menu.tpl` e `principal.css` no lugar. No próximo conflito desses arquivos, a publicação volta e a cara nova some. O adaptador não sabe recolocá-la.

## Consequences

- `principal.css` continua no miolo (grade e formulário). O CSS do shell é arquivo próprio.
- O gancho é uma troca de trecho estável. A escolha da âncora acontece quando o shell for construído.
