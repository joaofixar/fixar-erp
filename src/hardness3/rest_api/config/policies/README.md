# AccessPolicy Interna

Esta documentação foi mantida curta. O guia operacional completo da REST API agora está em:

- [rest_api/README_INTERNO_OPERACAO.md](/root/sistema_hardness/hardness3/rest_api/README_INTERNO_OPERACAO.md:1)

## Caminho atual em produção

A policy real por cliente fica em:

```text
dados_usuarios/{dbCliente}/rest_api/access_policy.php
```

Exemplo:

```text
dados_usuarios/e998/rest_api/access_policy.php
```

## Fallback de desenvolvimento

O caminho antigo dentro do projeto continua existindo apenas como fallback/dev:

```text
rest_api/config/policies/clients/{host_normalizado}__{dbCliente}.php
```

Exemplo:

```text
localhost__DEV002.php
cliente-hardness-com-br__e998.php
```

## Formato da policy

```php
<?php

return [
    'T005' => true,
    'T006' => [
        'block' => [
            'T006_Custo',
            'T006_Margem',
        ],
    ],
    'T007' => [
        'allow' => [
            'T007_Id',
            'T007_Descricao',
        ],
    ],
];
```

## Regras principais

- `true`: libera a tabela inteira, respeitando proteções globais.
- `block`: libera tudo, exceto os campos listados.
- `allow`: libera somente os campos listados.
- policy ausente: bloqueia tudo.
- policy inválida: bloqueia tudo.
- tabela ausente da policy: bloqueia tudo.
- não misturar `block` e `allow` na mesma tabela.

## Observações importantes

- `A001` e `A002` continuam bloqueadas estruturalmente.
- campo inexistente não é liberado, mesmo listado na policy.
- a PK real da tabela entra automaticamente para leitura/schema/fields/filter/sort, mesmo em `allow`.
- a PK continua bloqueada para alteração em `PATCH` e protegida no `POST` quando auto increment.
