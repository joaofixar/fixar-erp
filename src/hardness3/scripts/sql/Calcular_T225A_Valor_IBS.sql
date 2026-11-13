CREATE FUNCTION `Calcular_T225A_Valor_IBS`(xT225_Id bigint, xT225A_Id bigint, xMunicipal decimal(15,2), xUF decimal(15,2)) RETURNS decimal(15,2)
BEGIN RETURN IFNULL(xMunicipal,0) + IFNULL(xUF,0); END
