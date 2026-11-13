CREATE PROCEDURE `T008_Gravar_Totalizacao`(xT008_Id int(11))
BEGIN
    DECLARE xPercentual_Desconto DECIMAL(19,10) DEFAULT 0;
    DECLARE xMultiplicador_Despesa DECIMAL(19,10) DEFAULT 0;
    DECLARE xMultiplicador_Despesas_Acessorias DECIMAL(19,10) DEFAULT 0;
    DECLARE xMultiplicador_Frete DECIMAL(19,10) DEFAULT 0;
    DECLARE xMultiplicador_Seguro DECIMAL(19,10) DEFAULT 0;
    DECLARE xT007_Id INT(11) DEFAULT 0;
    DECLARE xFlag_Revenda_Consumidor VARCHAR(1);
    DECLARE xFlag_Venda_Compra_Outros VARCHAR(1);
    DECLARE xD006_Flag_Somar_IPI_Base_ICMS ENUM('S','N') DEFAULT 'N';
    DECLARE xD006_Flag_Somar_Despesas_Base_IPI ENUM('S','N') DEFAULT 'N';
    DECLARE xD006_Flag_Forma_Calculo_COFINS varchar(1) default 'V';
    DECLARE xD006_Flag_Forma_Calculo_PIS varchar(1) default 'V';
    DECLARE xC004_Casas_Decimais_Preco int(2);
    DECLARE xT007_Casas_Decimais_Preco int(2);
    DECLARE xC004_Id int(10);
    DECLARE xT134_Id int(10) default '0';
    DECLARE xT008_Flag_Calculo_Manual varchar(1) default 'N';
    DECLARE xD006_Flag_Devolucao varchar(1) default 'N';
    DECLARE xT134_Reducao_Base_ICMS decimal(10,2) default '0';
  	DECLARE xFlag_ACP char(1);
    DECLARE xC031_Somar_ICMS_Preco_Venda varchar(1);
    DECLARE xC031_Somar_PIS_Preco_Venda varchar(1);
    DECLARE xC031_Somar_COFINS_Preco_Venda varchar(1);
    DECLARE xC031_Somar_IRPJ_Preco_Venda varchar(1);
    DECLARE xC031_Somar_CSLL_Preco_Venda varchar(1);
    DECLARE xD006_Flag_Isento_ICMS varchar(1);
	
    SELECT T008_T007_Id,
           D006_Flag_Venda_Compra_Outros,
           D006_Flag_Somar_IPI_Base_ICMS,
           D006_Flag_Somar_Despesas_Base_IPI,
           D006_Flag_Forma_Calculo_COFINS,
           D006_Flag_Forma_Calculo_PIS,
           T008_Flag_Calculo_Manual,
           T007_Casas_Decimais_Preco,
           T007_C004_Id,
           T007_Flag_ACP,
           T134_Reducao_Base_ICMS,
           T134_Id,
           D006_Flag_Devolucao,
           D006_Flag_Isento_ICMS
      INTO xT007_Id,
           xFlag_Venda_Compra_Outros,
           xD006_Flag_Somar_IPI_Base_ICMS,
           xD006_Flag_Somar_Despesas_Base_IPI,
           xD006_Flag_Forma_Calculo_COFINS,
           xD006_Flag_Forma_Calculo_PIS,
           xT008_Flag_Calculo_Manual,
           xT007_Casas_Decimais_Preco,
           xC004_Id,
           xFlag_ACP,
           xT134_Reducao_Base_ICMS,
           xT134_Id,
           xD006_Flag_Devolucao,
           xD006_Flag_Isento_ICMS
      FROM T008
     LEFT JOIN T007 ON T007_Id=T008_T007_Id
     LEFT JOIN D006 ON D006_Id=T008_D006_Id
     LEFT JOIN D024 ON D024_Id=T007_D024_Id
     LEFT JOIN D020 ON D020_Id=D024_D020_Id
     LEFT JOIN T134 ON T134_Id=T007_T134_Id
     LEFT JOIN D053 ON D053_D018_Id=D020_D018_Id AND D053_C004_Id=T007_C004_Id
      WHERE T008_Id=xT008_Id
       LIMIT 1;

      SELECT C004_Casas_Decimais_Preco
        INTO xC004_Casas_Decimais_Preco
        FROM C004
       WHERE C004_Id=xC004_Id;
       
    IF xT007_Casas_Decimais_Preco>0 THEN
       SET xC004_Casas_Decimais_Preco = xT007_Casas_Decimais_Preco;
    END IF;
    
    SELECT (T007_Valor_Desconto/T007_Valor_Total_Produtos)
      INTO xPercentual_Desconto
      FROM T007
    LEFT JOIN T008 ON T008_T007_Id=T007_Id
    WHERE T007_Id=xT007_Id
    GROUP BY T007_Id;
    IF xPercentual_Desconto IS NULL THEN
       SET xPercentual_Desconto = 0;
    END IF;
    SELECT  (T007_Valor_Frete+T007_Valor_Despesas_Acessorias+T007_Valor_Seguro) / sum(T008_Valor_Total_Preco),
             T007_Valor_Despesas_Acessorias / sum(T008_Valor_Total_Preco),
             T007_Valor_Seguro / sum(T008_Valor_Total_Preco),
             T007_Valor_Frete / sum(T008_Valor_Total_Preco),
             T007_Flag_Revenda_Consumidor
    INTO xMultiplicador_Despesa,
             xMultiplicador_Despesas_Acessorias,
             xMultiplicador_Seguro,
             xMultiplicador_Frete,
             xFlag_Revenda_Consumidor
    FROM T007
    LEFT JOIN T008 ON T008_T007_Id=T007_Id
    WHERE T007_Id=xT007_Id
    GROUP BY T007_Id;
    IF xMultiplicador_Despesa IS NULL THEN
       SET xMultiplicador_Despesa = 0;
    END IF;
    IF xMultiplicador_Despesas_Acessorias IS NULL THEN
       SET xMultiplicador_Despesas_Acessorias = 0;
    END IF;
    IF xMultiplicador_Frete IS NULL THEN
       SET xMultiplicador_Frete = 0;
    END IF;
    IF xMultiplicador_Seguro IS NULL THEN
       SET xMultiplicador_Seguro = 0;
    END IF;

    SELECT C031_Valor INTO xC031_Somar_ICMS_Preco_Venda FROM C031 WHERE C031_Campo = 'somarICMSPrecoVenda' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Somar_PIS_Preco_Venda FROM C031 WHERE C031_Campo = 'somarPISPrecoVenda' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Somar_COFINS_Preco_Venda FROM C031 WHERE C031_Campo = 'somarCOFINSPrecoVenda' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Somar_IRPJ_Preco_Venda FROM C031 WHERE C031_Campo = 'somarIRPJPrecoVenda' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Somar_CSLL_Preco_Venda FROM C031 WHERE C031_Campo = 'somarCSLLPrecoVenda' AND C031_C004_Id = xC004_Id;

    IF (xT008_Flag_Calculo_Manual = 'S') THEN
        UPDATE T008 LEFT JOIN D009 ON D009_Id=T008_D009_Id
        			LEFT JOIN T007 ON T007_Id=T008_T007_Id
                    SET T008_Valor_Preco_Unitario = T008_Valor_Preco_Sem_Desconto_Unitario,
                        T008_Valor_Total_Preco = T008_Valor_Preco_Unitario * T008_Quantidade,
                        T008_Valor_Total_Preco_Sem_Desconto = T008_Valor_Preco_Sem_Desconto_Unitario * T008_Quantidade, 
                        T008_Valor_Total_Custo=T008_Valor_Custo_Unitario*T008_Quantidade,
                        T008_Valor_Total_Despesas=T008_Valor_Total_Preco*xMultiplicador_Despesa,
                        T008_Valor_Total_Despesas_Acessorias=if((T008_Valor_Total_Preco*xMultiplicador_Despesas_Acessorias)<0.01 and xMultiplicador_Despesas_Acessorias>0,0.01,T008_Valor_Total_Preco*xMultiplicador_Despesas_Acessorias),
                        T008_Valor_Total_Frete=if(xMultiplicador_Frete>0 and (T008_Valor_Total_Preco*xMultiplicador_Frete)<=0.01,0.01,T008_Valor_Total_Preco*xMultiplicador_Frete),
                        T008_Valor_Total_Seguro=T008_Valor_Total_Preco*xMultiplicador_Seguro,
                        T008_Valor_Despesas_Unitario=T008_Valor_Total_Despesas/T008_Quantidade,
                        T008_Valor_Despesas_Acessorias_Unitario=T008_Valor_Total_Despesas_Acessorias/T008_Quantidade,
                        T008_Valor_Frete_Unitario=T008_Valor_Total_Frete/T008_Quantidade,
                        T008_Valor_Seguro_Unitario=T008_Valor_Total_Seguro/T008_Quantidade,
                        T008_Peso_Total=T008_Quantidade*T008_Peso_Unitario,
                        T008_Peso_Bruto_Total=T008_Quantidade*T008_Peso_Bruto,
                        T008_Valor_Total_Custo_Final=(T008_Valor_Total_Preco+T008_Valor_Total_Despesas+if(xFlag_Revenda_Consumidor='C',T008_Valor_IPI,0)),
                        T008_Valor_Custo_Final_Unitario=T008_Valor_Total_Custo_Final/T008_Quantidade,
                        T008_Valor_Base_ICMS_Diferido=T008_Valor_Base_ICMS_Sem_Reducao-T008_Valor_Base_ICMS,                        
                        T008_IPV=(T008_Valor_Preco_Unitario-
                                IF(xC031_Somar_ICMS_Preco_Venda   = 'S', IFNULL(T008_Valor_ICMS/T008_Quantidade,0), 0)-
                                IF(xC031_Somar_PIS_Preco_Venda    = 'S', IFNULL(T008_Valor_PIS/T008_Quantidade,0), 0)-
                                IF(xC031_Somar_COFINS_Preco_Venda = 'S', IFNULL(T008_Valor_COFINS/T008_Quantidade,0), 0)-
                                IF(xC031_Somar_IRPJ_Preco_Venda   = 'S', IFNULL(T008_Valor_IRPJ/T008_Quantidade,0), 0)-
                                IF(xC031_Somar_CSLL_Preco_Venda   = 'S', IFNULL(T008_Valor_CSLL/T008_Quantidade,0), 0))/T008_Valor_Custo_Unitario,
                        T008_Percentual_Margem=round((((T008_Valor_Preco_Unitario-
                                IF(xC031_Somar_ICMS_Preco_Venda   = 'S', IFNULL(T008_Valor_ICMS/T008_Quantidade,0), 0)-
                                IF(xC031_Somar_PIS_Preco_Venda    = 'S', IFNULL(T008_Valor_PIS/T008_Quantidade,0), 0)-
                                IF(xC031_Somar_COFINS_Preco_Venda = 'S', IFNULL(T008_Valor_COFINS/T008_Quantidade,0), 0)-
                                IF(xC031_Somar_IRPJ_Preco_Venda   = 'S', IFNULL(T008_Valor_IRPJ/T008_Quantidade,0), 0)-
                                IF(xC031_Somar_CSLL_Preco_Venda   = 'S', IFNULL(T008_Valor_CSLL/T008_Quantidade,0), 0))-T008_Valor_Custo_Unitario)/
                              (T008_Valor_Preco_Unitario-
                                IF(xC031_Somar_ICMS_Preco_Venda   = 'S', IFNULL(T008_Valor_ICMS/T008_Quantidade,0), 0)-
                                IF(xC031_Somar_PIS_Preco_Venda    = 'S', IFNULL(T008_Valor_PIS/T008_Quantidade,0), 0)-
                                IF(xC031_Somar_COFINS_Preco_Venda = 'S', IFNULL(T008_Valor_COFINS/T008_Quantidade,0), 0)-
                                IF(xC031_Somar_IRPJ_Preco_Venda   = 'S', IFNULL(T008_Valor_IRPJ/T008_Quantidade,0), 0)-
                                IF(xC031_Somar_CSLL_Preco_Venda   = 'S', IFNULL(T008_Valor_CSLL/T008_Quantidade,0), 0)))*100,2),
                        T008_Valor_Lucro=round((T008_Valor_Total_Custo*T008_IPV)-(T008_Valor_Total_Custo*T008_IPV_Sem_Lucro),2),
                        T008_Aliquota_PIS=if(T008_Flag_Isento_PIS='S',0,T008_Aliquota_PIS),
                        T008_Aliquota_COFINS=if(T008_Flag_Isento_COFINS='S',0,T008_Aliquota_COFINS),
                        T008_Total_Cubagem = IFNULL(T008_Cubagem_Unitaria,0) * IFNULL(T008_Quantidade,0),
                        T008_Valor_Trib_Transparencia=IFNULL(T008_Valor_Total_Preco_Sem_Desconto,0)*(IFNULL(T008_Percentual_Trib_Transparencia,0)/100)
                    WHERE T008_Id=xT008_Id and T007_Flag_Calcular='S' and xD006_Flag_Devolucao = 'N';
    ELSE
        UPDATE T008 LEFT JOIN D009 ON D009_Id=T008_D009_Id
        			LEFT JOIN T007 ON T007_Id=T008_T007_Id
                    SET T008_Valor_Preco_Unitario = T008_Valor_Preco_Sem_Desconto_Unitario,
                        T008_Valor_Total_Preco              = if(xFlag_ACP=4,truncate(T008_Valor_Preco_Unitario              * T008_Quantidade,2),T008_Valor_Preco_Unitario              * T008_Quantidade),
                        T008_Valor_Total_Preco_Sem_Desconto = if(xFlag_ACP=4,truncate(T008_Valor_Preco_Sem_Desconto_Unitario * T008_Quantidade,2),T008_Valor_Preco_Sem_Desconto_Unitario * T008_Quantidade), 
                        T008_Valor_Total_Custo=T008_Valor_Custo_Unitario*T008_Quantidade,
                        T008_Valor_Total_Despesas=T008_Valor_Total_Preco*xMultiplicador_Despesa,
                        T008_Valor_Total_Despesas_Acessorias=if((T008_Valor_Total_Preco*xMultiplicador_Despesas_Acessorias)<0.01 and xMultiplicador_Despesas_Acessorias>0,0.01,T008_Valor_Total_Preco*xMultiplicador_Despesas_Acessorias),
                        T008_Valor_Total_Frete=if(xMultiplicador_Frete>0 and (T008_Valor_Total_Preco*xMultiplicador_Frete)<=0.01,0.01,T008_Valor_Total_Preco*xMultiplicador_Frete),
                        T008_Valor_Total_Seguro=T008_Valor_Total_Preco*xMultiplicador_Seguro,
                        T008_Valor_Base_IPI=If(T008_Aliquota_IPI>0,(T008_Valor_Total_Preco+T008_Valor_Total_Imposto_Importacao+if(xD006_Flag_Somar_Despesas_Base_IPI='S',T008_Valor_Total_Frete+T008_Valor_Total_Seguro+T008_Valor_Total_Despesas_Acessorias,0))*If(T008_Percentual_Reducao_IPI>0,(T008_Percentual_Reducao_IPI/100),1),0),
                        T008_Valor_IPI=round(T008_Valor_Base_IPI*(T008_Aliquota_IPI/100),2),
                        T008_Valor_Despesas_Unitario=T008_Valor_Total_Despesas/T008_Quantidade,
                        T008_Valor_Despesas_Acessorias_Unitario=T008_Valor_Total_Despesas_Acessorias/T008_Quantidade,
                        T008_Valor_Frete_Unitario=T008_Valor_Total_Frete/T008_Quantidade,
                        T008_Valor_Seguro_Unitario=T008_Valor_Total_Seguro/T008_Quantidade,
                        T008_Peso_Total=T008_Quantidade*T008_Peso_Unitario,
                        T008_Peso_Bruto_Total=T008_Quantidade*T008_Peso_Bruto,
                        T008_Valor_Total_Custo_Final=(T008_Valor_Total_Preco+T008_Valor_Total_Despesas+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T008_Valor_IPI,0)),
                        T008_Valor_Custo_Final_Unitario=T008_Valor_Total_Custo_Final/T008_Quantidade,
                        T008_Valor_Base_ICMS_Sem_Reducao=If(T008_Aliquota_ICMS>0,T008_Valor_Total_Custo_Final+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T008_Valor_IPI,0),0),
                        T008_Valor_Base_ICMS=If(T008_Aliquota_ICMS>0,T008_Valor_Base_ICMS_Sem_Reducao*if(T008_Percentual_Reducao_ICMS>0,((100-T008_Percentual_Reducao_ICMS)/100),1),0),
                        T008_Valor_Base_ICMS_Diferido=T008_Valor_Base_ICMS_Sem_Reducao-T008_Valor_Base_ICMS,
                        T008_Valor_Base_ICMS_Desonerado=IF(T008_Aliquota_ICMS_Oculto>0 AND xD006_Flag_Isento_ICMS = 'D',T008_Valor_Total_Custo_Final+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T008_Valor_IPI,0)-IF(T008_Percentual_Reducao_ICMS>0,T008_Valor_Base_ICMS,0),0),
                        T008_Valor_ICMS=T008_Valor_Base_ICMS*(T008_Aliquota_ICMS/100),                        
                        T008_Valor_ICMS_Desonerado=T008_Valor_Base_ICMS_Desonerado*(T008_Aliquota_ICMS_Oculto/100),
                        T008_Valor_Lucro=round((T008_Valor_Total_Custo*T008_IPV)-(T008_Valor_Total_Custo*T008_IPV_Sem_Lucro),2),
                        T008_Valor_Base_ICMS_Substituicao=IF(T008_Flag_ST='S',(T008_Valor_Total_Preco+T008_Valor_Total_Despesas+T008_Valor_IPI)*((T008_ST_VA/100)+1),0),
                        T008_Valor_ICMS_Substituicao=(T008_Valor_Base_ICMS_Substituicao*(T008_ST_ICMS_Interno/100)),
                        T008_Valor_ICMS_Substituicao_Retencao=((T008_Valor_Base_ICMS_Substituicao*if(T008_Percentual_Reducao_ICMS_ST>0,((100-T008_Percentual_Reducao_ICMS_ST)/100),1))*(T008_ST_ICMS_Interno/100))-((T008_Valor_Total_Custo_Final*if(T008_Percentual_Reducao_ICMS>0,((100-T008_Percentual_Reducao_ICMS)/100),1))*T008_ICMS_Inter_Estadual/100),
                        T008_Valor_ICMS_Substituicao_Retencao=IF(T008_Valor_ICMS_Substituicao_Retencao<0,0,T008_Valor_ICMS_Substituicao_Retencao),
                        T008_Valor_Base_ICMS_Interno=(((T008_Valor_Total_Preco+T008_Valor_Total_Despesas+T008_Valor_IPI)*((T008_ST_VA/100)+1))*if(T008_Percentual_Reducao_ICMS_ST>0,((100-T008_Percentual_Reducao_ICMS_ST)/100),1)),
                        T008_Valor_ICMS_Interno=(T008_Valor_Base_ICMS_Interno*(T008_ST_ICMS_Interno/100)),
                        T008_Valor_Base_ICMS_Inter_Estadual=(T008_Valor_Total_Custo_Final*if(T008_Percentual_Reducao_ICMS>0,((100-T008_Percentual_Reducao_ICMS)/100),1)),
                        T008_Valor_ICMS_Inter_Estadual=((T008_Valor_Base_ICMS_Inter_Estadual*T008_ICMS_Inter_Estadual)/100),
                        T008_Valor_Base_ICMS_Impresso=if(substring(T008_Flag_Situacao_Tributaria,2,2)='51' or substring(T008_Flag_Situacao_Tributaria,2,2)='90',T008_Valor_Base_ICMS_Sem_Reducao,T008_Valor_Base_ICMS),
                        T008_Valor_Base_PIS=if(T008_Flag_Isento_PIS='S',0,if(xD006_Flag_Forma_Calculo_PIS='L',if(T008_Valor_Total_Custo<T008_Valor_Total_Preco,T008_Valor_Total_Preco-T008_Valor_Total_Custo,0),T008_Valor_Total_Preco)),
                        T008_Aliquota_PIS=if(T008_Flag_Isento_PIS='S',0,T008_Aliquota_PIS),
                        T008_Valor_PIS=if(T008_Aliquota_PIS>0,T008_Valor_Base_PIS*(T008_Aliquota_PIS/100),0),
                        T008_Valor_Base_COFINS=if(T008_Flag_Isento_COFINS='S',0,if(xD006_Flag_Forma_Calculo_COFINS='L',if(T008_Valor_Total_Custo<T008_Valor_Total_Preco,T008_Valor_Total_Preco-T008_Valor_Total_Custo,0),T008_Valor_Total_Preco)),
                        T008_Aliquota_COFINS=if(T008_Flag_Isento_COFINS='S',0,T008_Aliquota_COFINS),
                        T008_Valor_COFINS=if(T008_Aliquota_COFINS>0,T008_Valor_Base_COFINS*(T008_Aliquota_COFINS/100),0),
                        T008_Valor_ICMS_Credito=if(xT134_Id<=0 or xT134_Id='' or ifnull(xT134_Id,0)<=0,T008_Valor_ICMS_Credito,If(T008_Aliquota_ICMS_Credito>0,(T008_Valor_Total_Custo_Final+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T008_Valor_IPI,0))-((T008_Valor_Total_Custo_Final+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T008_Valor_IPI,0))*if(xT134_Reducao_Base_ICMS>0,(xT134_Reducao_Base_ICMS/100),1)),0)*(T008_Aliquota_ICMS_Credito/100)),
                        T008_Aliquota_IRPJ=if(T008_Flag_Isento_IRPJ='S',0,T008_Aliquota_IRPJ),
                        T008_Aliquota_CSLL=if(T008_Flag_Isento_CSLL='S',0,T008_Aliquota_CSLL),
                        T008_Valor_Base_IRPJ=T008_Valor_Total_Preco,
                        T008_Valor_IRPJ=if(T008_Aliquota_IRPJ>0,T008_Valor_Base_IRPJ*(T008_Aliquota_IRPJ/100),0),
                        T008_Valor_Base_CSLL=T008_Valor_Total_Preco,
                        T008_Valor_CSLL=if(T008_Aliquota_CSLL>0,T008_Valor_Base_CSLL*(T008_Aliquota_CSLL/100),0),
                        T008_Total_Cubagem = IFNULL(T008_Cubagem_Unitaria,0) * IFNULL(T008_Quantidade,0),
                        T008_Valor_Trib_Transparencia=IFNULL(T008_Valor_Total_Preco_Sem_Desconto,0)*(IFNULL(T008_Percentual_Trib_Transparencia,0)/100),
                        T008_IPV=(T008_Valor_Preco_Unitario-
                                IF(xC031_Somar_ICMS_Preco_Venda   = 'S', IFNULL(T008_Valor_ICMS/T008_Quantidade,0), 0)-
                                IF(xC031_Somar_PIS_Preco_Venda    = 'S', IFNULL(T008_Valor_PIS/T008_Quantidade,0), 0)-
                                IF(xC031_Somar_COFINS_Preco_Venda = 'S', IFNULL(T008_Valor_COFINS/T008_Quantidade,0), 0)-
                                IF(xC031_Somar_IRPJ_Preco_Venda   = 'S', IFNULL(T008_Valor_IRPJ/T008_Quantidade,0), 0)-
                                IF(xC031_Somar_CSLL_Preco_Venda   = 'S', IFNULL(T008_Valor_CSLL/T008_Quantidade,0), 0))/T008_Valor_Custo_Unitario,
                        T008_Percentual_Margem=round((((T008_Valor_Preco_Unitario-
                                IF(xC031_Somar_ICMS_Preco_Venda   = 'S', IFNULL(T008_Valor_ICMS/T008_Quantidade,0), 0)-
                                IF(xC031_Somar_PIS_Preco_Venda    = 'S', IFNULL(T008_Valor_PIS/T008_Quantidade,0), 0)-
                                IF(xC031_Somar_COFINS_Preco_Venda = 'S', IFNULL(T008_Valor_COFINS/T008_Quantidade,0), 0)-
                                IF(xC031_Somar_IRPJ_Preco_Venda   = 'S', IFNULL(T008_Valor_IRPJ/T008_Quantidade,0), 0)-
                                IF(xC031_Somar_CSLL_Preco_Venda   = 'S', IFNULL(T008_Valor_CSLL/T008_Quantidade,0), 0))-T008_Valor_Custo_Unitario)/
                              (T008_Valor_Preco_Unitario-
                                IF(xC031_Somar_ICMS_Preco_Venda   = 'S', IFNULL(T008_Valor_ICMS/T008_Quantidade,0), 0)-
                                IF(xC031_Somar_PIS_Preco_Venda    = 'S', IFNULL(T008_Valor_PIS/T008_Quantidade,0), 0)-
                                IF(xC031_Somar_COFINS_Preco_Venda = 'S', IFNULL(T008_Valor_COFINS/T008_Quantidade,0), 0)-
                                IF(xC031_Somar_IRPJ_Preco_Venda   = 'S', IFNULL(T008_Valor_IRPJ/T008_Quantidade,0), 0)-
                                IF(xC031_Somar_CSLL_Preco_Venda   = 'S', IFNULL(T008_Valor_CSLL/T008_Quantidade,0), 0)))*100,2)
                    WHERE T008_Id=xT008_Id and T007_Flag_Calcular='S' and xD006_Flag_Devolucao = 'N';
    END IF;
END;
