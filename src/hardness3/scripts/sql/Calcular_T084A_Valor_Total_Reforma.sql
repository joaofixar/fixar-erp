CREATE FUNCTION `Calcular_T084A_Valor_Total_Reforma`(
    -- Ids para usar com SELECT (devem ser usados principalmente para personalização para evitar personalizar o T084_Gravar_Totalizacao_4 )
    xT084_Id bigint, 
    xT084A_Id bigint,

    -- Parâmetros para cálculo
    xT084_Valor_Conhecimento decimal(10, 2),
    xT084A_Valor_CBS decimal(15, 2),
    xT084A_Valor_IBS decimal(15, 2)
) RETURNS decimal(15, 2)
BEGIN
    RETURN xT084_Valor_Conhecimento;

    -- Escrito na NT: Exceção: Em 2026 não somar IBS e CBS (então não somamos IBS e CBS por enquanto)
    -- RETURN xT084_Valor_Conhecimento + xT084A_Valor_IBS + xT084A_Valor_CBS;
END
