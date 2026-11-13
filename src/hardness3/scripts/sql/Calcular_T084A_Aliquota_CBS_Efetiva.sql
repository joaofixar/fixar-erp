CREATE FUNCTION `Calcular_T084A_Aliquota_CBS_Efetiva`(
    -- Ids para usar com SELECT (devem ser usados principalmente para personalização para evitar personalizar o T084_Gravar_Totalizacao_4 )
    xT084_Id bigint, 
    xT084A_Id bigint,

    -- Parâmetros para cálculo
    xT084A_Aliquota_CBS decimal(7, 4),
    xT084A_Percentual_Reducao_CBS decimal(7, 4)
) RETURNS decimal(7, 4)
BEGIN
    RETURN xT084A_Aliquota_CBS * (1 - xT084A_Percentual_Reducao_CBS / 100);
END
