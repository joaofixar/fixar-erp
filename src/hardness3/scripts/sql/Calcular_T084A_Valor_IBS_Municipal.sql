CREATE FUNCTION `Calcular_T084A_Valor_IBS_Municipal`(
    -- Ids para usar com SELECT (devem ser usados principalmente para personalização para evitar personalizar o T084_Gravar_Totalizacao_4 )
    xT084_Id bigint,
    xT084A_Id bigint,

    -- Parâmetros para cálculo
    xT084A_Valor_Base_CBS_IBS decimal(15, 2), 
    xT084A_Aliquota_IBS_Municipal decimal(7, 4),
    xT084A_Valor_Diferimento_IBS_Municipal decimal(15, 2)

) RETURNS decimal(15, 2)
BEGIN
    -- Se aliquota for > 0, calcula o valor, se não é zero
    RETURN IF (
        xT084A_Aliquota_IBS_Municipal > 0,
        (xT084A_Valor_Base_CBS_IBS*(xT084A_Aliquota_IBS_Municipal/100)) - xT084A_Valor_Diferimento_IBS_Municipal,
        0
    );
END