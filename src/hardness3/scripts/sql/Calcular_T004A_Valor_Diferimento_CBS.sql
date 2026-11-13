CREATE FUNCTION `Calcular_T004A_Valor_Diferimento_CBS`(
    -- Ids para usar com SELECT (devem ser usados principalmente para personalização para evitar personalizar o T004_Gravar_Totalizacao_4 )
    xT004_Id bigint,
    xT004A_Id bigint,

    -- Parâmetros para cálculo
    xT004A_Valor_Base_CBS_IBS decimal(15, 2),
    xT004A_Aliquota_CBS decimal(7, 4),
    xT004A_Percentual_Diferimento_CBS decimal(7, 4)
)
RETURNS decimal(15, 2)
BEGIN
    -- Se aliquota for > 0, calcula o valor, se não é zero
    RETURN xT004A_Valor_Base_CBS_IBS * (xT004A_Aliquota_CBS / 100) * (xT004A_Percentual_Diferimento_CBS / 100);
END