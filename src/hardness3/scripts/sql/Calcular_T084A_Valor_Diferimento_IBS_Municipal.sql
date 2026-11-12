CREATE FUNCTION `Calcular_T084A_Valor_Diferimento_IBS_Municipal`(
    -- Ids para usar com SELECT (devem ser usados principalmente para personalização para evitar personalizar o T084_Gravar_Totalizacao_4 )
    xT084_Id bigint,
    xT084A_Id bigint,

    -- Parâmetros para cálculo
    xT084A_Valor_Base_CBS_IBS decimal(15, 2),
    xT084A_Aliquota_IBS_Municipal decimal(7, 4),
    xT084A_Percentual_Diferimento_IBS_Municipal decimal(7, 4)
) 
RETURNS decimal(15, 2)
BEGIN
    RETURN xT084A_Valor_Base_CBS_IBS * (xT084A_Aliquota_IBS_Municipal / 100) * (xT084A_Percentual_Diferimento_IBS_Municipal / 100);
END

