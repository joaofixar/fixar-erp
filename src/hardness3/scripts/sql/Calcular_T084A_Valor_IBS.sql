CREATE FUNCTION `Calcular_T084A_Valor_IBS`(
    -- Ids para usar com SELECT (devem ser usados principalmente para personalização para evitar personalizar o T084_Gravar_Totalizacao_4 )
    xT084_Id bigint, 
    xT084A_Id bigint,

    -- Parâmetros para cálculo
    xT084A_Valor_IBS_Municipal decimal(15, 2),
    xT084A_Valor_IBS_UF decimal(15, 2)
) RETURNS decimal(15, 2)
BEGIN
    RETURN xT084A_Valor_IBS_Municipal + xT084A_Valor_IBS_UF;
END
