CREATE FUNCTION `Calcular_T219A_Valor_IBS`(xT219_Id bigint, xT219A_Id bigint, xMunicipal decimal(15,2), xUF decimal(15,2)) RETURNS decimal(15,2)
BEGIN RETURN IFNULL(xMunicipal,0) + IFNULL(xUF,0); END
