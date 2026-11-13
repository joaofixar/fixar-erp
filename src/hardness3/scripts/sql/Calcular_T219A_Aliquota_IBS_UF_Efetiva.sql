CREATE FUNCTION `Calcular_T219A_Aliquota_IBS_UF_Efetiva`(xT219_Id bigint, xT219A_Id bigint, xAliquota decimal(7,4), xReducao decimal(7,4)) RETURNS decimal(7,4)
BEGIN RETURN IFNULL(xAliquota,0) * (1 - IFNULL(xReducao,0) / 100); END
