CREATE FUNCTION `Calcular_T084A_Valor_Base_CBS_IBS`(
    -- Ids para usar com SELECT (devem ser usados principalmente para personalização para evitar personalizar o T084_Gravar_Totalizacao_4 )
    xT084_Id bigint,
    xT084A_Id bigint,

    -- Parâmetros para cálculo
    xT084_Valor_Conhecimento decimal(10,2),
    xT084_Valor_ICMS decimal(10,2)
) RETURNS decimal(10,2)
BEGIN    
    RETURN 
    IFNULL(xT084_Valor_Conhecimento, 0)
    -IFNULL(xT084_Valor_ICMS, 0);
END
