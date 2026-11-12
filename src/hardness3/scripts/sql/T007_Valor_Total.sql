CREATE FUNCTION `T007_Valor_Total`(xT007_Valor_Total_Produtos decimal(14,2), xT007_Valor_Total_Servicos decimal(14,2), xT007_Valor_Mao_Obra decimal(10,2), xT007_Valor_Despesas_Acessorias decimal(10,2), xT007_Valor_ICMS_Substituicao_Retencao decimal(14,2), xT007_Valor_Total_IPI decimal(14,4), xT007_Valor_Total_IPI_Devolucao decimal(14,2), xT007_Valor_Seguro decimal(10,2), xT007_Valor_FCP_ST decimal(15,2), xT007_Valor_Frete decimal(19,2), xT007_Valor_Desconto decimal(12,6), xT007_Valor_Total_ICMS_Desonerado decimal(15,2), xT007_Valor_Total_ICMS decimal(14,2), xT007_Valor_Total_Imposto_Importacao decimal(19,2), xT007_Valor_Total_FCP decimal(15,2), xD006_Deduz_ICMS_Desonerado_Valor_Total varchar(1), xD006_Flag_Estado varchar(1), xT007_Valor_Total_COFINS decimal(14,2), xT007_Valor_Total_PIS decimal(14,2), xT008_Valor_Total_Despesas_Aduaneira decimal(14,2)) RETURNS decimal(14,2)
BEGIN
  
        RETURN IFNULL(xT007_Valor_Total_Produtos,0) + 
               IFNULL(xT007_Valor_Total_Servicos,0) + 
               IFNULL(xT007_Valor_Mao_Obra,0) + 
               IFNULL(xT007_Valor_Despesas_Acessorias,0) +
               IFNULL(xT007_Valor_ICMS_Substituicao_Retencao,0) +
               IFNULL(xT007_Valor_Total_IPI,0) +
               IFNULL(xT007_Valor_Total_IPI_Devolucao,0) +
               IFNULL(xT007_Valor_Seguro,0) +
               IFNULL(xT007_Valor_FCP_ST,0) +
               IFNULL(xT007_Valor_Frete,0) -
               IFNULL(xT007_Valor_Desconto,0) -
               IFNULL(IF(xD006_Deduz_ICMS_Desonerado_Valor_Total = 'S',xT007_Valor_Total_ICMS_Desonerado,0),0);          
END







