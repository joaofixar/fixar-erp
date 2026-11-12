CREATE FUNCTION `T055_Valor_Total_Preco_Nota_Fiscal`(T055_Valor_Total_Custo decimal(16,6), T055_Valor_Base_Imposto_Importacao decimal(16,6), T055_Valor_Base_COFINS decimal(16,6), T055_Valor_Base_PIS decimal(16,6), T055_Valor_Base_IPI decimal(16,6), T055_Valor_Total_Imposto_Importacao decimal(16,6), T055_Valor_Total_PIS decimal(16,6), T055_Valor_Total_COFINS decimal(16,6), T055_Valor_Total_IPI decimal(16,6), T055_Valor_Total_Frete decimal(16,6), T055_Valor_Total_AFRMM decimal(16,6), T055_Valor_Total_THC decimal(16,6), T055_Valor_Total_Siscomex decimal(16,6), T055_Valor_Total_Despesas_Acessorias_Nota_Fiscal decimal(16,6), T055_Valor_Total_Despesas_Aduaneira decimal(16,6), T055_Valor_Total_Despesas decimal(16,6), xValor_Despesas_Preco_NFe decimal(16,9), T055_Valor_Total_Seguro decimal(16,9)) RETURNS decimal(19,6)
BEGIN
  
        RETURN T055_Valor_Total_Custo + T055_Valor_Base_Imposto_Importacao + T055_Valor_Total_Imposto_Importacao + T055_Valor_Total_PIS + T055_Valor_Total_COFINS;

END



