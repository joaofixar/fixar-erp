# Banco de dados

O schema inicial idempotente cria `WHK001` a `WHK007`, sem chaves estrangeiras, no atualizador-base. O baseline suportado é **MySQL 5.6.19 até 8.4**, com InnoDB, `utf8mb4` e `utf8mb4_unicode_ci`.

Os campos `LONGTEXT` não declaram `DEFAULT`, pois MySQL 5.6 não permite default para tipos TEXT/BLOB. Não há migrations incrementais, `ALTER TABLE` nem consulta a `information_schema` para o schema WHK.

`WHK003` é o evento persistido; `WHK004` é uma delivery por subscription elegível. `WHK007` é interno e opcional para assinatura HMAC.
