CREATE FUNCTION `VEN_Calculo_IPV`(xT003_Id int(11),
                                  xT004_Id int(11),
                                  xT005_Id int(11),
                                  xT006_Id int(11),
                                  xT007_Id int(11),
                                  xT008_Id int(11),
                                  xC031_Somar_ICMS_Preco_Venda varchar(1),
                                  xC031_Somar_PIS_Preco_Venda varchar(1),
                                  xC031_Somar_COFINS_Preco_Venda varchar(1),
                                  xC031_Somar_IRPJ_Preco_Venda varchar(1),
                                  xC031_Somar_CSLL_Preco_Venda varchar(1),
                                  xValor_Preco_Unitario decimal(20,10),
                                  xValor_Custo_Unitario decimal(20,5),
                                  xQuantidade decimal(15,5),
                                  xValor_ICMS decimal(15,5),
                                  xValor_PIS decimal(15,5),
                                  xValor_COFINS decimal(15,5),
                                  xValor_IRPJ decimal(15,5),
                                  xValor_CSLL decimal(15,5)) RETURNS decimal(10,4)
BEGIN

    RETURN (xValor_Preco_Unitario-
            IF(xC031_Somar_ICMS_Preco_Venda   = 'S', IFNULL(xValor_ICMS/xQuantidade,0), 0)-
            IF(xC031_Somar_PIS_Preco_Venda    = 'S', IFNULL(xValor_PIS/xQuantidade,0), 0)-
            IF(xC031_Somar_COFINS_Preco_Venda = 'S', IFNULL(xValor_COFINS/xQuantidade,0), 0)-
            IF(xC031_Somar_IRPJ_Preco_Venda   = 'S', IFNULL(xValor_IRPJ/xQuantidade,0), 0)-
            IF(xC031_Somar_CSLL_Preco_Venda   = 'S', IFNULL(xValor_CSLL/xQuantidade,0), 0)) / xValor_Custo_Unitario;

END
