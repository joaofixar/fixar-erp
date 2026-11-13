CREATE FUNCTION `Calcular_T004A_Valor_IBS_Municipal`(
    -- Ids para usar com SELECT (devem ser usados principalmente para personalização para evitar personalizar o T004_Gravar_Totalizacao_4 )
    xT004_Id bigint,
    xT004A_Id bigint,

    -- Parâmetros para cálculo
    xT004A_Valor_Base_CBS_IBS decimal(15, 2),
    xT004A_Aliquota_IBS_Municipal decimal(7, 4),
    xT004A_Valor_Diferimento_IBS_Municipal decimal(15, 2)

) RETURNS decimal(15, 2)
BEGIN
    -- Se aliquota for > 0, calcula o valor, se não é zero
    RETURN IF (
        xT004A_Aliquota_IBS_Municipal > 0,
        (xT004A_Valor_Base_CBS_IBS*(xT004A_Aliquota_IBS_Municipal/100)) - xT004A_Valor_Diferimento_IBS_Municipal,
        0
    );
END