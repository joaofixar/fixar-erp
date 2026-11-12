CREATE PROCEDURE `T006_Gravar_Totalizacao`(xT006_Id int(11))
BEGIN
    DECLARE xT005_Id int;
    DECLARE xPercentual_Desconto DECIMAL(20,10) DEFAULT 0;
    DECLARE xPercentual_Desconto_Bruto DECIMAL(20,10) DEFAULT 0;
    DECLARE xMultiplicador_Despesa DECIMAL(20,10) DEFAULT 0;
    DECLARE xMultiplicador_Despesas_Acessorias DECIMAL(20,10) DEFAULT 0;
    DECLARE xMultiplicador_Frete DECIMAL(20,10) DEFAULT 0;
    DECLARE xFlag_Revenda_Consumidor VARCHAR(1);
    DECLARE xFlag_Devolucao VARCHAR(1);
    DECLARE xFlag_Tipo_Devolucao VARCHAR(10);
    DECLARE xValor_Total_Frete DECIMAL(19,2) DEFAULT 0;
    DECLARE xValor_Total_Despesas_Acessorias DECIMAL(10,2) DEFAULT 0;
    DECLARE xValor_Total_Agregados DECIMAL(10,2) DEFAULT 0;
    DECLARE xValor_Total_Desconto DECIMAL(10,2) DEFAULT 0;
    DECLARE xFlag_ST VARCHAR(3);
    DECLARE xFlag_Venda_Compra_Outros VARCHAR(1);
    DECLARE xAliquota_ICMS_1 DECIMAL(5,2);

    DECLARE xFlag_ACP varchar(1);

    DECLARE xT134_Id int(10) default '0';
    DECLARE xT134_Reducao_Base_ICMS decimal(10,2) default '0';

    DECLARE xD006_Flag_Somar_IPI_Base_ICMS ENUM('S','N') DEFAULT 'N';
    DECLARE xD006_Flag_Somar_Despesas_Base_IPI ENUM('S','N') DEFAULT 'N';
    DECLARE xD006_Flag_Somar_Despesas_Base_ST ENUM('S','N') DEFAULT 'N';
    DECLARE xD006_Flag_Isento_PIS ENUM('S','N') DEFAULT 'N';
    DECLARE xD006_Situacao_Tributaria_PIS VARCHAR(2) default '01';
    DECLARE xD006_Flag_Isento_COFINS ENUM('S','N') DEFAULT 'N';
    DECLARE xD006_Situacao_Tributaria_COFINS VARCHAR(2) default '01';
    DECLARE xD059_Codigo_ST varchar(3);
    DECLARE xC004_Percentual_PIS decimal(6,2) DEFAULT 0;
    DECLARE xC004_Percentual_COFINS decimal(6,2) DEFAULT 0;
    DECLARE xC004_Flag_Rateio_Frete VARCHAR(1) DEFAULT '1';
    DECLARE xTotal_Itens INT(5);
    DECLARE xD049_Flag_Nacional_Importado varchar(1);

    DECLARE xD006_Flag_Forma_Calculo_COFINS varchar(1) default 'V';
    DECLARE xD006_Flag_Forma_Calculo_PIS varchar(1) default 'V';

    DECLARE xC004_Casas_Decimais_Preco int(2);
    DECLARE xC004_Id int(10);
    
    DECLARE xT185_Qtd_Separado DECIMAL(20,2) DEFAULT 0;
    DECLARE xC031_Somar_ICMS_Preco_Venda varchar(1);
    DECLARE xC031_Somar_PIS_Preco_Venda varchar(1);
    DECLARE xC031_Somar_COFINS_Preco_Venda varchar(1);
    DECLARE xC031_Desabilitar_Compra_Emergencia varchar(1);
    DECLARE xD006_Flag_Isento_ICMS varchar(1);

    SELECT ifnull(SUM(T204_Valor_Unitario),0) INTO xValor_Total_Agregados FROM T204 WHERE T204_T006_Id=xT006_Id;
    
    -- GRAVA A CFOP POR ITEM PARA OS QUE AINDA NAO TEM CFOP
    update T006 left join T005 on T005_Id=T006_T005_Id set T006_D006_Id=T005_D006_Id where T006_Id=xT006_Id and T006_D006_Id is null;

    SELECT T006_T005_Id,
           D006_Flag_Venda_Compra_Outros,
           D049_Flag_Nacional_Importado,
           D006_Flag_Somar_IPI_Base_ICMS,
           D006_Flag_Somar_Despesas_Base_IPI,
           D006_Flag_Somar_Despesas_Base_ST,
           D006_Flag_Forma_Calculo_COFINS,
           D006_Flag_Forma_Calculo_PIS,
           T006_C004_Id,
           T005_Flag_ACP,
           T134_Reducao_Base_ICMS,
           T134_Id,
           T006_Percentual_Desconto_Bruto/100,
           D006_Flag_Isento_ICMS
      INTO xT005_Id,
           xFlag_Venda_Compra_Outros,
           xD049_Flag_Nacional_Importado,
           xD006_Flag_Somar_IPI_Base_ICMS,
           xD006_Flag_Somar_Despesas_Base_IPI,
           xD006_Flag_Somar_Despesas_Base_ST,
           xD006_Flag_Forma_Calculo_COFINS,
           xD006_Flag_Forma_Calculo_PIS,
           xC004_Id,
           xFlag_ACP,
           xT134_Reducao_Base_ICMS,
           xT134_Id,
           xPercentual_Desconto_Bruto,
           xD006_Flag_Isento_ICMS
      FROM T006
      LEFT JOIN T005 ON T005_Id=T006_T005_Id
      LEFT JOIN D006 ON D006_Id=T006_D006_Id
      LEFT JOIN T134 ON T134_Id=T005_T134_Id
      LEFT JOIN D024 ON D024_Id=T005_D024_Id
      LEFT JOIN D020 ON D020_Id=D024_D020_Id
      LEFT JOIN D009 ON D009_Id=T006_D009_Id
      LEFT JOIN D049 ON D049_Id=D009_D049_Id
      LEFT JOIN D001 ON D001_Id=D049_D001_Id
     WHERE T006_Id=xT006_Id
     LIMIT 1;

     SELECT COUNT(*) INTO xTotal_Itens FROM T006 WHERE T006_T005_Id = xT005_Id;
     
     SELECT C004_Casas_Decimais_Preco,
             C004_Flag_Rateio_Frete
        INTO xC004_Casas_Decimais_Preco,
             xC004_Flag_Rateio_Frete
        FROM C004
       WHERE C004_Id=xC004_Id;

    SELECT T005_Percentual_Desconto/100,
           T005_Flag_Devolucao,
           T005_Flag_Tipo_Devolucao
      INTO xPercentual_Desconto,
           xFlag_Devolucao,
           xFlag_Tipo_Devolucao
      FROM T005
      LEFT JOIN C004 ON C004_Id=T005_C004_Id
     WHERE T005_Id=xT005_Id;

    SELECT C031_Valor INTO xC031_Somar_ICMS_Preco_Venda FROM C031 WHERE C031_Campo = 'somarICMSPrecoVenda' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Somar_PIS_Preco_Venda FROM C031 WHERE C031_Campo = 'somarPISPrecoVenda' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Somar_COFINS_Preco_Venda FROM C031 WHERE C031_Campo = 'somarCOFINSPrecoVenda' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Desabilitar_Compra_Emergencia FROM C031 WHERE C031_Campo = 'desabilitarCompraEmergencia' AND C031_C004_Id = xC004_Id;
     
    IF xPercentual_Desconto IS NULL THEN
       set xPercentual_Desconto = 0;
    END IF;   
    
    UPDATE T006 SET T006_Valor_Desconto_Unitario = if(xPercentual_Desconto>0,T006_Valor_Preco_Sem_Desconto_Unitario*xPercentual_Desconto,0),
    				        T006_Valor_Desconto_Unitario_Bruto = if(xPercentual_Desconto_Bruto>0 or xPercentual_Desconto_Bruto<0,T006_Valor_Preco_Original_Bruto*xPercentual_Desconto_Bruto,0),
                    T006_Valor_Total_Desconto = T006_Valor_Desconto_Unitario*T006_Quantidade,
                    T006_Valor_Preco_Unitario = round(T006_Valor_Preco_Sem_Desconto_Unitario - T006_Valor_Desconto_Unitario,xC004_Casas_Decimais_Preco),
                    T006_Valor_Total_Preco = if(xFlag_ACP=4,truncate(T006_Valor_Preco_Unitario*T006_Quantidade,2),T006_Valor_Preco_Unitario*T006_Quantidade),
                    T006_Valor_Total_Preco_Sem_Desconto = if(xFlag_ACP=4,truncate(T006_Valor_Preco_Original*T006_Quantidade,2),T006_Valor_Preco_Original*T006_Quantidade),
                    T006_Valor_Preco_Bruto = round(T006_Valor_Preco_Original_Bruto - T006_Valor_Desconto_Unitario_Bruto,xC004_Casas_Decimais_Preco)
              WHERE T006_Id=xT006_Id;

    IF xFlag_Devolucao='S' AND xFlag_Tipo_Devolucao='EMITIDA' THEN
       UPDATE T006 SET T006_Valor_Total_Frete=T006_Valor_Frete_Unitario*T006_Quantidade,
                       T006_Valor_Total_Despesas_Acessorias=T006_Valor_Despesas_Acessorias_Unitario*T006_Quantidade,
                       T006_Valor_Total_Desconto=T006_Valor_Desconto_Unitario*T006_Quantidade
              WHERE T006_T005_Id=xT005_Id;
       SELECT SUM(T006_Valor_Total_Frete),
              SUM(T006_Valor_Total_Despesas_Acessorias),
              SUM(T006_Valor_Total_Desconto)
         INTO xValor_Total_Frete,
              xValor_Total_Despesas_Acessorias,
              xValor_Total_Desconto
         FROM T006
         WHERE T006_T005_Id=xT005_Id
         GROUP BY T006_T005_Id;
         UPDATE T005
         SET T005_Valor_Frete=xValor_Total_Frete,
              T005_Valor_Despesas_Acessorias=xValor_Total_Despesas_Acessorias
         WHERE T005_Id=xT005_Id;
    ELSE
           SELECT T005_Valor_Frete/sum(T006_Valor_Preco_Unitario*T006_Quantidade),
                  T005_Valor_Frete,
                  T005_Valor_Despesas_Acessorias/sum(T006_Valor_Preco_Unitario*T006_Quantidade)
           INTO xMultiplicador_Frete,
                xValor_Total_Frete,
                xMultiplicador_Despesas_Acessorias
         FROM T005
         LEFT JOIN T006 ON T006_T005_Id=T005_Id
         WHERE T005_Id=xT005_Id
         GROUP BY T005_Id;
        UPDATE T006 SET T006_Valor_Total_Despesas_Acessorias=T006_Valor_Total_Preco*xMultiplicador_Despesas_Acessorias,
                        T006_Valor_Despesas_Acessorias_Unitario=T006_Valor_Total_Despesas_Acessorias/T006_Quantidade,
                        T006_Valor_Total_Frete=IF(xC004_Flag_Rateio_Frete=1,T006_Valor_Total_Preco*xMultiplicador_Frete,xValor_Total_Frete/xTotal_Itens),
                        T006_Valor_Frete_Unitario=T006_Valor_Total_Frete/T006_Quantidade
                  WHERE T006_Id=xT006_Id;
    END IF;
    SELECT (T005_Valor_Frete+T005_Valor_Despesas_Acessorias)/sum(T006_Valor_Preco_Unitario*T006_Quantidade),
           T005_Flag_Revenda_Consumidor
      INTO
        xMultiplicador_Despesa,
        xFlag_Revenda_Consumidor
      FROM T005
      LEFT JOIN T006 ON T006_T005_Id=T005_Id
     WHERE T005_Id=xT005_Id
     GROUP BY T005_Id;
    IF xMultiplicador_Despesa IS NULL THEN
       set xMultiplicador_Despesa = 0;
    END IF;
    UPDATE T006
    LEFT JOIN D009 ON D009_Id=T006_D009_Id
    LEFT JOIN D049 ON D049_Id=D009_D049_Id
                SET T006_Valor_Total_Custo = T006_Valor_Custo_Unitario*T006_Quantidade,
                    T006_Peso_Total = T006_Peso_Unitario*T006_Quantidade,
                    T006_Peso_Bruto_Total = T006_Peso_Bruto*T006_Quantidade,
                    T006_Valor_Total_Despesas=(T006_Valor_Total_Preco*xMultiplicador_Despesa)+xValor_Total_Agregados,
                    T006_Valor_Despesas_Unitario=T006_Valor_Total_Despesas/T006_Quantidade,
                    T006_Valor_Total_Agregados=xValor_Total_Agregados,
                    T006_Valor_Base_IPI=If(T006_Aliquota_IPI>0,(T006_Valor_Total_Preco+if(xD006_Flag_Somar_Despesas_Base_IPI='S',T006_Valor_Total_Frete+T006_Valor_Total_Despesas_Acessorias,0))*If(T006_Percentual_Reducao_IPI>0,(T006_Percentual_Reducao_IPI/100),1),0),
                    T006_Valor_IPI=T006_Valor_Base_IPI*(T006_Aliquota_IPI/100),
                    T006_Valor_Total_Custo_Final=(T006_Valor_Total_Preco+if(xD006_Flag_Somar_Despesas_Base_ST='S',T006_Valor_Total_Despesas,0)+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T006_Valor_IPI,0)),
                    T006_Valor_Custo_Final_Unitario=T006_Valor_Total_Custo_Final/T006_Quantidade,
                    T006_Valor_Base_ICMS=If(T006_Aliquota_ICMS>0,T006_Valor_Total_Custo_Final*if(T006_Percentual_Reducao_ICMS>0,((100-T006_Percentual_Reducao_ICMS)/100),1),0),
                    T006_Valor_Base_ICMS_Oculto=If(T006_Aliquota_ICMS_Oculto>0,T006_Valor_Total_Custo_Final*if(T006_Percentual_Reducao_ICMS_Oculto>0,((100-T006_Percentual_Reducao_ICMS_Oculto)/100),1),0),
                    T006_Valor_Base_ICMS_Desonerado=IF(T006_Aliquota_ICMS_Oculto>0 AND xD006_Flag_Isento_ICMS = 'D',T006_Valor_Total_Custo_Final-IF(T006_Percentual_Reducao_ICMS>0,T006_Valor_Base_ICMS,0),0),
                    T006_Valor_ICMS=T006_Valor_Base_ICMS*(T006_Aliquota_ICMS/100),
                    T006_Valor_ICMS_Oculto=T006_Valor_Base_ICMS_Oculto*(T006_Aliquota_ICMS_Oculto/100),
                    T006_Valor_ICMS_Desonerado=T006_Valor_Base_ICMS_Desonerado*(T006_Aliquota_ICMS_Oculto/100),                    
                    T006_Valor_Base_ICMS_Substituicao=IF(T006_Flag_ST='S',(T006_Valor_Total_Preco+if(xD006_Flag_Somar_Despesas_Base_ST='S',T006_Valor_Total_Despesas,0)+T006_Valor_IPI)*((T006_ST_VA/100)+1),0),
                    T006_Valor_ICMS_Substituicao=(T006_Valor_Base_ICMS_Substituicao*(T006_ST_ICMS_Interno/100)),
                    T006_Valor_ICMS_Substituicao_Retencao=((T006_Valor_Base_ICMS_Substituicao*if(T006_Percentual_Reducao_ICMS_ST>0,((100-T006_Percentual_Reducao_ICMS_ST)/100),1))*(T006_ST_ICMS_Interno/100))-((T006_Valor_Total_Custo_Final*if(T006_Percentual_Reducao_ICMS>0,((100-T006_Percentual_Reducao_ICMS)/100),1))*(T006_ICMS_Inter_Estadual/100)),
                    T006_Valor_ICMS_Substituicao_Retencao=IF(T006_Valor_ICMS_Substituicao_Retencao<0,0,T006_Valor_ICMS_Substituicao_Retencao),
                    T006_Valor_Base_PIS=if(T006_Flag_Isento_PIS='S',0,if(xD006_Flag_Forma_Calculo_PIS='L',T006_Valor_Total_Preco-T006_Valor_Total_Custo,T006_Valor_Total_Preco)),
                    T006_Aliquota_PIS=if(T006_Flag_Isento_PIS='S',0,T006_Aliquota_PIS),
                    T006_Valor_PIS=if(T006_Aliquota_PIS>0,T006_Valor_Base_PIS*(T006_Aliquota_PIS/100),0),
                    T006_Valor_Base_COFINS=if(T006_Flag_Isento_COFINS='S',0,if(xD006_Flag_Forma_Calculo_COFINS='L',T006_Valor_Total_Preco-T006_Valor_Total_Custo,T006_Valor_Total_Preco)),
                    T006_Aliquota_COFINS=if(T006_Flag_Isento_COFINS='S',0,T006_Aliquota_COFINS),
                    T006_Valor_COFINS=if(T006_Aliquota_COFINS>0,T006_Valor_Base_COFINS*(T006_Aliquota_COFINS/100),0),
                    T006_Valor_ICMS_Credito=if(xT134_Id<=0 or xT134_Id='' or ifnull(xT134_Id,0)<=0,T006_Valor_ICMS_Credito,If(T006_Aliquota_ICMS_Credito>0,(T006_Valor_Total_Custo_Final+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T006_Valor_IPI,0))-((T006_Valor_Total_Custo_Final+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T006_Valor_IPI,0))*if(xT134_Reducao_Base_ICMS>0,(xT134_Reducao_Base_ICMS/100),1)),0)*(T006_Aliquota_ICMS_Credito/100)),
                    T006_Total_Cubagem = IFNULL(T006_Cubagem_Unitaria,0) * IFNULL(T006_Quantidade,0),
                    T006_Valor_Trib_Transparencia=IFNULL(T006_Valor_Total_Preco,0)*(IFNULL(T006_Percentual_Trib_Transparencia,0)/100),
                    T006_IPV=(T006_Valor_Preco_Unitario-
                              IF(xC031_Somar_ICMS_Preco_Venda   = 'S', IFNULL(T006_Valor_ICMS/T006_Quantidade,0), 0)-
                              IF(xC031_Somar_PIS_Preco_Venda    = 'S', IFNULL(T006_Valor_PIS/T006_Quantidade,0), 0)-
                              IF(xC031_Somar_COFINS_Preco_Venda = 'S', IFNULL(T006_Valor_COFINS/T006_Quantidade,0), 0))/T006_Valor_Custo_Unitario,
                    T006_Percentual_Margem=round((((T006_Valor_Preco_Unitario-
                                IF(xC031_Somar_ICMS_Preco_Venda   = 'S', IFNULL(T006_Valor_ICMS/T006_Quantidade,0), 0)-
                                IF(xC031_Somar_PIS_Preco_Venda    = 'S', IFNULL(T006_Valor_PIS/T006_Quantidade,0), 0)-
                                IF(xC031_Somar_COFINS_Preco_Venda = 'S', IFNULL(T006_Valor_COFINS/T006_Quantidade,0), 0))-T006_Valor_Custo_Unitario)/
                              (T006_Valor_Preco_Unitario-
                                IF(xC031_Somar_ICMS_Preco_Venda   = 'S', IFNULL(T006_Valor_ICMS/T006_Quantidade,0), 0)-
                                IF(xC031_Somar_PIS_Preco_Venda    = 'S', IFNULL(T006_Valor_PIS/T006_Quantidade,0), 0)-
                                IF(xC031_Somar_COFINS_Preco_Venda = 'S', IFNULL(T006_Valor_COFINS/T006_Quantidade,0), 0)))*100,2),
                    T006_Valor_Lucro=round((T006_Valor_Total_Custo*T006_IPV)-(T006_Valor_Total_Custo*T006_IPV_Sem_Lucro),2),
                    T006_Valor_Total_Desconto=IF(IFNULL(D049_Flag_Tipo,'') != 'CP' OR xC031_Desabilitar_Compra_Emergencia='S' OR (T006_Quantidade_Estoque+T006_Quantidade_OC) >= T006_Quantidade OR T006_T219_Id > 0 OR T006_T225_Id > 0, T006_Valor_Desconto_Unitario*T006_Quantidade, 0)
              WHERE T006_Id=xT006_Id;
    CALL T005_Gravar_Totalizacao_3(xT005_Id);
END;
