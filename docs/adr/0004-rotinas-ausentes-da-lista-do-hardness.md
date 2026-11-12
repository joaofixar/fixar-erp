---
status: accepted
---

# As rotinas que faltam nascem da lista do Hardness

A fotografia traz cinco rotinas. As telas chamam o catálogo de `atualizacaoBase-rotinas-lista.php`, com 195 funções, procedures e triggers em `scripts/sql/`. A base local cria só as que ainda não existem. Não recria as cinco da fotografia e não roda o resto da atualização de tabelas. O passo vale para o volume que já está no ar e para um volume novo. Arquivo que falha fica listado e o passo segue.

## Considered Options

- Instalar uma função por tela, começando por `T003_Status_Orcamento`. O mesmo erro volta na tela seguinte.
- Rodar `atualizacaoBase.php` inteiro. Além das rotinas, ele cria coluna, índice e configuração.
- Copiar o SQL das rotinas para dentro da fotografia. O texto ficaria em dois lugares.

## Consequences

- Corrige o trecho do ADR 0003 que diz que a fotografia traz as rotinas que as telas chamam. A fotografia continua sendo a estrutura e os dados de validação. As rotinas vêm deste passo.
- Onze arquivos em `scripts/sql/` que a lista não chama ficam de fora.
- Gatilho que ainda não existe passa a gravar junto com a função. É o catálogo que a produção já aplica, não um gatilho novo da Fixar.
