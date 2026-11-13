# Task 45 — exclusão e arquivamento seguro

SQL manual (não executado): `ALTER TABLE GNRE001 ADD COLUMN GNRE001_Arquivada CHAR(1) NOT NULL DEFAULT 'N', ADD INDEX IDX_GNRE001_EMPRESA_ARQUIVADA (GNRE001_C004_Id, GNRE001_Arquivada);`.

O schema confirma `GNRE001_C004_Id` como empresa proprietária. Não houve migration, Portal, transmissão, consulta, PDF ou alteração de guia/banco real. Exclusão física só alcança rascunho sem histórico; rejeição técnica final de recepção também é arquivável, mas jamais excluível. Arquivamento é reversível e preserva integralmente auditoria, XML, recibos, SOAP, resultado, PDF e snapshots.
