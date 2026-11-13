CREATE FUNCTION `TRIB_Valor_ICMS_Desonerado`(xValor_Base_ICMS_Desonerado decimal(20,2), xAliquota_ICMS_Oculto decimal(5,2),xPercentual_Reducao_ICMS decimal(5,2)) RETURNS decimal(20,2)
BEGIN

    RETURN xValor_Base_ICMS_Desonerado*(xAliquota_ICMS_Oculto/100);

END
