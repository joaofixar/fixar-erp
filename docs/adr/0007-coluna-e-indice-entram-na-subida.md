---
status: accepted
---

# Coluna e índice ausentes entram na subida

O código publicado passou a ler coluna que a fotografia não tem. A tela de empresa avisa que `C004_Flag_Usar_DANFE_RTC` não existe. O `olhar` e o `trazer` continuam sem falar com o banco. Na subida, junto das rotinas, a base cria a coluna e o índice que `atualizacaoBase.php` pede por `Criar_Campo`, `Criar_Campos` e `Criar_Indice`, e que ainda não existem.

## Considered Options

- Rodar `atualizacaoBase.php` inteiro. Além da coluna, ele grava configuração e outro SQL.
- Criar só a coluna do aviso. O próximo campo novo repetiria o mesmo aviso.

## Consequences

- Tabela que o script não cria continua de fora. Coluna nova numa tabela ausente espera a tabela existir.
- Configuração continua de fora. O valor padrão da coluna é o que o `Criar_Campo` já declara.
- Falha de uma coluna fica listada e o passo segue. A subida não recria coluna nem índice que já existem.
