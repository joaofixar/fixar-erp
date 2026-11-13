CREATE FUNCTION `Calcular_T008A_Aliquota_IBS_UF_Efetiva`(
    -- Ids para usar com SELECT (devem ser usados principalmente para personalização para evitar personalizar o T008_Gravar_Totalizacao_4 )
    xT008_Id bigint, 
    xT008A_Id bigint,

    -- Parâmetros para cálculo
    xT008A_Aliquota_IBS_UF decimal(7, 4),
    xT008A_Percentual_Reducao_IBS_UF decimal(7, 4)
) RETURNS decimal(7, 4)
BEGIN
    RETURN xT008A_Aliquota_IBS_UF * (1 - xT008A_Percentual_Reducao_IBS_UF / 100);
END
