CREATE FUNCTION `Calcular_T008A_Valor_IBS_UF`(
    -- Ids para usar com SELECT (devem ser usados principalmente para personalização para evitar personalizar o T008_Gravar_Totalizacao_4 )
    xT008_Id bigint, 
    xT008A_Id bigint,

    -- Parâmetros para cálculo
    xT008A_Valor_Base_CBS_IBS decimal(15, 2), 
    xT008A_Aliquota_IBS_UF decimal(7, 4),
    xT008A_Valor_Diferimento_IBS_UF decimal(15, 2)

) RETURNS decimal(15, 2)
BEGIN
    -- Se aliquota for > 0, calcula o valor, se não é zero
    RETURN IF (
        xT008A_Aliquota_IBS_UF > 0,
        (xT008A_Valor_Base_CBS_IBS*(xT008A_Aliquota_IBS_UF/100)) - xT008A_Valor_Diferimento_IBS_UF,
        0
    );
END