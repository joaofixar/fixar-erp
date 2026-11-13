CREATE FUNCTION `Calcular_T225A_Valor_CBS`(xT225_Id bigint, xT225A_Id bigint, xBase decimal(15,2), xAliquota decimal(7,4), xDiferimento decimal(15,2)) RETURNS decimal(15,2)
BEGIN RETURN IFNULL(xBase,0) * IFNULL(xAliquota,0) / 100 - IFNULL(xDiferimento,0); END
