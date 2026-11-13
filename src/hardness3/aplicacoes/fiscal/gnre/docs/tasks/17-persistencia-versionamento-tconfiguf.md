# Task 17 — Persistência e versionamento da TConfigUf

`GNRE007` é o snapshot imutável da resposta oficial `TConfigUf`: empresa que
consultou o Portal (`C004`), ambiente, UF, XML validado, interpretação JSON,
SHA-256, horário, usuário e origem técnica. A unicidade
`empresa + ambiente + UF + SHA-256` reutiliza conteúdo idêntico sem misturar
empresas, ambientes ou UFs; XML diferente cria uma nova versão.

`GNRE001_GNRE007_Id` é nullable para preservar guias existentes. Guias novas
guardam o snapshot selecionado; ao abrir uma guia, as regras vêm desse vínculo,
não da sessão e sem nova consulta ao Portal. A validação XML/XSD ocorre antes
da persistência e a interpretação continua centralizada em `GnreConfigUfRules`
(Tasks 14/16).

Uma atualização é sempre manual: consulta bem-sucedida cria/reutiliza somente
o snapshot e não altera guias já vinculadas. Para guia legada, a tela permanece
em consulta e bloqueia edição dependente, XML e transmissão até o operador
consultar uma configuração e confirmar conscientemente o vínculo compatível.

## SQL local aplicado

Em 30/10/2026, após confirmação explícita, foi aplicado em `e998` pelo
container `hardness-php7` somente o `CREATE TABLE GNRE007` e o `ALTER TABLE
GNRE001` abaixo. Não houve DML, backfill, drop ou alteração de outra tabela.

```sql
CREATE TABLE GNRE007 (
GNRE007_Id int unsigned NOT NULL AUTO_INCREMENT,
GNRE007_C004_Id int unsigned NOT NULL COMMENT 'Empresa/CNPJ cujo certificado consultou o Portal',
GNRE007_Ambiente char(1) NOT NULL COMMENT 'Ambiente informado na TConfigUf (homologação=2)',
GNRE007_UF char(2) NOT NULL COMMENT 'UF favorecida da configuração oficial',
GNRE007_XML mediumtext NOT NULL COMMENT 'TConfigUf bruto validado no XSD oficial',
GNRE007_Regras_JSON mediumtext NOT NULL COMMENT 'Interpretação Task 16 do XML validado',
GNRE007_XML_SHA256 char(64) NOT NULL COMMENT 'SHA-256 do XML bruto',
GNRE007_Data_Hora_Consulta datetime NOT NULL,
GNRE007_C007_Id_Consulta int DEFAULT NULL COMMENT 'Usuário que solicitou a consulta manual',
GNRE007_Origem varchar(30) NOT NULL DEFAULT 'PORTAL_MANUAL' COMMENT 'Origem técnica auditável',
PRIMARY KEY (GNRE007_Id),
UNIQUE KEY UQ_GNRE007_Empresa_Ambiente_UF_Hash (GNRE007_C004_Id,GNRE007_Ambiente,GNRE007_UF,GNRE007_XML_SHA256),
KEY IDX_GNRE007_Localizacao (GNRE007_C004_Id,GNRE007_Ambiente,GNRE007_UF,GNRE007_Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Snapshots imutáveis da configuração oficial TConfigUf';

ALTER TABLE GNRE001
  ADD COLUMN GNRE001_GNRE007_Id int unsigned DEFAULT NULL COMMENT 'Snapshot imutável TConfigUf escolhido conscientemente para esta guia',
  ADD KEY IDX_GNRE001_GNRE007_Id (GNRE001_GNRE007_Id);
```

`SHOW CREATE TABLE GNRE007` confirmou os dois índices esperados. `SHOW COLUMNS`
e `SHOW INDEX FROM GNRE001` confirmaram a coluna nullable e
`IDX_GNRE001_GNRE007_Id`. A única guia existente, `GNRE001_Id=7`, continuou
`R`, empresa `3`, UF `PR`, com vínculo `NULL`; o total de guias ficou em `1`
antes e depois. `GNRE007` iniciou vazia (`0` snapshots), como esperado.
