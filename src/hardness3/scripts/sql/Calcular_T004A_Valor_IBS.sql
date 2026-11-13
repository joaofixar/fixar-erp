CREATE FUNCTION `Calcular_T004A_Valor_IBS`(
    -- Ids para usar com SELECT (devem ser usados principalmente para personalização para evitar personalizar o T004_Gravar_Totalizacao_4 )
    xT004_Id bigint,
    xT004A_Id bigint,

    -- Parâmetros para cálculo
    xT004A_Valor_IBS_Municipal decimal(15, 2),
    xT004A_Valor_IBS_UF decimal(15, 2)
) RETURNS decimal(15, 2)
BEGIN
    RETURN xT004A_Valor_IBS_Municipal + xT004A_Valor_IBS_UF;
END
