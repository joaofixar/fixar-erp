CREATE FUNCTION `TRIB_Valor_Difal_Destino`(xValor_Base_Partilha_ICMS decimal(15,2),
								   xValor_ICMS decimal(10,2),
								   xAliquota_ICMS decimal(5,2),
								   xPercentual_Partilha_ICMS_Destino decimal(10,2),
								   xST_ICMS_Interno decimal(5,2),
								   xICMS_Inter_Estadual decimal(5,2),
								   xPercentual_Reducao_ICMS decimal(5,2)) RETURNS decimal(15,2)
BEGIN

	RETURN ((xValor_Base_Partilha_ICMS*(xST_ICMS_Interno/100)) - xValor_ICMS) * (xPercentual_Partilha_ICMS_Destino/100);

END
