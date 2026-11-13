CREATE PROCEDURE `T006_Gravar_Totalizacao_4`(xT006_Id int(11))
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
    DECLARE xT005_Casas_Decimais_Preco int(2);
    DECLARE xC004_Id int(10);    
    DECLARE xT185_Qtd_Separado DECIMAL(20,2) DEFAULT 0;
    DECLARE xC031_Somar_ICMS_Preco_Venda varchar(1);
    DECLARE xC031_Somar_PIS_Preco_Venda varchar(1);
    DECLARE xC031_Somar_COFINS_Preco_Venda varchar(1);
    DECLARE xC031_Somar_IRPJ_Preco_Venda varchar(1);
    DECLARE xC031_Somar_CSLL_Preco_Venda varchar(1);
    DECLARE xC031_Desabilitar_Compra_Emergencia varchar(1);
    DECLARE xD006_Flag_Isento_ICMS varchar(1);
    DECLARE xFator_ICMS DECIMAL(19,10) DEFAULT 0;
    DECLARE xD006_Flag_Somar_Fator_ICMS_Dif_Base VARCHAR(1) DEFAULT 'N';
    DECLARE xC031_Ratear_Frete_Venda_No_Custo varchar(1);
    DECLARE xT005_Flag_Frete int(10);
    DECLARE xPreco_Pauta_ST DECIMAL(20,10) DEFAULT '0';
    DECLARE xModalidade varchar(1);
    DECLARE xD006_Flag_Deduz_ICMS_Base_PIS varchar(1);
    DECLARE xD006_Flag_Deduz_ICMS_Base_COFINS varchar(1); 
    DECLARE xT006_D059_Id int(11);
    DECLARE xD006_Flag_Somar_Despesas_Base_PIS ENUM('S','N') DEFAULT 'N';
    DECLARE xD006_Flag_Somar_Despesas_Base_COFINS ENUM('S','N') DEFAULT 'N';
    DECLARE xT006A_Percentual_Reducao_PIS decimal(5,2) default '0';
    DECLARE xT006A_Percentual_Reducao_COFINS decimal(5,2) default '0';
    DECLARE xT006A_Trib_Interna_UF_Dest_Reducao_Base decimal(20,2) default '0';
    DECLARE xD006_Flag_Calcula_FCP_ST varchar(1);
    DECLARE xD006_Flag_Calcula_FCP varchar(1);
    DECLARE xD006_Flag_Calcula_FCP_Retido varchar(1);
    DECLARE xD006_Flag_Deduz_FCP_Base_PIS varchar(1);
    DECLARE xD006_Flag_Deduz_Partilha_ICMS_Destino_Base_PIS varchar(1);
    DECLARE xD006_Flag_Deduz_FCP_Base_COFINS varchar(1);
    DECLARE xD006_Flag_Deduz_Partilha_ICMS_Destino_Base_COFINS varchar(1);
    DECLARE xD006_Flag_Reduz_Base_DIFAL varchar(1) DEFAULT 'N';
    DECLARE xD006_Flag_Tipo_Desconto varchar(1) DEFAULT 'C';
    DECLARE xD006_Flag_Tipo_Desconto_PIS_COFINS varchar(1) DEFAULT 'C';
    DECLARE xD006_Flag_Tipo_Desconto_IPI varchar(1) DEFAULT 'C';
    DECLARE xTotal_Desconto_Produto_Manual DECIMAL(20,2) DEFAULT '0';
    DECLARE xD006_Flag_Deduz_FCP_ST_Valor_ST varchar(1) DEFAULT 'S';
    DECLARE xD006_Flag_Somar_Despesas_Base_IRPJ varchar(1) DEFAULT 'N';
    DECLARE xD006_Flag_Somar_Despesas_Base_CSLL varchar(1) DEFAULT 'N';
    DECLARE xD006_Flag_Somar_ICMS_Diferido varchar(1) DEFAULT 'N';
    DECLARE xT006A_Percentual_Reducao_Base_DIFAL decimal(5,2) DEFAULT '0';

    SELECT ifnull(SUM(T204_Valor_Unitario),0) INTO xValor_Total_Agregados FROM T204 WHERE T204_T006_Id=xT006_Id;
    
    -- GRAVA A CFOP POR ITEM PARA OS QUE AINDA NAO TEM CFOP
    -- update T006 left join T005 on T005_Id=T006_T005_Id set T006_D006_Id=T005_D006_Id where T006_Id=xT006_Id and T006_D006_Id is null;

    SELECT T006_T005_Id,
           D006_Flag_Venda_Compra_Outros,
           D049_Flag_Nacional_Importado,
           D006_Flag_Somar_IPI_Base_ICMS,
           D006_Flag_Somar_Despesas_Base_IPI,
           D006_Flag_Somar_Despesas_Base_ST,
           D006_Flag_Forma_Calculo_COFINS,
           D006_Flag_Forma_Calculo_PIS,
           T005_C004_Id,
           T005_Flag_ACP,
           T134_Reducao_Base_ICMS,
           T134_Id,
           T006_Percentual_Desconto_Bruto/100,
           D006_Flag_Isento_ICMS,
           (100 - T006_ST_ICMS_Interno) / 100,
           D006_Flag_Somar_Fator_ICMS_Dif_Base,
           IFNULL(T005_Flag_Frete,0) AS T005_Flag_Frete,
           (T006_Preco_Pauta_ST * T006_Quantidade),
           T006_Modalidade_ICMS_Substituicao,
           D006_Flag_Deduz_ICMS_Base_PIS,
           D006_Flag_Deduz_ICMS_Base_COFINS,
           T006_D059_Id,
           D006_Flag_Somar_Despesas_Base_PIS,
           D006_Flag_Somar_Despesas_Base_COFINS,
           T006A_Percentual_Reducao_PIS,
           T006A_Percentual_Reducao_COFINS,
           T006A_Trib_Interna_UF_Dest_Reducao_Base,
           D006_Flag_Calcula_FCP_ST, 
           D006_Flag_Calcula_FCP, 
           D006_Flag_Calcula_FCP_Retido, 
           D006_Flag_Deduz_FCP_Base_PIS, 
           D006_Flag_Deduz_Partilha_ICMS_Destino_Base_PIS,
           D006_Flag_Deduz_FCP_Base_COFINS,
           D006_Flag_Deduz_Partilha_ICMS_Destino_Base_COFINS,
           D006_Flag_Reduz_Base_DIFAL,
           D006_Flag_Tipo_Desconto,
           D006_Flag_Deduz_FCP_ST_Valor_ST,
           D006_Flag_Somar_Despesas_Base_IRPJ,
           D006_Flag_Somar_Despesas_Base_CSLL,
           D006_Flag_Tipo_Desconto_PIS_COFINS,
           D006_Flag_Tipo_Desconto_IPI,
           IF(T005_Valor_Desconto_Manual > 0, round((T006_Valor_Preco_Sem_Desconto_Unitario*T006_Quantidade) * (T005_Valor_Desconto_Manual/T005_Valor_Total_Produtos),2),0),
           D006_Flag_Somar_ICMS_Diferido,
           IFNULL(T006A_Percentual_Reducao_Base_DIFAL,0)
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
           xD006_Flag_Isento_ICMS,
           xFator_ICMS,
           xD006_Flag_Somar_Fator_ICMS_Dif_Base,
           xT005_Flag_Frete,
           xPreco_Pauta_ST,
           xModalidade,
           xD006_Flag_Deduz_ICMS_Base_PIS,
           xD006_Flag_Deduz_ICMS_Base_COFINS,
           xT006_D059_Id,
           xD006_Flag_Somar_Despesas_Base_PIS,
           xD006_Flag_Somar_Despesas_Base_COFINS,
           xT006A_Percentual_Reducao_PIS,
           xT006A_Percentual_Reducao_COFINS,
           xT006A_Trib_Interna_UF_Dest_Reducao_Base,
           xD006_Flag_Calcula_FCP_ST, 
           xD006_Flag_Calcula_FCP, 
           xD006_Flag_Calcula_FCP_Retido, 
           xD006_Flag_Deduz_FCP_Base_PIS, 
           xD006_Flag_Deduz_Partilha_ICMS_Destino_Base_PIS,
           xD006_Flag_Deduz_FCP_Base_COFINS,
           xD006_Flag_Deduz_Partilha_ICMS_Destino_Base_COFINS,
           xD006_Flag_Reduz_Base_DIFAL,
           xD006_Flag_Tipo_Desconto,
           xD006_Flag_Deduz_FCP_ST_Valor_ST,
           xD006_Flag_Somar_Despesas_Base_IRPJ,
           xD006_Flag_Somar_Despesas_Base_CSLL,
           xD006_Flag_Tipo_Desconto_PIS_COFINS,
           xD006_Flag_Tipo_Desconto_IPI,
           xTotal_Desconto_Produto_Manual,
           xD006_Flag_Somar_ICMS_Diferido,
           xT006A_Percentual_Reducao_Base_DIFAL
      FROM T006
 LEFT JOIN T005 ON T005_Id=T006_T005_Id
 LEFT JOIN D006 ON D006_Id=T006_D006_Id
 LEFT JOIN T134 ON T134_Id=T005_T134_Id
 LEFT JOIN D009 ON D009_Id=T006_D009_Id
 LEFT JOIN D049 ON D049_Id=D009_D049_Id
 LEFT JOIN T006A ON T006_Id = T006A_T006_Id
     WHERE T006_Id=xT006_Id
     LIMIT 1;

     /*SELECT COUNT(*) INTO xTotal_Itens FROM T006 WHERE T006_T005_Id = xT005_Id;
     
     SELECT C004_Casas_Decimais_Preco,
             C004_Flag_Rateio_Frete
        INTO xC004_Casas_Decimais_Preco,
             xC004_Flag_Rateio_Frete
        FROM C004
       WHERE C004_Id=xC004_Id;
       */
    SELECT C031_Valor INTO xC031_Somar_ICMS_Preco_Venda FROM C031 WHERE C031_Campo = 'somarICMSPrecoVenda' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Somar_PIS_Preco_Venda FROM C031 WHERE C031_Campo = 'somarPISPrecoVenda' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Somar_COFINS_Preco_Venda FROM C031 WHERE C031_Campo = 'somarCOFINSPrecoVenda' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Somar_IRPJ_Preco_Venda FROM C031 WHERE C031_Campo = 'somarIRPJPrecoVenda' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Somar_CSLL_Preco_Venda FROM C031 WHERE C031_Campo = 'somarCSLLPrecoVenda' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Desabilitar_Compra_Emergencia FROM C031 WHERE C031_Campo = 'desabilitarCompraEmergencia' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Ratear_Frete_Venda_No_Custo FROM C031 WHERE C031_Campo = 'ratearFreteVendaNoCusto' AND C031_C004_Id = xC004_Id;

    SELECT T005_Percentual_Desconto/100,
           T005_Flag_Devolucao,
           T005_Flag_Tipo_Devolucao,
           T005_Valor_Frete/sum(T006_Valor_Preco_Unitario*T006_Quantidade),
           T005_Valor_Frete,
           T005_Valor_Despesas_Acessorias/sum(T006_Valor_Preco_Unitario*T006_Quantidade),
           T005_Casas_Decimais_Preco,
           C004_Casas_Decimais_Preco,
           C004_Flag_Rateio_Frete,
           COUNT(*),
           (IF(IF(T005A_Flag_Ratear_Frete_Custo != '',T005A_Flag_Ratear_Frete_Custo,xC031_Ratear_Frete_Venda_No_Custo)='S' AND xT005_Flag_Frete = '0',0,T005_Valor_Frete)+T005_Valor_Despesas_Acessorias)/sum(T006_Valor_Preco_Unitario*T006_Quantidade),
           T005_Flag_Revenda_Consumidor
      INTO xPercentual_Desconto,
           xFlag_Devolucao,
           xFlag_Tipo_Devolucao,
           xMultiplicador_Frete,
           xValor_Total_Frete,
           xMultiplicador_Despesas_Acessorias,
           xT005_Casas_Decimais_Preco,
           xC004_Casas_Decimais_Preco,
           xC004_Flag_Rateio_Frete,
           xTotal_Itens,
           xMultiplicador_Despesa,
           xFlag_Revenda_Consumidor
      FROM T005
      LEFT JOIN C004 ON C004_Id=T005_C004_Id
      LEFT JOIN T006 ON T006_T005_Id=T005_Id
      LEFT JOIN T005A ON T005_Id = T005A_T005_Id
     WHERE T005_Id=xT005_Id;

     
     IF xPercentual_Desconto IS NULL THEN
          SET xPercentual_Desconto = 0;
     END IF;  

     IF xTotal_Desconto_Produto_Manual IS NULL THEN
          SET xTotal_Desconto_Produto_Manual = 0;
     END IF;

     IF xT005_Casas_Decimais_Preco > 0 THEN
          SET xC004_Casas_Decimais_Preco = xT005_Casas_Decimais_Preco;
     END IF;
    
    /*UPDATE T006 SET T006_Valor_Desconto_Unitario = if(xPercentual_Desconto>0,T006_Valor_Preco_Sem_Desconto_Unitario*xPercentual_Desconto,0),
            T006_Valor_Desconto_Unitario_Bruto = if(xPercentual_Desconto_Bruto>0 or xPercentual_Desconto_Bruto<0,T006_Valor_Preco_Original_Bruto*xPercentual_Desconto_Bruto,0),
                    T006_Valor_Total_Desconto = T006_Valor_Desconto_Unitario*T006_Quantidade,
                    T006_Valor_Preco_Unitario = round(T006_Valor_Preco_Sem_Desconto_Unitario - T006_Valor_Desconto_Unitario,xC004_Casas_Decimais_Preco),
                    T006_Valor_Total_Preco = if(xFlag_ACP=4,truncate(T006_Valor_Preco_Unitario*T006_Quantidade,2),T006_Valor_Preco_Unitario*T006_Quantidade),
                    T006_Valor_Total_Preco_Sem_Desconto = if(xFlag_ACP=4,truncate(T006_Valor_Preco_Original*T006_Quantidade,2),T006_Valor_Preco_Original*T006_Quantidade),
                    T006_Valor_Preco_Bruto = round(T006_Valor_Preco_Original_Bruto - T006_Valor_Desconto_Unitario_Bruto,xC004_Casas_Decimais_Preco)
              WHERE T006_Id=xT006_Id;
              */

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
    END IF;
    /*
    --ELSE
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
    --END IF;
  */

    /*SELECT (T005_Valor_Frete+T005_Valor_Despesas_Acessorias)/sum(T006_Valor_Preco_Unitario*T006_Quantidade),
           T005_Flag_Revenda_Consumidor
      INTO
        xMultiplicador_Despesa,
        xFlag_Revenda_Consumidor
      FROM T005
      LEFT JOIN T006 ON T006_T005_Id=T005_Id
     WHERE T005_Id=xT005_Id
     GROUP BY T005_Id;*/
    IF xMultiplicador_Despesa IS NULL THEN
       set xMultiplicador_Despesa = 0;
    END IF;
    UPDATE T006
              LEFT JOIN T008 ON T008_T006_Id=T006_Id
              LEFT JOIN T007 ON T008_T007_Id = T007_Id
              LEFT JOIN T005 ON T005_Id=T006_T005_Id
              LEFT JOIN C004 ON C004_Id=T005_C004_Id
              LEFT JOIN D024 ON D024_Id=T005_D024_Id
              LEFT JOIN D018 AS UFOrigem ON UFOrigem.D018_Id=C004_D018_Id
              LEFT JOIN D018 AS UFDestino ON UFDestino.D018_Id=D024_D018_Id
              LEFT JOIN D053 ON D053_D018_Id=D024_D018_Id AND D053_C004_Id=T005_C004_Id
                SET 
                    T006_Valor_Desconto_Unitario = if(xPercentual_Desconto>0,T006_Valor_Preco_Sem_Desconto_Unitario*xPercentual_Desconto,0),
                    T006_Valor_Desconto_Unitario_Bruto = if(xPercentual_Desconto_Bruto>0 or xPercentual_Desconto_Bruto<0,T006_Valor_Preco_Original_Bruto*xPercentual_Desconto_Bruto,0),
                    T006_Valor_Total_Desconto = T006_Valor_Desconto_Unitario*T006_Quantidade,
                    T006_Valor_Preco_Unitario = round(T006_Valor_Preco_Sem_Desconto_Unitario - T006_Valor_Desconto_Unitario,xC004_Casas_Decimais_Preco),
                    T006_Valor_Total_Preco = if(xFlag_ACP=4,truncate(T006_Valor_Preco_Unitario*T006_Quantidade,2),T006_Valor_Preco_Unitario*T006_Quantidade),
                    T006_Valor_Total_Preco_Sem_Desconto = if(xFlag_ACP=4,truncate(T006_Valor_Preco_Original*T006_Quantidade,2),T006_Valor_Preco_Original*T006_Quantidade),
                    T006_Valor_Preco_Bruto = round(T006_Valor_Preco_Original_Bruto - T006_Valor_Desconto_Unitario_Bruto,xC004_Casas_Decimais_Preco),
                    T006_Valor_Total_Despesas_Acessorias=T006_Valor_Total_Preco*xMultiplicador_Despesas_Acessorias,
                    T006_Valor_Despesas_Acessorias_Unitario=T006_Valor_Total_Despesas_Acessorias/T006_Quantidade,
                    T006_Valor_Total_Frete=IF(xC004_Flag_Rateio_Frete=1,T006_Valor_Total_Preco*xMultiplicador_Frete,xValor_Total_Frete/xTotal_Itens),
                    T006_Valor_Frete_Unitario=T006_Valor_Total_Frete/T006_Quantidade,
                    T006_Quantidade_Estoque=IFNULL(T006_Quantidade_Estoque,0),
                    T006_Valor_Total_Custo = T006_Valor_Custo_Unitario*T006_Quantidade,
                    T006_Peso_Total = T006_Peso_Unitario*T006_Quantidade,
                    T006_Peso_Bruto_Total = T006_Peso_Bruto*T006_Quantidade,
                    T006_Valor_Total_Despesas=(T006_Valor_Total_Preco*xMultiplicador_Despesa)+xValor_Total_Agregados,
                    T006_Valor_Despesas_Unitario=T006_Valor_Total_Despesas/T006_Quantidade,
                    T006_Valor_Total_Agregados=xValor_Total_Agregados,
                    T006_Valor_Base_IPI=If(T006_Aliquota_IPI>0,(T006_Valor_Total_Preco+if(xD006_Flag_Somar_Despesas_Base_IPI='S',T006_Valor_Total_Frete+T006_Valor_Total_Despesas_Acessorias,0) - if(xD006_Flag_Tipo_Desconto_IPI = 'I', xTotal_Desconto_Produto_Manual, 0))*If(T006_Percentual_Reducao_IPI>0,(T006_Percentual_Reducao_IPI/100),1),0),
                    T006_Valor_IPI=T006_Valor_Base_IPI*(T006_Aliquota_IPI/100),
                    T006_Valor_Total_Custo_Final=(T006_Valor_Total_Preco+if(xD006_Flag_Somar_Despesas_Base_ST='S',T006_Valor_Total_Despesas,0)+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T006_Valor_IPI,0) - if(xD006_Flag_Tipo_Desconto = 'I', xTotal_Desconto_Produto_Manual,0)),
                    T006_Valor_Custo_Final_Unitario=T006_Valor_Total_Custo_Final/T006_Quantidade,
                    T006_Valor_Base_ICMS_Sem_Reducao=If(T006_Aliquota_ICMS>0,IF(xD006_Flag_Somar_ICMS_Diferido='S',(T006_Valor_Total_Custo_Final/((100-T006_Aliquota_ICMS)/100)), T006_Valor_Total_Custo_Final),0),
                    T006_Valor_Base_ICMS=If(T006_Aliquota_ICMS>0,T006_Valor_Base_ICMS_Sem_Reducao*if(T006_Percentual_Reducao_ICMS>0,((100-T006_Percentual_Reducao_ICMS)/100),1),0),
                    T006_Valor_Base_ICMS=IF(xD006_Flag_Somar_Fator_ICMS_Dif_Base='S',T006_Valor_Base_ICMS/xFator_ICMS,T006_Valor_Base_ICMS),
                    T006_Valor_Base_ICMS_Diferido=T006_Valor_Base_ICMS_Sem_Reducao-T006_Valor_Base_ICMS,
                    T006_Valor_ICMS_Diferido=T006_Valor_Base_ICMS_Diferido*(T006_Aliquota_ICMS/100),
                    T006_Valor_Base_ICMS_Oculto=If(T006_Aliquota_ICMS_Oculto>0,T006_Valor_Total_Custo_Final*if(T006_Percentual_Reducao_ICMS_Oculto>0,((100-T006_Percentual_Reducao_ICMS_Oculto)/100),1),0),
                    #T006_Valor_Base_ICMS_Desonerado=IF(T006_Aliquota_ICMS_Oculto>0 AND xD006_Flag_Isento_ICMS = 'D',T006_Valor_Total_Custo_Final-IF(T006_Percentual_Reducao_ICMS>0,T006_Valor_Base_ICMS,0),0),
                    T006_Valor_Base_ICMS_Desonerado=if(xD006_Flag_Isento_ICMS = 'D', TRIB_Base_ICMS_Desonerado(T006_Valor_Total_Custo_Final,T006_Valor_IPI,T006_Percentual_Reducao_ICMS_Oculto,T006_Valor_Base_ICMS,T006_Aliquota_ICMS_Oculto,xD006_Flag_Somar_IPI_Base_ICMS,xT006_D059_Id),0),                   
                    T006_Valor_ICMS=T006_Valor_Base_ICMS*(T006_Aliquota_ICMS/100),
                    T006_Valor_ICMS_Oculto=T006_Valor_Base_ICMS_Oculto*(T006_Aliquota_ICMS_Oculto/100),
                    #T006_Valor_ICMS_Desonerado=T006_Valor_Base_ICMS_Desonerado*(T006_Aliquota_ICMS_Oculto/100),                    
                    T006_Valor_ICMS_Desonerado= TRIB_Valor_ICMS_Desonerado(T006_Valor_Base_ICMS_Desonerado,T006_Aliquota_ICMS_Oculto,T006_Percentual_Reducao_ICMS),                    
                    T006_Valor_Base_ICMS_Substituicao=if(T006_Flag_ST='S',TRIB_Base_ST(T006_Flag_ST,T006_Valor_Total_Preco,if(xD006_Flag_Somar_Despesas_Base_ST='S',T006_Valor_Total_Despesas,0),T006_Valor_IPI,T006_ST_VA,UFOrigem.D018_UF,UFDestino.D018_UF,IFNULL(T006_Flag_Destino_Produto,0),IFNULL(D053_Calculo_ST_Por_Dentro,'N'),IFNULL(T006_ST_ICMS_Interno,0),IFNULL(T006_Valor_ICMS,0),IFNULL(T006_ICMS_Inter_Estadual,0),IFNULL(xPreco_Pauta_ST,0),IFNULL(T006_Modalidade_ICMS_Substituicao,0)),0),
                    T006_Valor_ICMS_Substituicao=(T006_Valor_Base_ICMS_Substituicao*(T006_ST_ICMS_Interno/100)),
                    T006_Valor_ICMS_Substituicao_Retencao=TRIB_Valor_ST(T006_Valor_Base_ICMS_Substituicao,T006_Percentual_Reducao_ICMS_ST,T006_ST_ICMS_Interno,T006_Valor_Total_Custo_Final,T006_Percentual_Reducao_ICMS,T006_ICMS_Inter_Estadual,T006_ST_VA,UFOrigem.D018_UF,UFDestino.D018_UF,IFNULL(T006_Flag_Destino_Produto,0),IFNULL(xPreco_Pauta_ST,0),IFNULL(T006_Modalidade_ICMS_Substituicao,0)),
                    T006_Aliquota_IRPJ=if(T006_Flag_Isento_IRPJ='S',0,T006_Aliquota_IRPJ),
                    T006_Aliquota_CSLL=if(T006_Flag_Isento_CSLL='S',0,T006_Aliquota_CSLL),
                    T006_Valor_Base_IRPJ=if(T006_Aliquota_IRPJ>0,T006_Valor_Total_Preco + IF(xD006_Flag_Somar_Despesas_Base_IRPJ = 'S', T006_Valor_Total_Despesas,0),0),
                    T006_Valor_IRPJ=if(T006_Aliquota_IRPJ>0,T006_Valor_Base_IRPJ*(T006_Aliquota_IRPJ/100),0),
                    T006_Valor_Base_CSLL=if(T006_Aliquota_CSLL>0,T006_Valor_Total_Preco + IF(xD006_Flag_Somar_Despesas_Base_CSLL = 'S', T006_Valor_Total_Despesas,0),0),
                    T006_Valor_CSLL=if(T006_Aliquota_CSLL>0,T006_Valor_Base_CSLL*(T006_Aliquota_CSLL/100),0),
                    #T006_Valor_ICMS_Credito=if(ifnull(xT134_Id,0)<=0,0.00,If(T006_Aliquota_ICMS_Credito>0,(T006_Valor_Total_Custo_Final+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T006_Valor_IPI,0))-(
                    T006_Valor_ICMS_Credito = IF(IFNULL(xT134_Id, 0) <= 0,0.00,IF(T006_Aliquota_ICMS_Credito > 0,(T006_Valor_Total_Custo_Final + IF(xD006_Flag_Somar_IPI_Base_ICMS = 'S',T006_Valor_IPI,0)),0) * (T006_Aliquota_ICMS_Credito / 100)),
                    T006_Total_Cubagem = IFNULL(T006_Cubagem_Unitaria,0) * IFNULL(T006_Quantidade,0),
                    T006_Valor_Trib_Transparencia=IFNULL(T006_Valor_Total_Preco,0)*(IFNULL(T006_Percentual_Trib_Transparencia,0)/100),
                    T006_Percentual_Partilha_ICMS=IF((T006_ST_ICMS_Interno * IF(xT006A_Trib_Interna_UF_Dest_Reducao_Base > 0,((100-xT006A_Trib_Interna_UF_Dest_Reducao_Base)/100),1)) - T006_ICMS_Inter_Estadual > 0, T006_ST_ICMS_Interno - T006_ICMS_Inter_Estadual, 0),
                    #T006_Valor_Base_Partilha_ICMS=If(T006_Aliquota_ICMS>0, IF(xD006_Flag_Reduz_Base_DIFAL = 'N', T006_Valor_Base_ICMS_Sem_Reducao, T006_Valor_Base_ICMS) * IF(xT006A_Trib_Interna_UF_Dest_Reducao_Base > 0,((100-xT006A_Trib_Interna_UF_Dest_Reducao_Base)/100),1),0),
                    #T006_Valor_Base_Partilha_ICMS=IF(xD006_Flag_Somar_Fator_ICMS_Dif_Base='S',T006_Valor_Base_Partilha_ICMS/xFator_ICMS, T006_Valor_Base_Partilha_ICMS),                  
                    #T006_Valor_Partilha_ICMS_Destino=IF(T006_Percentual_Partilha_ICMS>0,((T006_Valor_Base_Partilha_ICMS*(T006_ST_ICMS_Interno/100)) - T006_Valor_ICMS) * (T006_Percentual_Partilha_ICMS_Destino/100),0),
                    T006_Valor_Base_Partilha_ICMS=IF(
                        T006_Aliquota_ICMS > 0,
                        TRIB_Base_Difal(
                            T006_Aliquota_ICMS,
                            IF(
                                xT006A_Percentual_Reducao_Base_DIFAL > 0,
                                /* Nova configuração: reduz somente a base do DIFAL. */
                                T006_Valor_Base_ICMS_Sem_Reducao
                                * ((100 - xT006A_Percentual_Reducao_Base_DIFAL) / 100),
                                /* Zero: mantém o comportamento legado. */
                                IF(
                                    xD006_Flag_Reduz_Base_DIFAL = 'N',
                                    T006_Valor_Base_ICMS_Sem_Reducao,
                                    T006_Valor_Base_ICMS
                                )
                            ),
                            xT006A_Trib_Interna_UF_Dest_Reducao_Base,
                            T006_Valor_ICMS,
                            T006_ST_ICMS_Interno,
                            T006_ICMS_Inter_Estadual,
                            T006_Percentual_Reducao_ICMS,
                            D053_Calculo_Difal_Por_Dentro,
                            xD006_Flag_Somar_Fator_ICMS_Dif_Base,
                            xFator_ICMS
                        ),
                        0
                    ),
                    T006_Valor_Partilha_ICMS_Destino=IF(T006_Percentual_Partilha_ICMS>0,TRIB_Valor_Difal_Destino(T006_Valor_Base_Partilha_ICMS, T006_Valor_ICMS, T006_Aliquota_ICMS, T006_Percentual_Partilha_ICMS_Destino, T006_ST_ICMS_Interno, T006_ICMS_Inter_Estadual, T006_Percentual_Reducao_ICMS),0),
                    T006_Valor_Base_PIS=if(T006_Flag_Isento_PIS='S',0,if(xD006_Flag_Forma_Calculo_PIS='L',T006_Valor_Total_Preco-T006_Valor_Total_Custo,T006_Valor_Total_Preco)),
                    T006_Valor_Base_PIS=if(T006_Valor_Base_PIS>0,if(xD006_Flag_Deduz_ICMS_Base_PIS='S',T006_Valor_Base_PIS-T006_Valor_ICMS,T006_Valor_Base_PIS)+if(xD006_Flag_Somar_Despesas_Base_PIS = 'S',T006_Valor_Total_Frete+T006_Valor_Total_Despesas_Acessorias,0) - if(xD006_Flag_Tipo_Desconto_PIS_COFINS = 'I', xTotal_Desconto_Produto_Manual,0) - if(xD006_Flag_Deduz_Partilha_ICMS_Destino_Base_PIS = 'S',T006_Valor_Partilha_ICMS_Destino,0),0),                   
                    T006_Valor_Base_PIS= T006_Valor_Base_PIS * IF(xT006A_Percentual_Reducao_PIS > 0, ((100-xT006A_Percentual_Reducao_PIS)/100) ,1),
                    T006_Aliquota_PIS=if(T006_Flag_Isento_PIS='S',0,T006_Aliquota_PIS),
                    T006_Valor_PIS=if(T006_Aliquota_PIS>0,T006_Valor_Base_PIS*(T006_Aliquota_PIS/100),0),
                    T006_Valor_Base_COFINS=if(T006_Flag_Isento_COFINS='S',0,if(xD006_Flag_Forma_Calculo_COFINS='L',T006_Valor_Total_Preco-T006_Valor_Total_Custo,T006_Valor_Total_Preco)),
                    T006_Valor_Base_COFINS=if(T006_Valor_Base_COFINS>0,if(xD006_Flag_Deduz_ICMS_Base_COFINS='S',T006_Valor_Base_COFINS-T006_Valor_ICMS,T006_Valor_Base_COFINS)+if(xD006_Flag_Somar_Despesas_Base_COFINS = 'S',T006_Valor_Total_Frete+T006_Valor_Total_Despesas_Acessorias,0) - if(xD006_Flag_Tipo_Desconto_PIS_COFINS = 'I', xTotal_Desconto_Produto_Manual,0) - if(xD006_Flag_Deduz_Partilha_ICMS_Destino_Base_COFINS = 'S',T006_Valor_Partilha_ICMS_Destino,0),0),
                    T006_Valor_Base_COFINS=T006_Valor_Base_COFINS * IF(xT006A_Percentual_Reducao_COFINS > 0, ((100-xT006A_Percentual_Reducao_COFINS)/100) ,1),
                    T006_Aliquota_COFINS=if(T006_Flag_Isento_COFINS='S',0,T006_Aliquota_COFINS),
                    T006_Valor_COFINS=if(T006_Aliquota_COFINS>0,T006_Valor_Base_COFINS*(T006_Aliquota_COFINS/100),0),
                    T006_Valor_Partilha_ICMS_Origem=IF(T006_Percentual_Partilha_ICMS>0,((T006_Valor_Base_Partilha_ICMS*(T006_ST_ICMS_Interno/100)) - T006_Valor_ICMS) * (T006_Percentual_Partilha_ICMS_Origem/100),0),        
                    T006_IPV = VEN_Calculo_IPV(0, 0, xT005_Id, xT006_Id, 0, 0, xC031_Somar_ICMS_Preco_Venda, xC031_Somar_PIS_Preco_Venda, xC031_Somar_COFINS_Preco_Venda, xC031_Somar_IRPJ_Preco_Venda, xC031_Somar_CSLL_Preco_Venda, T006_Valor_Preco_Unitario, T006_Valor_Custo_Unitario, T006_Quantidade, T006_Valor_ICMS, T006_Valor_PIS, T006_Valor_COFINS, T006_Valor_IRPJ, T006_Valor_CSLL), 
                    #T006_IPV=(T006_Valor_Preco_Unitario-
                    #          IF(xC031_Somar_ICMS_Preco_Venda   = 'S', IFNULL(T006_Valor_ICMS/T006_Quantidade,0), 0)-
                    #          IF(xC031_Somar_PIS_Preco_Venda    = 'S', IFNULL(T006_Valor_PIS/T006_Quantidade,0), 0)-
                    #          IF(xC031_Somar_COFINS_Preco_Venda = 'S', IFNULL(T006_Valor_COFINS/T006_Quantidade,0), 0)-
                    #          IF(xC031_Somar_IRPJ_Preco_Venda   = 'S', IFNULL(T006_Valor_IRPJ/T006_Quantidade,0), 0)-
                    #          IF(xC031_Somar_CSLL_Preco_Venda   = 'S', IFNULL(T006_Valor_CSLL/T006_Quantidade,0), 0))/T006_Valor_Custo_Unitario,
                    
                    T006_Percentual_Margem=VEN_Calculo_Margem(0, 0, 0, xT006_Id, 0, 0, xC031_Somar_ICMS_Preco_Venda, xC031_Somar_PIS_Preco_Venda, xC031_Somar_COFINS_Preco_Venda, xC031_Somar_IRPJ_Preco_Venda, xC031_Somar_CSLL_Preco_Venda, T006_Valor_Preco_Unitario, T006_Valor_Custo_Unitario, T006_Quantidade, T006_Valor_ICMS, T006_Valor_PIS, T006_Valor_COFINS, T006_Valor_IRPJ, T006_Valor_CSLL), 
                    #T006_Percentual_Margem=round((((T006_Valor_Preco_Unitario-
                    #            IF(xC031_Somar_ICMS_Preco_Venda   = 'S', IFNULL(T006_Valor_ICMS/T006_Quantidade,0), 0)-
                    #            IF(xC031_Somar_PIS_Preco_Venda    = 'S', IFNULL(T006_Valor_PIS/T006_Quantidade,0), 0)-
                    #            IF(xC031_Somar_COFINS_Preco_Venda = 'S', IFNULL(T006_Valor_COFINS/T006_Quantidade,0), 0)-
                    #            IF(xC031_Somar_IRPJ_Preco_Venda   = 'S', IFNULL(T006_Valor_IRPJ/T006_Quantidade,0), 0)-
                    #            IF(xC031_Somar_CSLL_Preco_Venda   = 'S', IFNULL(T006_Valor_CSLL/T006_Quantidade,0), 0))-T006_Valor_Custo_Unitario)/
                    #          (T006_Valor_Preco_Unitario-
                    #            IF(xC031_Somar_ICMS_Preco_Venda   = 'S', IFNULL(T006_Valor_ICMS/T006_Quantidade,0), 0)-
                    #            IF(xC031_Somar_PIS_Preco_Venda    = 'S', IFNULL(T006_Valor_PIS/T006_Quantidade,0), 0)-
                    #            IF(xC031_Somar_COFINS_Preco_Venda = 'S', IFNULL(T006_Valor_COFINS/T006_Quantidade,0), 0)-
                    #            IF(xC031_Somar_IRPJ_Preco_Venda   = 'S', IFNULL(T006_Valor_IRPJ/T006_Quantidade,0), 0)-
                    #           IF(xC031_Somar_CSLL_Preco_Venda   = 'S', IFNULL(T006_Valor_CSLL/T006_Quantidade,0), 0)))*100,2),
                    T006_Valor_Lucro=round((T006_Valor_Total_Custo*T006_IPV)-(T006_Valor_Total_Custo*T006_IPV_Sem_Lucro),2)
              WHERE T006_Id=xT006_Id AND (T008_Id IS NULL OR T007_Flag_Cancelada = 'S' OR T007_Flag_Inutilizado = 'S');
              -- T006_Valor_Total_Desconto=IF(IFNULL(D049_Flag_Tipo,'') != 'CP' OR xC031_Desabilitar_Compra_Emergencia='S' OR (T006_Quantidade_Estoque+T006_Quantidade_OC) >= T006_Quantidade OR T006_T219_Id > 0 OR T006_T225_Id > 0, T006_Valor_Desconto_Unitario*T006_Quantidade, 0)

     UPDATE T006A 
  LEFT JOIN T006 ON T006_Id = T006A_T006_Id
        SET T006A_Valor_Base_FCP_ST=if(xD006_Flag_Calcula_FCP_ST = 'S', T006_Valor_Base_ICMS_Substituicao,0),
            T006A_Valor_FCP_ST=IF(T006_Valor_ICMS_Substituicao_Retencao > 0, T006A_Valor_Base_FCP_ST*(T006A_Percentual_FCP/100),0),
            T006_Valor_ICMS_Substituicao_Retencao=if(xD006_Flag_Deduz_FCP_ST_Valor_ST = 'S', T006_Valor_ICMS_Substituicao_Retencao-IFNULL(T006A_Valor_FCP_ST,0),T006_Valor_ICMS_Substituicao_Retencao),                    
            T006A_Valor_Base_FCP_Dest=T006_Valor_Base_Partilha_ICMS,
            T006A_Valor_FCP_Partilha=IF(T006_Percentual_Partilha_ICMS > 0, T006A_Valor_Base_FCP_Dest*(T006A_Percentual_FCP/100),0),
            T006A_Valor_Base_FCP=if(xD006_Flag_Calcula_FCP = 'S', T006_Valor_Base_ICMS,0),
            T006A_Valor_FCP=T006A_Valor_Base_FCP*(T006A_Percentual_FCP/100),
            T006A_Valor_Total_Desconto_Manual = xTotal_Desconto_Produto_Manual
      WHERE T006A_T006_Id=xT006_Id; 
     CALL T006_Gravar_Totalizacao_4_Reforma(xT006_Id);
END;
