CREATE PROCEDURE `T004_Gravar_Totalizacao_4`(xT004_Id int(11))                                            
BEGIN
    DECLARE xT003_Id INT;
    DECLARE xPercentual_Desconto DECIMAL(30,20) DEFAULT 0;
    DECLARE xPercentual_Desconto_Bruto DECIMAL(30,20) DEFAULT 0;
    DECLARE xPercentual_Desconto_Confirmado DECIMAL(30,20) DEFAULT 0;
    DECLARE xMultiplicador_Despesa DECIMAL(10,6) DEFAULT 0;
    DECLARE xMultiplicador_Despesa_Confirmado DECIMAL(10,6) DEFAULT 0;
    DECLARE xMultiplicador_Despesas_Acessorias DECIMAL(20,10) DEFAULT 0;
    DECLARE xFlag_Revenda_Consumidor VARCHAR(1);
    DECLARE xD006_Flag_Forma_Calculo_COFINS varchar(1) default 'V';
    DECLARE xD006_Flag_Forma_Calculo_PIS varchar(1) default 'V';
    DECLARE xD006_Flag_Somar_Despesas_Base_ST ENUM('S','N') DEFAULT 'N';
    DECLARE xValor_Total_Agregados DECIMAL(10,2) DEFAULT 0;
    DECLARE xC004_Casas_Decimais_Preco int(2);
    DECLARE xC004_Id int(10);
    DECLARE xD006_Flag_Somar_IPI_Base_ICMS varchar(1);
    DECLARE xValor_Total_Frete DECIMAL(19,2) DEFAULT 0;
    DECLARE xMultiplicador_Frete DECIMAL(20,10) DEFAULT 0;
    DECLARE xTotal_Itens INT(5);
    DECLARE xC004_Flag_Rateio_Frete VARCHAR(1) DEFAULT '1';
    DECLARE xFlag_ACP VARCHAR(1);
    DECLARE xT003_Casas_Decimais_Preco int(2);
    DECLARE xC031_Somar_ICMS_Preco_Venda varchar(1);
    DECLARE xC031_Somar_PIS_Preco_Venda varchar(1);
    DECLARE xC031_Somar_COFINS_Preco_Venda varchar(1);
    DECLARE xC031_Somar_IRPJ_Preco_Venda varchar(1);
    DECLARE xC031_Somar_CSLL_Preco_Venda varchar(1);
    DECLARE xC031_Desabilitar_Cotacao_Emergencia varchar(1);
    DECLARE xD006_Flag_Isento_ICMS varchar(1);
    DECLARE xD006_Flag_Somar_Despesas_Base_IPI ENUM('S','N') DEFAULT 'N';
    DECLARE xD070_Data_Vencimento date;
    DECLARE xFator_ICMS DECIMAL(19,10) DEFAULT 0;
    DECLARE xD006_Flag_Somar_Fator_ICMS_Dif_Base VARCHAR(1) DEFAULT 'N';

      SELECT T004_T003_Id,
           T003_Flag_ACP,
           D006_Flag_Forma_Calculo_COFINS,
           D006_Flag_Forma_Calculo_PIS,
           D006_Flag_Somar_IPI_Base_ICMS,
           D006_Flag_Somar_Despesas_Base_ST,
           T004_Percentual_Desconto/100,
           T004_Percentual_Desconto/100,
           T004_Percentual_Desconto_Bruto/100,
           T004_C004_Id,
           T003_Casas_Decimais_Preco,
           D006_Flag_Isento_ICMS,
           (100 - T004_ST_ICMS_Interno) / 100,
           D006_Flag_Somar_Fator_ICMS_Dif_Base,
           D006_Flag_Somar_Despesas_Base_IPI
      INTO xT003_Id,
           xFLag_ACP,
           xD006_Flag_Forma_Calculo_COFINS,
           xD006_Flag_Forma_Calculo_PIS,
           xD006_Flag_Somar_IPI_Base_ICMS,
           xD006_Flag_Somar_Despesas_Base_ST,
           xPercentual_Desconto,
           xPercentual_Desconto_Confirmado,
           xPercentual_Desconto_Bruto,
           xC004_Id,
           xT003_Casas_Decimais_Preco,
           xD006_Flag_Isento_ICMS,
           xFator_ICMS,
           xD006_Flag_Somar_Fator_ICMS_Dif_Base,
           xD006_Flag_Somar_Despesas_Base_IPI
      FROM T004
      LEFT JOIN D006 ON D006_Id=T004_D006_Id
      LEFT JOIN T003 ON T003_Id=T004_T003_Id
      WHERE T004_Id=xT004_Id;

      SELECT C004_Casas_Decimais_Preco,
           C004_Flag_Rateio_Frete
        INTO xC004_Casas_Decimais_Preco,
           xC004_Flag_Rateio_Frete
        FROM C004
       WHERE C004_Id=xC004_Id;

      SELECT C031_Valor INTO xC031_Somar_ICMS_Preco_Venda FROM C031 WHERE C031_Campo = 'somarICMSPrecoVenda' AND C031_C004_Id = xC004_Id;
      SELECT C031_Valor INTO xC031_Somar_PIS_Preco_Venda FROM C031 WHERE C031_Campo = 'somarPISPrecoVenda' AND C031_C004_Id = xC004_Id;
      SELECT C031_Valor INTO xC031_Somar_COFINS_Preco_Venda FROM C031 WHERE C031_Campo = 'somarCOFINSPrecoVenda' AND C031_C004_Id = xC004_Id;
      SELECT C031_Valor INTO xC031_Somar_IRPJ_Preco_Venda FROM C031 WHERE C031_Campo = 'somarIRPJPrecoVenda' AND C031_C004_Id = xC004_Id;
      SELECT C031_Valor INTO xC031_Somar_CSLL_Preco_Venda FROM C031 WHERE C031_Campo = 'somarCSLLPrecoVenda' AND C031_C004_Id = xC004_Id;
      SELECT C031_Valor INTO xC031_Desabilitar_Cotacao_Emergencia FROM C031 WHERE C031_Campo = 'desabilitarCotacaoEmergencia' AND C031_C004_Id = xC004_Id;

      if !(xPercentual_Desconto>0 OR xPercentual_Desconto<0) THEN
         SELECT T003_Percentual_Desconto/100,
                T003_Percentual_Desconto_Confirmado/100
           INTO xPercentual_Desconto,
                xPercentual_Desconto_Confirmado
         FROM T003
         LEFT JOIN C004 ON C004_Id=T003_C004_Id
         WHERE T003_Id=xT003_Id;
      END IF;

      SELECT SUM(T202_Valor_Unitario) INTO xValor_Total_Agregados FROM T202 WHERE T202_T004_Id=xT004_Id;

      IF xPercentual_Desconto IS NULL THEN
         set xPercentual_Desconto = 0;
      END IF;
      IF xPercentual_Desconto_Bruto IS NULL THEN
         set xPercentual_Desconto_Bruto = 0;
      END IF;
       IF xPercentual_Desconto_Confirmado IS NULL THEN
         set xPercentual_Desconto_Confirmado = 0;
      END IF;

      SELECT IFNULL(D070_Data_Vencimento,'0000-00-00')
        INTO xD070_Data_Vencimento
      FROM D070
      LEFT JOIN D069 ON D070_Id=D069_D070_Id
      LEFT JOIN D009 ON D009_Id=D009_D069_Id
      LEFT JOIN T004 ON D009_Id=T004_D009_Id
      WHERE T004_Id=xT004_Id LIMIT 1;

      UPDATE T004
      LEFT JOIN D009 ON D009_Id=T004_D009_Id
      LEFT JOIN D049 ON D049_Id=D009_D049_Id      
         SET T004_Valor_Desconto_Unitario = if((xPercentual_Desconto>0 or xPercentual_Desconto<0) and T004_Valor_Preco_Original>0,T004_Valor_Preco_Original*xPercentual_Desconto,0),
             T004_Valor_Desconto_Unitario_Bruto = if(xPercentual_Desconto_Bruto>0 or xPercentual_Desconto_Bruto<0,T004_Valor_Preco_Original_Bruto*xPercentual_Desconto_Bruto,0),
             T004_Valor_Desconto_Unitario_Confirmado = if(xPercentual_Desconto_Confirmado>0 or xPercentual_Desconto_Confirmado<0,T004_Valor_Preco_Original*xPercentual_Desconto_Confirmado,0),
             T004_Valor_Total_Desconto=IF(IFNULL(D049_Flag_Tipo,'') != 'CP' OR xC031_Desabilitar_Cotacao_Emergencia='S' OR (D009_Quantidade_Estoque+D009_Quantidade_OC) >= T004_Quantidade OR T004_T219_Id > 0 OR (D009_Valor_Custo_Tabela > 0 AND xD070_Data_Vencimento > CURRENT_DATE()), T004_Valor_Desconto_Unitario*T004_Quantidade, 0),
             T004_Valor_Total_Desconto_Confirmado=IF(IFNULL(D049_Flag_Tipo,'') != 'CP' OR xC031_Desabilitar_Cotacao_Emergencia='S' OR (D009_Quantidade_Estoque+D009_Quantidade_OC) >= T004_Quantidade OR T004_T219_Id > 0, T004_Valor_Desconto_Unitario_Confirmado*T004_Quantidade, 0),
             T004_Valor_Preco_Unitario = if(T004_Valor_Preco_Original>0,round(T004_Valor_Preco_Original - T004_Valor_Desconto_Unitario,xT003_Casas_Decimais_Preco),T004_Valor_Preco_Unitario),
             T004_Valor_Preco_Bruto = if(T004_Valor_Preco_Original_Bruto>0,round(T004_Valor_Preco_Original_Bruto - T004_Valor_Desconto_Unitario_Bruto,xT003_Casas_Decimais_Preco),T004_Valor_Preco_Bruto),
             T004_Valor_Preco_Unitario_Confirmado = round(T004_Valor_Preco_Original - T004_Valor_Desconto_Unitario_Confirmado,xT003_Casas_Decimais_Preco),
             T004_Valor_Total_Preco = if(xFLag_ACP=4,truncate(T004_Valor_Preco_Unitario*T004_Quantidade,2),T004_Valor_Preco_Unitario*T004_Quantidade),
             T004_Valor_Total_Preco_Confirmado = if(xFLag_ACP=4,truncate(T004_Valor_Preco_Unitario_Confirmado*T004_Quantidade_Confirmada,2), T004_Valor_Preco_Unitario_Confirmado*T004_Quantidade_Confirmada),
             T004_Valor_Total_Preco_Sem_Desconto =  if(xFLag_ACP=4,truncate(T004_Valor_Preco_Original * T004_Quantidade,2),T004_Valor_Preco_Original * T004_Quantidade),
             T004_Valor_Total_Preco_Sem_Desconto_Confirmado =  if(xFLag_ACP=4,truncate(T004_Valor_Preco_Original * T004_Quantidade_Confirmada,2), T004_Valor_Preco_Original * T004_Quantidade_Confirmada),
             T004_Valor_Total_Agregados=xValor_Total_Agregados,
             T004_Aliquota_PIS=if(T004_Flag_Isento_PIS='S',0,T004_Aliquota_PIS),
             T004_Aliquota_COFINS=if(T004_Flag_Isento_COFINS='S',0,T004_Aliquota_COFINS),
             T004_Valor_Base_PIS=if(T004_Flag_Isento_PIS='S',0,if(xD006_Flag_Forma_Calculo_PIS='L',if(T004_Quantidade_Confirmada>0,T004_Valor_Total_Preco_Confirmado-T004_Valor_Total_Custo_Confirmado,T004_Valor_Total_Preco-T004_Valor_Total_Custo),if(T004_Quantidade_Confirmada>0,T004_Valor_Total_Preco_Confirmado,T004_Valor_Total_Preco))),
             T004_Valor_PIS=if(T004_Aliquota_PIS>0,T004_Valor_Base_PIS*(T004_Aliquota_PIS/100),0),
             T004_Valor_Base_COFINS=if(T004_Flag_Isento_COFINS='S',0,if(xD006_Flag_Forma_Calculo_COFINS='L',if(T004_Quantidade_Confirmada>0,T004_Valor_Total_Preco_Confirmado-T004_Valor_Total_Custo_Confirmado,T004_Valor_Total_Preco-T004_Valor_Total_Custo),if(T004_Quantidade_Confirmada>0,T004_Valor_Total_Preco_Confirmado,T004_Valor_Total_Preco))),
             T004_Valor_COFINS=if(T004_Aliquota_COFINS>0,T004_Valor_Base_COFINS*(T004_Aliquota_COFINS/100),0),
             T004_Aliquota_IRPJ=if(T004_Flag_Isento_IRPJ='S',0,T004_Aliquota_IRPJ),
             T004_Aliquota_CSLL=if(T004_Flag_Isento_CSLL='S',0,T004_Aliquota_CSLL),
             T004_Valor_Base_IRPJ=if(T004_Aliquota_IRPJ>0,if(T004_Quantidade_Confirmada>0,T004_Valor_Total_Preco_Confirmado,T004_Valor_Total_Preco),0),
             T004_Valor_IRPJ=if(T004_Aliquota_IRPJ>0,T004_Valor_Base_IRPJ*(T004_Aliquota_IRPJ/100),0),
             T004_Valor_Base_CSLL=if(T004_Aliquota_CSLL>0,if(T004_Quantidade_Confirmada>0,T004_Valor_Total_Preco_Confirmado,T004_Valor_Total_Preco),0),
             T004_Valor_CSLL=if(T004_Aliquota_CSLL>0,T004_Valor_Base_CSLL*(T004_Aliquota_CSLL/100),0),
             T004_Valor_Trib_Transparencia=IFNULL(T004_Valor_Total_Preco,0)*(IFNULL(T004_Percentual_Trib_Transparencia,0)/100)
       WHERE T004_Id=xT004_Id;

       SELECT (T003_Valor_Frete+T003_Valor_Despesas_Acessorias)/sum(T004_Valor_Preco_Unitario*T004_Quantidade),
                 (T003_Valor_Frete+T003_Valor_Despesas_Acessorias)/sum(T004_Valor_Preco_Unitario*T004_Quantidade_Confirmada),
                 T003_Flag_Revenda_Consumidor
        INTO xMultiplicador_Despesa,
              xMultiplicador_Despesa_Confirmado,
              xFlag_Revenda_Consumidor
        FROM T003
        LEFT JOIN T004 ON T004_T003_Id=T003_Id
       WHERE T003_Id=xT003_Id
       GROUP BY T003_Id;
       IF xMultiplicador_Despesa IS NULL THEN
           set xMultiplicador_Despesa = 0;
       END IF;
       IF xMultiplicador_Despesa_Confirmado IS NULL THEN
           set xMultiplicador_Despesa_Confirmado = 0;
       END IF;
       
        SELECT COUNT(*) INTO xTotal_Itens FROM T004 WHERE T004_T003_Id = xT003_Id;

    SELECT T003_Valor_Frete/SUM(T004_Valor_Preco_Unitario*T004_Quantidade),
        T003_Valor_Frete,
        T003_Valor_Despesas_Acessorias/SUM(T004_Valor_Preco_Unitario*T004_Quantidade)
    INTO xMultiplicador_Frete,
      xValor_Total_Frete,
      xMultiplicador_Despesas_Acessorias
    FROM T003
    LEFT JOIN T004 ON T004_T003_Id=T003_Id
    WHERE T003_Id=xT003_Id
    GROUP BY T003_Id;

    UPDATE T004 SET T004_Valor_Total_Despesas_Acessorias=T004_Valor_Total_Preco*xMultiplicador_Despesas_Acessorias,
        T004_Valor_Despesas_Acessorias_Unitario=T004_Valor_Total_Despesas_Acessorias/T004_Quantidade,
        T004_Valor_Frete_Total=IF(xC004_Flag_Rateio_Frete=1,T004_Valor_Total_Preco*xMultiplicador_Frete,xValor_Total_Frete/xTotal_Itens),
        T004_Valor_Frete_Unitario=T004_Valor_Frete_Total/T004_Quantidade
    WHERE T004_Id=xT004_Id;
       
    UPDATE T004
    SET T004_Valor_Total_Custo=T004_Valor_Custo_Unitario*T004_Quantidade,
                    T004_Valor_Total_Custo_Confirmado=T004_Valor_Custo_Unitario*T004_Quantidade_Confirmada,
                    T004_Peso_Total=T004_Peso_Unitario*T004_Quantidade,
                    T004_Peso_Bruto_Total=T004_Peso_Bruto*T004_Quantidade,
                    T004_Peso_Total_Confirmado=T004_Peso_Unitario*T004_Quantidade_Confirmada,
                    T004_Valor_Total_Despesas=(T004_Valor_Total_Preco*xMultiplicador_Despesa)+IFNULL(xValor_Total_Agregados,0),
                    T004_Valor_Total_Despesas_Confirmado=T004_Valor_Total_Preco_Confirmado*xMultiplicador_Despesa_Confirmado,
                    T004_Valor_Despesas_Unitario=T004_Valor_Total_Despesas/T004_Quantidade,
                    T004_Valor_Despesas_Unitario_Confirmado=T004_Valor_Total_Despesas_Confirmado/T004_Quantidade_Confirmada,
                    T004_Valor_Base_IPI=If(T004_Aliquota_IPI>0,(T004_Valor_Total_Preco+if(xD006_Flag_Somar_Despesas_Base_IPI='S',T004_Valor_Frete_Total+T004_Valor_Total_Despesas_Acessorias,0))*If(T004_Percentual_Reducao_IPI>0,(T004_Percentual_Reducao_IPI/100),1),0),
                    T004_Valor_Base_IPI_Confirmado=IF(T004_Aliquota_IPI>0,(T004_Valor_Total_Preco_Confirmado)*IF(T004_Percentual_Reducao_IPI,(T004_Percentual_Reducao_IPI/100),1),0),
                    T004_Valor_IPI=T004_Valor_Base_IPI*(T004_Aliquota_IPI/100),
                    T004_Valor_IPI_Confirmado=T004_Valor_Base_IPI_Confirmado*(T004_Aliquota_IPI/100),
                    T004_Valor_Total_Custo_Final=(T004_Valor_Total_Preco+if(xD006_Flag_Somar_Despesas_Base_ST='S',T004_Valor_Total_Despesas,0)+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T004_Valor_IPI,0)),
                    T004_Valor_Total_Custo_Final_Confirmado=(T004_Valor_Total_Preco_Confirmado+if(xD006_Flag_Somar_Despesas_Base_ST='S',T004_Valor_Total_Despesas_Confirmado,0)+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T004_Valor_IPI,0)),
                    T004_Valor_Custo_Final_Unitario=T004_Valor_Total_Custo_Final/T004_Quantidade,
                    T004_Valor_Custo_Final_Unitario_Confirmado=T004_Valor_Total_Custo_Final_Confirmado/T004_Quantidade_Confirmada,
                    T004_Valor_Base_ICMS=IF(T004_Aliquota_ICMS>0,T004_Valor_Total_Custo_Final*IF(T004_Percentual_Reducao_ICMS>0,((100-T004_Percentual_Reducao_ICMS)/100),1),0),
                    T004_Valor_Base_ICMS=IF(xD006_Flag_Somar_Fator_ICMS_Dif_Base='S',T004_Valor_Base_ICMS/xFator_ICMS,T004_Valor_Base_ICMS),
                    T004_Valor_Base_ICMS_Confirmado=IF(T004_Aliquota_ICMS>0,T004_Valor_Total_Custo_Final_Confirmado*IF(T004_Percentual_Reducao_ICMS>0,((100-T004_Percentual_Reducao_ICMS)/100),1),0),
                    T004_Valor_Base_ICMS_Desonerado=IF(T004_Aliquota_ICMS_Oculto>0 AND xD006_Flag_Isento_ICMS = 'D',((T004_Valor_Total_Preco+if(xD006_Flag_Somar_Despesas_Base_ST='S',T004_Valor_Total_Despesas,0))*IF(T004_Percentual_Reducao_ICMS>0,((100-T004_Percentual_Reducao_ICMS)/100),1))+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T004_Valor_IPI,0),0),
                    T004_Valor_ICMS=T004_Valor_Base_ICMS*(T004_Aliquota_ICMS/100),
                    T004_Valor_ICMS_Confirmado=T004_Valor_Base_ICMS_Confirmado*(T004_Aliquota_ICMS/100),
                    T004_Valor_ICMS_Desonerado=T004_Valor_Base_ICMS_Desonerado*(T004_Aliquota_ICMS_Oculto/100),
                    T004_Valor_Base_ST=IF(T004_Flag_ST='S',(T004_Valor_Total_Preco+if(xD006_Flag_Somar_Despesas_Base_ST='S',T004_Valor_Total_Despesas,0)+T004_Valor_IPI)*((T004_ST_VA/100)+1),0)*IF(T004_Percentual_Reducao_ICMS_ST>0,((100-T004_Percentual_Reducao_ICMS_ST)/100),1),
                    T004_Valor_ST=IF(T004_Flag_ST='S',(T004_Valor_Base_ST*(T004_ST_ICMS_Interno/100))-((T004_Valor_Total_Custo_Final*IF(T004_Percentual_Reducao_ICMS>0,((100-T004_Percentual_Reducao_ICMS)/100),1))*(T004_ICMS_Interno/100)),0),
                    T004_Valor_ST=IF(T004_Valor_ST<0,0,T004_Valor_ST),
                    T004_Valor_Base_ST_Confirmado=IF(T004_Flag_ST='S',(T004_Valor_Total_Custo_Final_Confirmado+T004_Valor_Despesas_Unitario_Confirmado+T004_Valor_IPI_Confirmado)*((T004_ST_VA/100)+1),0),
                    T004_Valor_ST_Confirmado=IF(T004_Flag_ST='S',(T004_Valor_Base_ST_Confirmado*(T004_ST_ICMS_Interno/100))-((T004_Valor_Total_Custo_Final_Confirmado*IF(T004_Percentual_Reducao_ICMS>0,((100-T004_Percentual_Reducao_ICMS)/100),1))*(T004_ICMS_Interno/100)),0),
                    T004_Valor_ST_Confirmado=IF(T004_Valor_ST_Confirmado<0,0,T004_Valor_ST_Confirmado),
                    T004_IPV=(T004_Valor_Preco_Unitario-
                              IF(xC031_Somar_ICMS_Preco_Venda   = 'S', IFNULL(T004_Valor_ICMS/T004_Quantidade,0), 0)-
                              IF(xC031_Somar_PIS_Preco_Venda    = 'S', IFNULL(T004_Valor_PIS/T004_Quantidade,0), 0)-
                              IF(xC031_Somar_COFINS_Preco_Venda = 'S', IFNULL(T004_Valor_COFINS/T004_Quantidade,0), 0)-
                              IF(xC031_Somar_IRPJ_Preco_Venda   = 'S', IFNULL(T004_Valor_IRPJ/T004_Quantidade,0), 0)-
                              IF(xC031_Somar_CSLL_Preco_Venda   = 'S', IFNULL(T004_Valor_CSLL/T004_Quantidade,0), 0))/T004_Valor_Custo_Unitario,
                    T004_Percentual_Margem=round((((T004_Valor_Preco_Unitario-
                                IF(xC031_Somar_ICMS_Preco_Venda   = 'S', IFNULL(T004_Valor_ICMS/T004_Quantidade,0), 0)-
                                IF(xC031_Somar_PIS_Preco_Venda    = 'S', IFNULL(T004_Valor_PIS/T004_Quantidade,0), 0)-
                                IF(xC031_Somar_COFINS_Preco_Venda = 'S', IFNULL(T004_Valor_COFINS/T004_Quantidade,0), 0)-
                                IF(xC031_Somar_IRPJ_Preco_Venda   = 'S', IFNULL(T004_Valor_IRPJ/T004_Quantidade,0), 0)-
                                IF(xC031_Somar_CSLL_Preco_Venda   = 'S', IFNULL(T004_Valor_CSLL/T004_Quantidade,0), 0))-T004_Valor_Custo_Unitario)/
                              (T004_Valor_Preco_Unitario-
                                IF(xC031_Somar_ICMS_Preco_Venda   = 'S', IFNULL(T004_Valor_ICMS/T004_Quantidade,0), 0)-
                                IF(xC031_Somar_PIS_Preco_Venda    = 'S', IFNULL(T004_Valor_PIS/T004_Quantidade,0), 0)-
                                IF(xC031_Somar_COFINS_Preco_Venda = 'S', IFNULL(T004_Valor_COFINS/T004_Quantidade,0), 0)-
                                IF(xC031_Somar_IRPJ_Preco_Venda   = 'S', IFNULL(T004_Valor_IRPJ/T004_Quantidade,0), 0)-
                                IF(xC031_Somar_CSLL_Preco_Venda   = 'S', IFNULL(T004_Valor_CSLL/T004_Quantidade,0), 0)))*100,2),
                    T004_Valor_Lucro=round((T004_Valor_Total_Custo*T004_IPV)-(T004_Valor_Total_Custo*T004_IPV_Sem_Lucro),2),
                    T004_Total_Cubagem = IFNULL(T004_Cubagem_Unitaria, 0) * IFNULL(T004_Quantidade,0),
                    T004_Valor_Trib_Transparencia=IFNULL(T004_Valor_Total_Preco,0)*(IFNULL(T004_Percentual_Trib_Transparencia,0)/100)
       WHERE T004_Id=xT004_Id;
       CALL T003_Gravar_Totalizacao_4(xT003_Id);
END;


