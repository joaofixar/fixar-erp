CREATE FUNCTION `Calcular_T008A_Valor_IBS`(
    -- Ids para usar com SELECT (devem ser usados principalmente para personalização para evitar personalizar o T008_Gravar_Totalizacao_4 )
    xT008_Id bigint, 
    xT008A_Id bigint,

    -- Parâmetros para cálculo
    xT008A_Valor_IBS_Municipal decimal(15, 2),
    xT008A_Valor_IBS_UF decimal(15, 2)
) RETURNS decimal(15, 2)
BEGIN
    RETURN xT008A_Valor_IBS_Municipal + xT008A_Valor_IBS_UF;
END
