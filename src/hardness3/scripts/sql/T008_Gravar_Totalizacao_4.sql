CREATE PROCEDURE `T008_Gravar_Totalizacao_4`(xT008_Id int(11))
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
    DECLARE xFator_ICMS DECIMAL(19,10) DEFAULT 0;
    DECLARE xD006_Flag_Somar_Fator_ICMS_Dif_Base VARCHAR(1) DEFAULT 'N';
    DECLARE xSomaDesconto DECIMAL(10,2) DEFAULT 0;
    DECLARE xtotalDesconto DECIMAL(10,2) DEFAULT 0;
    DECLARE xDiferencaDesconto DECIMAL(10,2) DEFAULT 0;
    DECLARE xT008_Id_Maior_Preco INT(11) DEFAULT 0;
    DECLARE xD006_Flag_Unidade_Tributavel VARCHAR(1) DEFAULT 'N';
    DECLARE xT008_D005_Id INT(10);
    DECLARE xD005_D037_Id_Tributavel INT(10);
    DECLARE xT008_D009_Id INT(10);
    DECLARE xD001_Quantidade_Tributavel DECIMAL(10,3);
    DECLARE xT007_BC_ICMS_ST_Compra DECIMAL(20,2);
    DECLARE xT007_Valor_ICMS_ST_Compra DECIMAL(20,2);
    DECLARE xT007_Valor_ICMS_Compra DECIMAL(20,2);
    DECLARE xT007_BC_ICMS_Compra DECIMAL(20,2);
    DECLARE xPreco_Pauta_ST DECIMAL(20,10) DEFAULT '0';
    DECLARE xModalidade varchar(1);
    DECLARE xD006_Flag_Calcula_FCP_ST varchar(1);
    DECLARE xD006_Flag_Calcula_FCP varchar(1);
    DECLARE xD006_Flag_Calcula_FCP_Retido varchar(1);
    DECLARE xD006_Flag_Deduz_ICMS_Base_PIS varchar(1);
    DECLARE xD006_Flag_Deduz_ICMS_Base_COFINS varchar(1);
    DECLARE xD006_Flag_Deduz_FCP_Base_PIS varchar(1);
    DECLARE xD006_Flag_Deduz_Partilha_ICMS_Destino_Base_PIS varchar(1);
    DECLARE xD006_Flag_Deduz_FCP_Base_COFINS varchar(1);
    DECLARE xD006_Flag_Deduz_Partilha_ICMS_Destino_Base_COFINS varchar(1);
    DECLARE xT008_D059_Id INT(11);
    DECLARE xC031_Atualizar_Aliquotas_Devolucao varchar(1);
    DECLARE xD006_Flag_Somar_Despesas_Base_PIS ENUM('S','N') DEFAULT 'N';
    DECLARE xD006_Flag_Somar_Despesas_Base_COFINS ENUM('S','N') DEFAULT 'N';
    DECLARE xT008A_Percentual_Reducao_PIS decimal(5,2) default '0';
    DECLARE xT008A_Percentual_Reducao_COFINS decimal(5,2) default '0';
    DECLARE xT008A_Trib_Interna_UF_Dest_Reducao_Base decimal(20,2) default '0';
    DECLARE xT008A_Quantidade_Comercial decimal(12,5) default '0';
    DECLARE xT008A_D184_Id INT(11) DEFAULT 0;
    DECLARE xD006_Flag_Somar_Despesas_Base_ST ENUM('S','N') DEFAULT 'N';
    DECLARE xD006_Flag_Reduz_Base_DIFAL varchar(1) DEFAULT 'N';
    DECLARE xD006_Flag_Tipo_Desconto varchar(1) DEFAULT 'C';
    DECLARE xD006_Flag_Tipo_Desconto_PIS_COFINS varchar(1) DEFAULT 'C';
    DECLARE xD006_Flag_Tipo_Desconto_IPI varchar(1) DEFAULT 'C';
    DECLARE xD006_Flag_Deduz_FCP_ST_Valor_ST varchar(1) DEFAULT 'S';
    DECLARE xD006_Flag_Somar_Despesas_Base_IRPJ varchar(1) DEFAULT 'N';
    DECLARE xD006_Flag_Somar_Despesas_Base_CSLL varchar(1) DEFAULT 'N';
    DECLARE xD006_Flag_Somar_ICMS_Diferido varchar(1) DEFAULT 'N';
    DECLARE xT008A_Percentual_Reducao_Base_DIFAL decimal(5,2) DEFAULT '0';

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
           D006_Flag_Isento_ICMS,
           (100 - T008_ST_ICMS_Interno) / 100,
           D006_Flag_Somar_Fator_ICMS_Dif_Base,
           D006_Flag_Unidade_Tributavel,
           T008_D005_Id, 
           T008_D009_Id,
           (T008_Preco_Pauta_ST * T008_Quantidade),
           T008_Modalidade_ICMS_Substituicao,
           D006_Flag_Calcula_FCP_ST,
           D006_Flag_Calcula_FCP,
           D006_Flag_Calcula_FCP_Retido,
           D006_Flag_Deduz_ICMS_Base_PIS,
           D006_Flag_Deduz_ICMS_Base_COFINS,
           T008_D059_Id,
           D006_Flag_Somar_Despesas_Base_PIS,
           D006_Flag_Somar_Despesas_Base_COFINS,
           T008A_Percentual_Reducao_PIS,
           T008A_Percentual_Reducao_COFINS,
           T008A_Trib_Interna_UF_Dest_Reducao_Base,
           T008A_Quantidade_Comercial,
           T008A_D184_Id,
           D006_Flag_Deduz_FCP_Base_PIS,
           D006_Flag_Deduz_Partilha_ICMS_Destino_Base_PIS,
           D006_Flag_Deduz_FCP_Base_COFINS,
           D006_Flag_Deduz_Partilha_ICMS_Destino_Base_COFINS,
           D006_Flag_Somar_Despesas_Base_ST,
           D006_Flag_Reduz_Base_DIFAL,
           D006_Flag_Tipo_Desconto,
           D006_Flag_Deduz_FCP_ST_Valor_ST,
           D006_Flag_Somar_Despesas_Base_IRPJ,
           D006_Flag_Somar_Despesas_Base_CSLL,
           D006_Flag_Tipo_Desconto_PIS_COFINS,
           D006_Flag_Tipo_Desconto_IPI,
           D006_Flag_Somar_ICMS_Diferido,
           IFNULL(T008A_Percentual_Reducao_Base_DIFAL,0)
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
           xD006_Flag_Isento_ICMS,
           xFator_ICMS,
           xD006_Flag_Somar_Fator_ICMS_Dif_Base,
           xD006_Flag_Unidade_Tributavel,
           xT008_D005_Id,
           xT008_D009_Id,
           xPreco_Pauta_ST,
           xModalidade,
           xD006_Flag_Calcula_FCP_ST,
           xD006_Flag_Calcula_FCP,
           xD006_Flag_Calcula_FCP_Retido,
           xD006_Flag_Deduz_ICMS_Base_PIS,
           xD006_Flag_Deduz_ICMS_Base_COFINS,   
           xT008_D059_Id,
           xD006_Flag_Somar_Despesas_Base_PIS,
           xD006_Flag_Somar_Despesas_Base_COFINS,
           xT008A_Percentual_Reducao_PIS,
           xT008A_Percentual_Reducao_COFINS,
           xT008A_Trib_Interna_UF_Dest_Reducao_Base,
           xT008A_Quantidade_Comercial,
           xT008A_D184_Id,
           xD006_Flag_Deduz_FCP_Base_PIS,
           xD006_Flag_Deduz_Partilha_ICMS_Destino_Base_PIS,
           xD006_Flag_Deduz_FCP_Base_COFINS,
           xD006_Flag_Deduz_Partilha_ICMS_Destino_Base_COFINS,
           xD006_Flag_Somar_Despesas_Base_ST,
           xD006_Flag_Reduz_Base_DIFAL,
           xD006_Flag_Tipo_Desconto,
           xD006_Flag_Deduz_FCP_ST_Valor_ST,
           xD006_Flag_Somar_Despesas_Base_IRPJ,
           xD006_Flag_Somar_Despesas_Base_CSLL,
           xD006_Flag_Tipo_Desconto_PIS_COFINS,
           xD006_Flag_Tipo_Desconto_IPI,
           xD006_Flag_Somar_ICMS_Diferido,
           xT008A_Percentual_Reducao_Base_DIFAL
      FROM T008
     LEFT JOIN T007 ON T007_Id=T008_T007_Id
     LEFT JOIN D006 ON D006_Id=T008_D006_Id
     LEFT JOIN D024 ON D024_Id=T007_D024_Id
     LEFT JOIN D020 ON D020_Id=D024_D020_Id
     LEFT JOIN T134 ON T134_Id=T007_T134_Id
     LEFT JOIN D053 ON D053_D018_Id=D020_D018_Id AND D053_C004_Id=T007_C004_Id
     LEFT JOIN T008A ON T008_Id = T008A_T008_Id
      WHERE T008_Id=xT008_Id
       LIMIT 1;

      SELECT C004_Casas_Decimais_Preco
        INTO xC004_Casas_Decimais_Preco
        FROM C004
       WHERE C004_Id=xC004_Id;

      SELECT D005_D037_Id_Tributavel 
        INTO xD005_D037_Id_Tributavel 
        FROM D005
       WHERE D005_Id=xT008_D005_Id;

      SELECT D001_Quantidade_Tributavel
        INTO xD001_Quantidade_Tributavel
        FROM D001
      LEFT JOIN D049 ON D049_D001_Id=D001_Id
      LEFT JOIN D009 ON D009_D049_Id=D049_Id
      WHERE D009_Id=xT008_D009_Id;
       
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

    SELECT SUM(T290_Valor_Base_ICMS_Substituicao), 
           SUM(T290_Valor_ICMS_Substituicao),
           SUM(T290_Base_ICMS_Substituto),
           SUM(T290_Valor_ICMS_Substituto)
    INTO   xT007_BC_ICMS_ST_Compra,
           xT007_Valor_ICMS_ST_Compra,
           xT007_BC_ICMS_Compra,
           xT007_Valor_ICMS_Compra
    FROM T290 
    WHERE T290_T008_Id=xT008_Id;

    SELECT C031_Valor INTO xC031_Somar_ICMS_Preco_Venda FROM C031 WHERE C031_Campo = 'somarICMSPrecoVenda' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Somar_PIS_Preco_Venda FROM C031 WHERE C031_Campo = 'somarPISPrecoVenda' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Somar_COFINS_Preco_Venda FROM C031 WHERE C031_Campo = 'somarCOFINSPrecoVenda' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Somar_IRPJ_Preco_Venda FROM C031 WHERE C031_Campo = 'somarIRPJPrecoVenda' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Somar_CSLL_Preco_Venda FROM C031 WHERE C031_Campo = 'somarCSLLPrecoVenda' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xC031_Atualizar_Aliquotas_Devolucao FROM C031 WHERE C031_Campo = 'atualizarAliquotasDevolucao' AND C031_C004_Id = xC004_Id;

    UPDATE T008 
           LEFT JOIN T007 ON T007_Id=T008_T007_Id 
           SET T008_Valor_Desconto_Unitario = 0,
               T008_Valor_Preco_Unitario = round(T008_Valor_Preco_Sem_Desconto_Unitario - T008_Valor_Desconto_Unitario,xC004_Casas_Decimais_Preco),
               T008_Valor_Total_Preco = if(xFlag_ACP=4,truncate(T008_Valor_Preco_Unitario*T008_Quantidade,2),T008_Valor_Preco_Unitario*T008_Quantidade),
               T008_Valor_Total_Preco_Sem_Desconto = if(xFlag_ACP=4,truncate(T008_Valor_Preco_Unitario * T008_Quantidade,2),T008_Valor_Preco_Unitario * T008_Quantidade),
               T008_Valor_Total_Desconto = round(T008_Valor_Total_Preco*xPercentual_Desconto,2)
         WHERE T008_Id=xT008_Id AND ifnull(T007_Numero_Protocolo_Nfe,'')='';

    SELECT 
        SUM(ROUND(T008_Valor_Total_Desconto,2)), 
        round(T007_Valor_Desconto,2)
    INTO
        xSomaDesconto,
        xtotalDesconto
    FROM T008 
        LEFT JOIN T007 ON T007_Id = T008_T007_Id 
        WHERE T007_Id=xT007_Id;

    IF xSomaDesconto != xtotalDesconto THEN
        SET xDiferencaDesconto = xSomaDesconto - xtotalDesconto;

        SELECT 
            T008_Id 
        INTO
            xT008_Id_Maior_Preco
        FROM T008 
            WHERE T008_T007_Id = xT007_Id 
            ORDER BY T008_Valor_Total_Preco DESC LIMIT 1;

        UPDATE T008 LEFT JOIN T007 ON T007_Id=T008_T007_Id SET T008_Valor_Total_Desconto = T008_Valor_Total_Desconto - (xDiferencaDesconto) WHERE T008_Id=xT008_Id_Maior_Preco AND ifnull(T007_Numero_Protocolo_Nfe,'')='';

    END IF;

    IF (xT008_Flag_Calculo_Manual = 'S') THEN
        UPDATE T008 LEFT JOIN D009 ON D009_Id=T008_D009_Id
        			LEFT JOIN T007 ON T007_Id=T008_T007_Id
                    SET T008_Valor_Total_Custo=T008_Valor_Custo_Unitario*T008_Quantidade,
                        T008_Valor_Total_Despesas=T008_Valor_Total_Preco*xMultiplicador_Despesa,
                        #T008_Valor_Total_Despesas_Acessorias=if((T008_Valor_Total_Preco*xMultiplicador_Despesas_Acessorias)<0.01 and xMultiplicador_Despesas_Acessorias>0,0.01,T008_Valor_Total_Preco*xMultiplicador_Despesas_Acessorias),
                        #T008_Valor_Total_Frete=if(xMultiplicador_Frete>0 and (T008_Valor_Total_Preco*xMultiplicador_Frete)<=0.01,0.01,T008_Valor_Total_Preco*xMultiplicador_Frete),
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
                        
                        T008_IPV=VEN_Calculo_IPV(0, 0, 0, 0, 0, xT008_Id, xC031_Somar_ICMS_Preco_Venda, xC031_Somar_PIS_Preco_Venda, xC031_Somar_COFINS_Preco_Venda, xC031_Somar_IRPJ_Preco_Venda, xC031_Somar_CSLL_Preco_Venda, T008_Valor_Preco_Unitario, T008_Valor_Custo_Unitario, T008_Quantidade, T008_Valor_ICMS, T008_Valor_PIS, T008_Valor_COFINS, T008_Valor_IRPJ, T008_Valor_CSLL),  
                        #T008_IPV=(T008_Valor_Preco_Unitario-
                        #        IF(xC031_Somar_ICMS_Preco_Venda   = 'S', IFNULL(T008_Valor_ICMS/T008_Quantidade,0), 0)-
                        #        IF(xC031_Somar_PIS_Preco_Venda    = 'S', IFNULL(T008_Valor_PIS/T008_Quantidade,0), 0)-
                        #        IF(xC031_Somar_COFINS_Preco_Venda = 'S', IFNULL(T008_Valor_COFINS/T008_Quantidade,0), 0)-
                        #        IF(xC031_Somar_IRPJ_Preco_Venda   = 'S', IFNULL(T008_Valor_IRPJ/T008_Quantidade,0), 0)-
                        #        IF(xC031_Somar_CSLL_Preco_Venda   = 'S', IFNULL(T008_Valor_CSLL/T008_Quantidade,0), 0))/T008_Valor_Custo_Unitario,
                        
                        T008_Percentual_Margem=VEN_Calculo_Margem(0, 0, 0, 0, 0, xT008_Id, xC031_Somar_ICMS_Preco_Venda, xC031_Somar_PIS_Preco_Venda, xC031_Somar_COFINS_Preco_Venda, xC031_Somar_IRPJ_Preco_Venda, xC031_Somar_CSLL_Preco_Venda, T008_Valor_Preco_Unitario, T008_Valor_Custo_Unitario, T008_Quantidade, T008_Valor_ICMS, T008_Valor_PIS, T008_Valor_COFINS, T008_Valor_IRPJ, T008_Valor_CSLL),  
                        #T008_Percentual_Margem=round((((T008_Valor_Preco_Unitario-
                        #        IF(xC031_Somar_ICMS_Preco_Venda   = 'S', IFNULL(T008_Valor_ICMS/T008_Quantidade,0), 0)-
                        #        IF(xC031_Somar_PIS_Preco_Venda    = 'S', IFNULL(T008_Valor_PIS/T008_Quantidade,0), 0)-
                        #        IF(xC031_Somar_COFINS_Preco_Venda = 'S', IFNULL(T008_Valor_COFINS/T008_Quantidade,0), 0)-
                        #        IF(xC031_Somar_IRPJ_Preco_Venda   = 'S', IFNULL(T008_Valor_IRPJ/T008_Quantidade,0), 0)-
                        #        IF(xC031_Somar_CSLL_Preco_Venda   = 'S', IFNULL(T008_Valor_CSLL/T008_Quantidade,0), 0))-T008_Valor_Custo_Unitario)/
                        #      (T008_Valor_Preco_Unitario-
                        #        IF(xC031_Somar_ICMS_Preco_Venda   = 'S', IFNULL(T008_Valor_ICMS/T008_Quantidade,0), 0)-
                        #        IF(xC031_Somar_PIS_Preco_Venda    = 'S', IFNULL(T008_Valor_PIS/T008_Quantidade,0), 0)-
                        #        IF(xC031_Somar_COFINS_Preco_Venda = 'S', IFNULL(T008_Valor_COFINS/T008_Quantidade,0), 0)-
                        #        IF(xC031_Somar_IRPJ_Preco_Venda   = 'S', IFNULL(T008_Valor_IRPJ/T008_Quantidade,0), 0)-
                        #        IF(xC031_Somar_CSLL_Preco_Venda   = 'S', IFNULL(T008_Valor_CSLL/T008_Quantidade,0), 0)))*100,2),
                        T008_Valor_Lucro=round((T008_Valor_Total_Custo*T008_IPV)-(T008_Valor_Total_Custo*T008_IPV_Sem_Lucro),2),
                        T008_Aliquota_PIS=if(T008_Flag_Isento_PIS='S',0,T008_Aliquota_PIS),
                        T008_Aliquota_COFINS=if(T008_Flag_Isento_COFINS='S',0,T008_Aliquota_COFINS),
                        T008_Total_Cubagem = IFNULL(T008_Cubagem_Unitaria,0) * IFNULL(T008_Quantidade,0),
                        T008_Valor_Trib_Transparencia=IFNULL(T008_Valor_Total_Preco_Sem_Desconto,0)*(IFNULL(T008_Percentual_Trib_Transparencia,0)/100),
                        T008_Valor_Trib_Federal=IFNULL(T008_Valor_Total_Preco_Sem_Desconto,0)*(IFNULL(T008_Percentual_Trib_Federal,0)/100),
                        T008_Valor_Trib_Estadual=IFNULL(T008_Valor_Total_Preco_Sem_Desconto,0)*(IFNULL(T008_Percentual_Trib_Estadual,0)/100),
                        T008_Valor_Trib_Municipal=IFNULL(T008_Valor_Total_Preco_Sem_Desconto,0)*(IFNULL(T008_Percentual_Trib_Municipal,0)/100),
                        #T008_Valor_Base_Partilha_ICMS=If(T008_Aliquota_ICMS>0,T008_Valor_Base_ICMS_Sem_Reducao*if(T008_Percentual_Reducao_ICMS>0,((100-T008_Percentual_Reducao_ICMS)/100),1),0),
                        #T008_Percentual_Partilha_ICMS=T008_ST_ICMS_Interno-T008_ICMS_Inter_Estadual,
                        #T008_Valor_Partilha_ICMS_Destino=if(T008_Percentual_Partilha_ICMS>0,(T008_Valor_Base_Partilha_ICMS*(T008_Percentual_Partilha_ICMS/100))*(T008_Percentual_Partilha_ICMS_Destino/100),0),
                        #T008_Valor_Partilha_ICMS_Origem=(T008_Valor_Base_Partilha_ICMS*(T008_Percentual_Partilha_ICMS/100))*(T008_Percentual_Partilha_ICMS_Origem/100),
                        T008_Valor_FCP_Partilha=T008_Valor_Base_Partilha_ICMS*(T008_Percentual_FCP/100),
                        T008_Valor_Base_FCP=if(xD006_Flag_Calcula_FCP = 'S', T008_Valor_Base_ICMS_Impresso,0),
                        T008_Valor_FCP=T008_Valor_Base_FCP*(T008_Percentual_FCP/100),
                        T008_Valor_Base_FCP_Ret=if(xD006_Flag_Calcula_FCP_Retido = 'S',T008_Valor_Base_ICMS_Substituicao_Retido,0),
                        T008_Valor_FCP_Retido=T008_Valor_Base_FCP_Ret*(T008_Percentual_FCP/100),
                        #T008_Valor_Base_FCP_ST=if(xD006_Flag_Calcula_FCP_ST = 'S', T008_Valor_Base_ICMS_Substituicao,0),
                        #T008_Valor_FCP_ST=IF(T008_Valor_ICMS_Substituicao_Retencao > 0, T008_Valor_Base_FCP_ST*(T008_Percentual_FCP/100),0),
                        #T008_Valor_FCP_ST=if(T008_Valor_FCP_ST < 0, T008_Valor_FCP_ST*(-1), T008_Valor_FCP_ST),
                        T008_Valor_Base_FCP_Dest=T008_Valor_Base_Partilha_ICMS,
                        #T008_Aliquota_ST_FCP=T008_Percentual_Partilha_ICMS+T008_Percentual_FCP,
                        T008_Aliquota_ST_FCP= if(T008_ST_ICMS_Interno > 0, T008_ST_ICMS_Interno + IF(IFNULL(T008_Percentual_FCP, 0) > 0, T008_Percentual_FCP, 0), 0),
                        T008_Quantidade_Tributavel=if(xD006_Flag_Unidade_Tributavel='S' AND xD001_Quantidade_Tributavel>0,xD001_Quantidade_Tributavel*T008_Quantidade,if(xT008A_D184_Id > 0 AND xT008A_Quantidade_Comercial > 0, xT008A_Quantidade_Comercial,T008_Quantidade)),
                        T008_Valor_Preco_Sem_Desconto_Unitario_Tributavel=(T008_Valor_Total_Preco_Sem_Desconto/T008_Quantidade_Tributavel),
                        T008_Valor_ICMS_Substituto=xT007_Valor_ICMS_Compra
                    WHERE T008_Id=xT008_Id and T007_Flag_Calcular='S' and (xD006_Flag_Devolucao = 'N' or xC031_Atualizar_Aliquotas_Devolucao = 'S') and ifnull(T007_Numero_Protocolo_Nfe,'')='';

        -- [REFORMA TRIBUTÁRIA] Chama a PROCEDURE para calcular tudo da reforma tributária
        CALL T008_Gravar_Totalizacao_4_Reforma(xT008_Id);
    ELSE
        UPDATE T008 LEFT JOIN D009 ON D009_Id=T008_D009_Id
        			LEFT JOIN T007 ON T007_Id=T008_T007_Id
              LEFT JOIN C004 ON C004_Id=T007_C004_Id
              LEFT JOIN D024 ON D024_Id=T007_D024_Id
              LEFT JOIN D018 AS UFOrigem ON UFOrigem.D018_Id=C004_D018_Id
              LEFT JOIN D018 AS UFDestino ON UFDestino.D018_Id=D024_D018_Id
              LEFT JOIN D053 ON D053_D018_Id=D024_D018_Id AND D053_C004_Id=T008_C004_Id
                    SET T008_Valor_Total_Custo=T008_Valor_Custo_Unitario*T008_Quantidade,
                        T008_Valor_Total_Despesas=T008_Valor_Total_Preco*xMultiplicador_Despesa,
                        T008_Valor_Total_Despesas_Acessorias=if((T008_Valor_Total_Preco*xMultiplicador_Despesas_Acessorias)<0.01 and xMultiplicador_Despesas_Acessorias>0,0.01,T008_Valor_Total_Preco*xMultiplicador_Despesas_Acessorias),
                        T008_Valor_Total_Frete=if(xMultiplicador_Frete>0 and (T008_Valor_Total_Preco*xMultiplicador_Frete)<=0.01,0.01,T008_Valor_Total_Preco*xMultiplicador_Frete),
                        T008_Valor_Total_Seguro=T008_Valor_Total_Preco*xMultiplicador_Seguro,
                        T008_Valor_Base_IPI=If(T008_Aliquota_IPI>0,(T008_Valor_Total_Preco+T008_Valor_Total_Imposto_Importacao+if(xD006_Flag_Somar_Despesas_Base_IPI='S',T008_Valor_Total_Frete+T008_Valor_Total_Seguro+T008_Valor_Total_Despesas_Acessorias,0)-if(xD006_Flag_Tipo_Desconto_IPI = 'I', T008_Valor_Total_Desconto,0))*If(T008_Percentual_Reducao_IPI>0,(T008_Percentual_Reducao_IPI/100),1),0),
                        T008_Valor_IPI=round(T008_Valor_Base_IPI*(T008_Aliquota_IPI/100),2),
                        T008_Valor_Despesas_Unitario=T008_Valor_Total_Despesas/T008_Quantidade,
                        T008_Valor_Despesas_Acessorias_Unitario=T008_Valor_Total_Despesas_Acessorias/T008_Quantidade,
                        T008_Valor_Frete_Unitario=T008_Valor_Total_Frete/T008_Quantidade,
                        T008_Valor_Seguro_Unitario=T008_Valor_Total_Seguro/T008_Quantidade,
                        T008_Peso_Total=T008_Quantidade*T008_Peso_Unitario,
                        T008_Peso_Bruto_Total=T008_Quantidade*T008_Peso_Bruto,
                        T008_Valor_Total_Custo_Final=(T008_Valor_Total_Preco + if(xD006_Flag_Somar_Despesas_Base_ST != 'N', T008_Valor_Total_Despesas, 0) + if(xD006_Flag_Somar_IPI_Base_ICMS='S',T008_Valor_IPI,0) - if(xD006_Flag_Tipo_Desconto = 'I', T008_Valor_Total_Desconto,0)),
                        T008_Valor_Custo_Final_Unitario=T008_Valor_Total_Custo_Final/T008_Quantidade,
                        T008_Valor_Base_ICMS_Sem_Reducao=If(T008_Aliquota_ICMS>0,IF(xD006_Flag_Somar_ICMS_Diferido='S',(T008_Valor_Total_Custo_Final/((100-T008_Aliquota_ICMS)/100)), T008_Valor_Total_Custo_Final),0),
                        T008_Valor_Base_ICMS=If(T008_Aliquota_ICMS>0,T008_Valor_Base_ICMS_Sem_Reducao*if(T008_Percentual_Reducao_ICMS>0,((100-T008_Percentual_Reducao_ICMS)/100),1),0),
                        T008_Valor_Base_ICMS=IF(xD006_Flag_Somar_Fator_ICMS_Dif_Base='S',T008_Valor_Base_ICMS/xFator_ICMS,T008_Valor_Base_ICMS),
                        T008_Valor_Base_ICMS_Diferido=T008_Valor_Base_ICMS_Sem_Reducao-T008_Valor_Base_ICMS,
                        T008_Valor_ICMS_Diferido=T008_Valor_Base_ICMS_Diferido*(T008_Aliquota_ICMS/100),
                        #T008_Valor_Base_ICMS_Desonerado=IF(T008_Aliquota_ICMS_Oculto>0 AND xD006_Flag_Isento_ICMS = 'D',T008_Valor_Total_Custo_Final+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T008_Valor_IPI,0)-IF(T008_Percentual_Reducao_ICMS>0,T008_Valor_Base_ICMS,0),0),
                        T008_Valor_Base_ICMS_Desonerado=if(xD006_Flag_Isento_ICMS = 'D', TRIB_Base_ICMS_Desonerado(T008_Valor_Total_Custo_Final,T008_Valor_IPI,T008_Percentual_Reducao_ICMS,T008_Valor_Base_ICMS,T008_Aliquota_ICMS_Oculto,xD006_Flag_Somar_IPI_Base_ICMS,xT008_D059_Id),0),
                        T008_Valor_ICMS=T008_Valor_Base_ICMS*(T008_Aliquota_ICMS/100),
                        T008_Valor_Base_ICMS_Oculto=If(T008_Aliquota_ICMS_Oculto>0,T008_Valor_Total_Custo_Final*if(T008_Percentual_Reducao_ICMS_Oculto>0,((100-T008_Percentual_Reducao_ICMS_Oculto)/100),1),0),
                        T008_Valor_ICMS_Oculto=T008_Valor_Base_ICMS_Oculto*(T008_Aliquota_ICMS_Oculto/100),
                        #T008_Valor_ICMS_Desonerado=T008_Valor_Base_ICMS_Desonerado*(T008_Aliquota_ICMS_Oculto/100),
                        T008_Valor_ICMS_Desonerado= TRIB_Valor_ICMS_Desonerado(T008_Valor_Base_ICMS_Desonerado,T008_Aliquota_ICMS_Oculto,T008_Percentual_Reducao_ICMS),
                        T008_Valor_Lucro=round((T008_Valor_Total_Custo*T008_IPV)-(T008_Valor_Total_Custo*T008_IPV_Sem_Lucro),2),
                        T008_Valor_Base_ICMS_Substituicao=TRIB_Base_ST(T008_Flag_ST,T008_Valor_Total_Preco,if(xD006_Flag_Somar_Despesas_Base_ST != 'N', T008_Valor_Total_Despesas, 0),T008_Valor_IPI,T008_ST_VA,UFOrigem.D018_UF,UFDestino.D018_UF,IFNULL(T008_Flag_Destino_Produto,0),IFNULL(D053_Calculo_ST_Por_Dentro,'N'),IFNULL(T008_ST_ICMS_Interno,0),IFNULL(T008_Valor_ICMS,0),IFNULL(T008_ICMS_Inter_Estadual,0),IFNULL(xPreco_Pauta_ST,0),IFNULL(T008_Modalidade_ICMS_Substituicao,0)),
                        T008_Valor_ICMS_Substituicao=(T008_Valor_Base_ICMS_Substituicao*(T008_ST_ICMS_Interno/100)),
                        T008_Valor_ICMS_Substituicao_Retencao=TRIB_Valor_ST(T008_Valor_Base_ICMS_Substituicao,T008_Percentual_Reducao_ICMS_ST,T008_ST_ICMS_Interno,T008_Valor_Total_Custo_Final,T008_Percentual_Reducao_ICMS,T008_ICMS_Inter_Estadual,T008_ST_VA,UFOrigem.D018_UF,UFDestino.D018_UF,IFNULL(T008_Flag_Destino_Produto,0),IFNULL(xPreco_Pauta_ST,0),IFNULL(T008_Modalidade_ICMS_Substituicao,0)),
                        T008_Valor_Base_ICMS_Interno=(((T008_Valor_Total_Preco+T008_Valor_Total_Despesas+T008_Valor_IPI)*((T008_ST_VA/100)+1))*if(T008_Percentual_Reducao_ICMS_ST>0,((100-T008_Percentual_Reducao_ICMS_ST)/100),1)),
                        T008_Valor_ICMS_Interno=(T008_Valor_Base_ICMS_Interno*(T008_ST_ICMS_Interno/100)),
                        T008_Valor_Base_ICMS_Inter_Estadual=(T008_Valor_Total_Custo_Final*if(T008_Percentual_Reducao_ICMS>0,((100-T008_Percentual_Reducao_ICMS)/100),1)),
                        T008_Valor_ICMS_Inter_Estadual=((T008_Valor_Base_ICMS_Inter_Estadual*T008_ICMS_Inter_Estadual)/100),
                        T008_Valor_Base_ICMS_Impresso=if(substring(T008_Flag_Situacao_Tributaria,2,2)='51' or substring(T008_Flag_Situacao_Tributaria,2,2)='90' or substring(T008_Flag_Situacao_Tributaria,2,2)='10',T008_Valor_Base_ICMS_Sem_Reducao,T008_Valor_Base_ICMS),
                        #T008_Valor_ICMS_Credito=if(ifnull(xT134_Id,0)<=0,0.00,If(T008_Aliquota_ICMS_Credito>0,(T008_Valor_Total_Custo_Final+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T008_Valor_IPI,0))-((T008_Valor_Total_Custo_Final+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T008_Valor_IPI,0))*if(xT134_Reducao_Base_ICMS>0,(xT134_Reducao_Base_ICMS/100),1)),0)*(T008_Aliquota_ICMS_Credito/100)),
                        T008_Valor_ICMS_Credito=if(ifnull(xT134_Id,0)<=0,0.00,If(T008_Aliquota_ICMS_Credito>0,(T008_Valor_Total_Custo_Final+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T008_Valor_IPI,0)),0)*(T008_Aliquota_ICMS_Credito/100)),
                        T008_Aliquota_IRPJ=if(T008_Flag_Isento_IRPJ='S',0,T008_Aliquota_IRPJ),
                        T008_Aliquota_CSLL=if(T008_Flag_Isento_CSLL='S',0,T008_Aliquota_CSLL),
                        T008_Valor_Base_IRPJ=T008_Valor_Total_Preco + IF(xD006_Flag_Somar_Despesas_Base_IRPJ = 'S',T008_Valor_Total_Despesas,0),
                        T008_Valor_IRPJ=if(T008_Aliquota_IRPJ>0,T008_Valor_Base_IRPJ*(T008_Aliquota_IRPJ/100),0),
                        T008_Valor_Base_CSLL=T008_Valor_Total_Preco + IF(xD006_Flag_Somar_Despesas_Base_CSLL = 'S',T008_Valor_Total_Despesas,0),
                        T008_Valor_CSLL=if(T008_Aliquota_CSLL>0,T008_Valor_Base_CSLL*(T008_Aliquota_CSLL/100),0),
                        T008_Total_Cubagem = IFNULL(T008_Cubagem_Unitaria,0) * IFNULL(T008_Quantidade,0),
                        T008_Valor_Trib_Transparencia=IFNULL(T008_Valor_Total_Preco_Sem_Desconto,0)*(IFNULL(T008_Percentual_Trib_Transparencia,0)/100),
                        T008_Valor_Trib_Federal=IFNULL(T008_Valor_Total_Preco_Sem_Desconto,0)*(IFNULL(T008_Percentual_Trib_Federal,0)/100),
                        T008_Valor_Trib_Estadual=IFNULL(T008_Valor_Total_Preco_Sem_Desconto,0)*(IFNULL(T008_Percentual_Trib_Estadual,0)/100),
                        T008_Valor_Trib_Municipal=IFNULL(T008_Valor_Total_Preco_Sem_Desconto,0)*(IFNULL(T008_Percentual_Trib_Municipal,0)/100),
                        T008_Percentual_Partilha_ICMS=IF((T008_ST_ICMS_Interno * IF(xT008A_Trib_Interna_UF_Dest_Reducao_Base > 0,((100-xT008A_Trib_Interna_UF_Dest_Reducao_Base)/100),1)) - T008_ICMS_Inter_Estadual > 0, T008_ST_ICMS_Interno - T008_ICMS_Inter_Estadual, 0),
                        #T008_Valor_Base_Partilha_ICMS=If(T008_Aliquota_ICMS>0, IF(xD006_Flag_Reduz_Base_DIFAL = 'N', T008_Valor_Base_ICMS_Sem_Reducao, T008_Valor_Base_ICMS) * IF(xT008A_Trib_Interna_UF_Dest_Reducao_Base > 0,((100-xT008A_Trib_Interna_UF_Dest_Reducao_Base)/100),1),0),
                        #T008_Valor_Base_Partilha_ICMS=IF(xD006_Flag_Somar_Fator_ICMS_Dif_Base='S',T008_Valor_Base_Partilha_ICMS/xFator_ICMS, T008_Valor_Base_Partilha_ICMS),
                        #T008_Valor_Partilha_ICMS_Destino=IF(T008_Percentual_Partilha_ICMS>0,((T008_Valor_Base_Partilha_ICMS*(T008_ST_ICMS_Interno/100)) - T008_Valor_ICMS) * (T008_Percentual_Partilha_ICMS_Destino/100),0),
                        T008_Valor_Base_Partilha_ICMS=IF(
                            T008_Aliquota_ICMS > 0,
                            TRIB_Base_Difal(
                                T008_Aliquota_ICMS,
                                IF(
                                    xT008A_Percentual_Reducao_Base_DIFAL > 0,
                                    /* Nova configuração: reduz somente a base do DIFAL. */
                                    T008_Valor_Base_ICMS_Sem_Reducao
                                    * ((100 - xT008A_Percentual_Reducao_Base_DIFAL) / 100),
                                    /* Zero: mantém o comportamento legado. */
                                    IF(
                                        xD006_Flag_Reduz_Base_DIFAL = 'N',
                                        T008_Valor_Base_ICMS_Sem_Reducao,
                                        T008_Valor_Base_ICMS
                                    )
                                ),
                                xT008A_Trib_Interna_UF_Dest_Reducao_Base,
                                T008_Valor_ICMS,
                                T008_ST_ICMS_Interno,
                                T008_ICMS_Inter_Estadual,
                                T008_Percentual_Reducao_ICMS,
                                D053_Calculo_Difal_Por_Dentro,
                                xD006_Flag_Somar_Fator_ICMS_Dif_Base,
                                xFator_ICMS
                            ),
                            0
                        ),
                        T008_Valor_Partilha_ICMS_Destino=IF(T008_Percentual_Partilha_ICMS>0,TRIB_Valor_Difal_Destino(T008_Valor_Base_Partilha_ICMS, T008_Valor_ICMS, T008_Aliquota_ICMS, T008_Percentual_Partilha_ICMS_Destino, T008_ST_ICMS_Interno, T008_ICMS_Inter_Estadual, T008_Percentual_Reducao_ICMS),0),
                        T008_Valor_Base_PIS=if(T008_Flag_Isento_PIS='S',0,if(xD006_Flag_Forma_Calculo_PIS='L',if(T008_Valor_Total_Custo<T008_Valor_Total_Preco,T008_Valor_Total_Preco-T008_Valor_Total_Custo,0),T008_Valor_Total_Preco)),
                        T008_Valor_Base_PIS = if(T008_Valor_Base_PIS > 0,if(xD006_Flag_Deduz_ICMS_Base_PIS = 'S',T008_Valor_Base_PIS - T008_Valor_ICMS,T008_Valor_Base_PIS) - if(xD006_Flag_Deduz_FCP_Base_PIS = 'S',T008_Valor_FCP,0) + if(xD006_Flag_Somar_Despesas_Base_PIS = 'S',T008_Valor_Total_Frete + T008_Valor_Total_Despesas_Acessorias,0) - if(xD006_Flag_Tipo_Desconto_PIS_COFINS = 'I',T008_Valor_Total_Desconto,0) - if(xD006_Flag_Deduz_Partilha_ICMS_Destino_Base_PIS = 'S',T008_Valor_Partilha_ICMS_Destino,0),0),
                        T008_Valor_Base_PIS= T008_Valor_Base_PIS * IF(xT008A_Percentual_Reducao_PIS > 0, ((100-xT008A_Percentual_Reducao_PIS)/100),1),                       
                        T008_Aliquota_PIS=if(T008_Flag_Isento_PIS='S',0,T008_Aliquota_PIS),
                        T008_Valor_PIS=if(T008_Aliquota_PIS>0,T008_Valor_Base_PIS*(T008_Aliquota_PIS/100),0),
                        T008_Valor_Base_COFINS=if(T008_Flag_Isento_COFINS='S',0,if(xD006_Flag_Forma_Calculo_COFINS='L',if(T008_Valor_Total_Custo<T008_Valor_Total_Preco,T008_Valor_Total_Preco-T008_Valor_Total_Custo,0),T008_Valor_Total_Preco)),   
                        T008_Valor_Base_COFINS = if(T008_Valor_Base_COFINS > 0,if(xD006_Flag_Deduz_ICMS_Base_COFINS = 'S',T008_Valor_Base_COFINS - T008_Valor_ICMS,T008_Valor_Base_COFINS) - if(xD006_Flag_Deduz_FCP_Base_COFINS = 'S',T008_Valor_FCP,0) + IF(xD006_Flag_Somar_Despesas_Base_COFINS = 'S',T008_Valor_Total_Frete + T008_Valor_Total_Despesas_Acessorias,0) - if(xD006_Flag_Tipo_Desconto_PIS_COFINS = 'I',T008_Valor_Total_Desconto,0) - if(xD006_Flag_Deduz_Partilha_ICMS_Destino_Base_COFINS = 'S',T008_Valor_Partilha_ICMS_Destino,0),0),                        
                        T008_Valor_Base_COFINS= T008_Valor_Base_COFINS * IF(xT008A_Percentual_Reducao_COFINS > 0, ((100-xT008A_Percentual_Reducao_COFINS)/100),1),
                        T008_Aliquota_COFINS=if(T008_Flag_Isento_COFINS='S',0,T008_Aliquota_COFINS),
                        T008_Valor_COFINS=if(T008_Aliquota_COFINS>0,T008_Valor_Base_COFINS*(T008_Aliquota_COFINS/100),0),
                        T008_Valor_Partilha_ICMS_Origem=IF(T008_Percentual_Partilha_ICMS>0,((T008_Valor_Base_Partilha_ICMS*(T008_ST_ICMS_Interno/100)) - T008_Valor_ICMS) * (T008_Percentual_Partilha_ICMS_Origem/100),0),
                        T008_Valor_FCP_Partilha=IF(T008_Percentual_Partilha_ICMS > 0, T008_Valor_Base_Partilha_ICMS*(T008_Percentual_FCP/100),0),
                        T008_IPV=VEN_Calculo_IPV(0, 0, 0, 0, 0, xT008_Id, xC031_Somar_ICMS_Preco_Venda, xC031_Somar_PIS_Preco_Venda, xC031_Somar_COFINS_Preco_Venda, xC031_Somar_IRPJ_Preco_Venda, xC031_Somar_CSLL_Preco_Venda, T008_Valor_Preco_Unitario, T008_Valor_Custo_Unitario, T008_Quantidade, T008_Valor_ICMS, T008_Valor_PIS, T008_Valor_COFINS, T008_Valor_IRPJ, T008_Valor_CSLL),  
                        #T008_IPV=(T008_Valor_Preco_Unitario-
                        #        IF(xC031_Somar_ICMS_Preco_Venda   = 'S', IFNULL(T008_Valor_ICMS/T008_Quantidade,0), 0)-
                        #        IF(xC031_Somar_PIS_Preco_Venda    = 'S', IFNULL(T008_Valor_PIS/T008_Quantidade,0), 0)-
                        #        IF(xC031_Somar_COFINS_Preco_Venda = 'S', IFNULL(T008_Valor_COFINS/T008_Quantidade,0), 0)-
                        #        IF(xC031_Somar_IRPJ_Preco_Venda   = 'S', IFNULL(T008_Valor_IRPJ/T008_Quantidade,0), 0)-
                        #        IF(xC031_Somar_CSLL_Preco_Venda   = 'S', IFNULL(T008_Valor_CSLL/T008_Quantidade,0), 0))/T008_Valor_Custo_Unitario,
                        
                        T008_Percentual_Margem=VEN_Calculo_Margem(0, 0, 0, 0, 0, xT008_Id, xC031_Somar_ICMS_Preco_Venda, xC031_Somar_PIS_Preco_Venda, xC031_Somar_COFINS_Preco_Venda, xC031_Somar_IRPJ_Preco_Venda, xC031_Somar_CSLL_Preco_Venda, T008_Valor_Preco_Unitario, T008_Valor_Custo_Unitario, T008_Quantidade, T008_Valor_ICMS, T008_Valor_PIS, T008_Valor_COFINS, T008_Valor_IRPJ, T008_Valor_CSLL),  
                        #T008_Percentual_Margem=round((((T008_Valor_Preco_Unitario-
                        #        IF(xC031_Somar_ICMS_Preco_Venda   = 'S', IFNULL(T008_Valor_ICMS/T008_Quantidade,0), 0)-
                        #        IF(xC031_Somar_PIS_Preco_Venda    = 'S', IFNULL(T008_Valor_PIS/T008_Quantidade,0), 0)-
                        #        IF(xC031_Somar_COFINS_Preco_Venda = 'S', IFNULL(T008_Valor_COFINS/T008_Quantidade,0), 0)-
                        #        IF(xC031_Somar_IRPJ_Preco_Venda   = 'S', IFNULL(T008_Valor_IRPJ/T008_Quantidade,0), 0)-
                        #        IF(xC031_Somar_CSLL_Preco_Venda   = 'S', IFNULL(T008_Valor_CSLL/T008_Quantidade,0), 0))-T008_Valor_Custo_Unitario)/
                        #      (T008_Valor_Preco_Unitario-
                        #        IF(xC031_Somar_ICMS_Preco_Venda   = 'S', IFNULL(T008_Valor_ICMS/T008_Quantidade,0), 0)-
                        #        IF(xC031_Somar_PIS_Preco_Venda    = 'S', IFNULL(T008_Valor_PIS/T008_Quantidade,0), 0)-
                        #        IF(xC031_Somar_COFINS_Preco_Venda = 'S', IFNULL(T008_Valor_COFINS/T008_Quantidade,0), 0)-
                        #        IF(xC031_Somar_IRPJ_Preco_Venda   = 'S', IFNULL(T008_Valor_IRPJ/T008_Quantidade,0), 0)-
                        #        IF(xC031_Somar_CSLL_Preco_Venda   = 'S', IFNULL(T008_Valor_CSLL/T008_Quantidade,0), 0)))*100,2),
                    T008_Valor_Base_FCP=if(xD006_Flag_Calcula_FCP = 'S', T008_Valor_Base_ICMS_Impresso,0),
                    T008_Valor_FCP=T008_Valor_Base_FCP*(T008_Percentual_FCP/100),
                    T008_Valor_Base_ICMS_Substituicao_Retido=xT007_BC_ICMS_ST_Compra,
                    T008_Valor_ICMS_Substituicao_Retido=xT007_Valor_ICMS_ST_Compra,
                    T008_Valor_Base_FCP_Ret=if(xD006_Flag_Calcula_FCP_Retido = 'S',T008_Valor_Base_ICMS_Substituicao_Retido,0),
                    T008_Valor_FCP_Retido=T008_Valor_Base_FCP_Ret*(T008_Percentual_FCP/100),
                    T008_Valor_Base_FCP_ST=if(xD006_Flag_Calcula_FCP_ST = 'S', T008_Valor_Base_ICMS_Substituicao,0),
                    T008_Valor_FCP_ST=IF(T008_Valor_ICMS_Substituicao_Retencao > 0, T008_Valor_Base_FCP_ST*(T008_Percentual_FCP/100),0),
                    T008_Valor_FCP_ST=if(T008_Valor_FCP_ST < 0, T008_Valor_FCP_ST*(-1), T008_Valor_FCP_ST),
                    T008_Valor_Base_FCP_Dest=T008_Valor_Base_Partilha_ICMS,
                    #T008_Aliquota_ST_FCP=T008_Percentual_Partilha_ICMS+T008_Percentual_FCP,
                    T008_Aliquota_ST_FCP= if(T008_ST_ICMS_Interno > 0, T008_ST_ICMS_Interno + IF(IFNULL(T008_Percentual_FCP, 0) > 0, T008_Percentual_FCP, 0), 0),
                    T008_Valor_ICMS_Substituicao_Retencao = IF(xD006_Flag_Deduz_FCP_ST_Valor_ST = 'S' , T008_Valor_ICMS_Substituicao_Retencao - T008_Valor_FCP_ST, T008_Valor_ICMS_Substituicao_Retencao),
                    T008_Quantidade_Tributavel=if(xD006_Flag_Unidade_Tributavel='S' AND xD001_Quantidade_Tributavel>0,xD001_Quantidade_Tributavel*T008_Quantidade,if(xT008A_D184_Id > 0 AND xT008A_Quantidade_Comercial > 0, xT008A_Quantidade_Comercial,T008_Quantidade)),
                    T008_Valor_Preco_Sem_Desconto_Unitario_Tributavel=(T008_Valor_Total_Preco_Sem_Desconto/T008_Quantidade_Tributavel),
                    T008_Base_ICMS_Substituto=xT007_BC_ICMS_Compra,
                    T008_Valor_ICMS_Substituto=xT007_Valor_ICMS_Compra
                    -- Débito/crédito com cálculo automático não é uma cópia
                    -- dos impostos da origem, mesmo usando CFOP de devolução.
                    WHERE T008_Id=xT008_Id and T007_Flag_Calcular='S' and (T007_Flag_Finalidade_NFe IN ('5','6') or xD006_Flag_Devolucao = 'N' or xC031_Atualizar_Aliquotas_Devolucao = 'S') AND ifnull(T007_Numero_Protocolo_Nfe,'')='';

        -- Update na tabela T008A
        UPDATE T008A
     LEFT JOIN T008 ON T008A_T008_Id = T008_Id
     LEFT JOIN T007 ON T008_T007_Id = T007_Id
     LEFT JOIN D006 ON D006_Id = T008_D006_Id
           SET 
           /* Campos para NT 2019.001 - Criação e Atualização de Regras de Validação
            Versão 1.64 – Setembro de 2024, por solicitação da Smartness
            Gabriel Cegantini - 02/10/2025
            ================================================================================ */
           T008A_Valor_Beneficio_Credito_Presumido = (T008A_Percentual_Beneficio_Credito_Presumido/100) * T008_Valor_Base_ICMS_Impresso,
           T008A_Valor_Base_PIS_Retido = IF(D006_Flag_Envia_PIS_XML = 'S',
               T008_Valor_Total_Preco
               + IF(D006_Flag_Somar_IPI_Base_Retido = 'S', IFNULL(T008_Valor_IPI, 0), 0)
               + IF(D006_Flag_Somar_ST_Base_Retido = 'S', IFNULL(T008_Valor_ICMS_Substituicao_Retencao, 0), 0)
               + IF(D006_Flag_Somar_FCP_Base_Retido = 'S', IFNULL(T008_Valor_FCP, 0), 0), 0),
           T008A_Valor_Base_COFINS_Retido = IF(D006_Flag_Envia_COFINS_XML = 'S',
               T008_Valor_Total_Preco
               + IF(D006_Flag_Somar_IPI_Base_COFINS_Retido = 'S', IFNULL(T008_Valor_IPI, 0), 0)
               + IF(D006_Flag_Somar_ST_Base_COFINS_Retido = 'S', IFNULL(T008_Valor_ICMS_Substituicao_Retencao, 0), 0)
               + IF(D006_Flag_Somar_FCP_Base_COFINS_Retido = 'S', IFNULL(T008_Valor_FCP, 0), 0), 0),
           T008A_Valor_PIS_Retido = ROUND(IF(D006_Flag_Envia_PIS_XML = 'S',
               (T008_Valor_Total_Preco
               + IF(D006_Flag_Somar_IPI_Base_Retido = 'S', IFNULL(T008_Valor_IPI, 0), 0)
               + IF(D006_Flag_Somar_ST_Base_Retido = 'S', IFNULL(T008_Valor_ICMS_Substituicao_Retencao, 0), 0)
               + IF(D006_Flag_Somar_FCP_Base_Retido = 'S', IFNULL(T008_Valor_FCP, 0), 0))
               * (T008_Aliquota_PIS / 100), 0), 2),
           T008A_Valor_COFINS_Retido = ROUND(IF(D006_Flag_Envia_COFINS_XML = 'S',
               (T008_Valor_Total_Preco
               + IF(D006_Flag_Somar_IPI_Base_COFINS_Retido = 'S', IFNULL(T008_Valor_IPI, 0), 0)
               + IF(D006_Flag_Somar_ST_Base_COFINS_Retido = 'S', IFNULL(T008_Valor_ICMS_Substituicao_Retencao, 0), 0)
               + IF(D006_Flag_Somar_FCP_Base_COFINS_Retido = 'S', IFNULL(T008_Valor_FCP, 0), 0))
               * (T008_Aliquota_COFINS / 100), 0), 2)
           /* ================================================================================ */
         WHERE T008_Id=xT008_Id and T007_Flag_Calcular='S' and (T007_Flag_Finalidade_NFe IN ('5','6') or xD006_Flag_Devolucao = 'N' or xC031_Atualizar_Aliquotas_Devolucao = 'S') AND ifnull(T007_Numero_Protocolo_Nfe,'')='';

        -- As retenções de IRPJ e CSLL podem ter bases específicas, sem alterar os demais tributos.
        UPDATE T008
     LEFT JOIN T007 ON T007_Id = T008_T007_Id
     LEFT JOIN D006 ON D006_Id = T008_D006_Id
           SET T008_Valor_Base_IRPJ = T008_Valor_Total_Preco
                   + IF(D006_Flag_Somar_Despesas_Base_IRPJ = 'S', IFNULL(T008_Valor_Total_Despesas, 0), 0)
                   + IF(D006_Flag_Somar_FCP_Base_IRPJ = 'S', IFNULL(T008_Valor_FCP, 0), 0)
                   + IF(D006_Flag_Somar_IPI_Base_IRPJ = 'S', IFNULL(T008_Valor_IPI, 0), 0)
                   + IF(D006_Flag_Somar_ST_Base_IRPJ = 'S', IFNULL(T008_Valor_ICMS_Substituicao_Retencao, 0), 0),
               T008_Valor_IRPJ = IF(T008_Aliquota_IRPJ > 0, T008_Valor_Base_IRPJ * (T008_Aliquota_IRPJ / 100), 0),
               T008_Valor_Base_CSLL = T008_Valor_Total_Preco
                   + IF(D006_Flag_Somar_Despesas_Base_CSLL = 'S', IFNULL(T008_Valor_Total_Despesas, 0), 0)
                   + IF(D006_Flag_Somar_FCP_Base_CSLL = 'S', IFNULL(T008_Valor_FCP, 0), 0)
                   + IF(D006_Flag_Somar_IPI_Base_CSLL = 'S', IFNULL(T008_Valor_IPI, 0), 0)
                   + IF(D006_Flag_Somar_ST_Base_CSLL = 'S', IFNULL(T008_Valor_ICMS_Substituicao_Retencao, 0), 0),
               T008_Valor_CSLL = IF(T008_Aliquota_CSLL > 0, T008_Valor_Base_CSLL * (T008_Aliquota_CSLL / 100), 0)
         WHERE T008_Id=xT008_Id and T007_Flag_Calcular='S' and (T007_Flag_Finalidade_NFe IN ('5','6') or xD006_Flag_Devolucao = 'N' or xC031_Atualizar_Aliquotas_Devolucao = 'S') AND ifnull(T007_Numero_Protocolo_Nfe,'')='';

        -- [REFORMA TRIBUTÁRIA] Chama a PROCEDURE para calcular tudo da reforma tributária
        CALL T008_Gravar_Totalizacao_4_Reforma(xT008_Id);
    END IF;

    -- Mantém as bases de retenção disponíveis também para itens com cálculo manual.
    IF xT008_Flag_Calculo_Manual = 'S' THEN
        UPDATE T008A
     LEFT JOIN T008 ON T008A_T008_Id = T008_Id
     LEFT JOIN T007 ON T008_T007_Id = T007_Id
     LEFT JOIN D006 ON D006_Id = T008_D006_Id
           SET T008A_Valor_Base_PIS_Retido = IF(D006_Flag_Envia_PIS_XML = 'S', T008_Valor_Total_Preco + IF(D006_Flag_Somar_IPI_Base_Retido = 'S', IFNULL(T008_Valor_IPI, 0), 0) + IF(D006_Flag_Somar_ST_Base_Retido = 'S', IFNULL(T008_Valor_ICMS_Substituicao_Retencao, 0), 0) + IF(D006_Flag_Somar_FCP_Base_Retido = 'S', IFNULL(T008_Valor_FCP, 0), 0), 0),
               T008A_Valor_Base_COFINS_Retido = IF(D006_Flag_Envia_COFINS_XML = 'S', T008_Valor_Total_Preco + IF(D006_Flag_Somar_IPI_Base_COFINS_Retido = 'S', IFNULL(T008_Valor_IPI, 0), 0) + IF(D006_Flag_Somar_ST_Base_COFINS_Retido = 'S', IFNULL(T008_Valor_ICMS_Substituicao_Retencao, 0), 0) + IF(D006_Flag_Somar_FCP_Base_COFINS_Retido = 'S', IFNULL(T008_Valor_FCP, 0), 0), 0),
               T008A_Valor_PIS_Retido = ROUND(IF(D006_Flag_Envia_PIS_XML = 'S', (T008_Valor_Total_Preco + IF(D006_Flag_Somar_IPI_Base_Retido = 'S', IFNULL(T008_Valor_IPI, 0), 0) + IF(D006_Flag_Somar_ST_Base_Retido = 'S', IFNULL(T008_Valor_ICMS_Substituicao_Retencao, 0), 0) + IF(D006_Flag_Somar_FCP_Base_Retido = 'S', IFNULL(T008_Valor_FCP, 0), 0)) * (T008_Aliquota_PIS / 100), 0), 2),
               T008A_Valor_COFINS_Retido = ROUND(IF(D006_Flag_Envia_COFINS_XML = 'S', (T008_Valor_Total_Preco + IF(D006_Flag_Somar_IPI_Base_COFINS_Retido = 'S', IFNULL(T008_Valor_IPI, 0), 0) + IF(D006_Flag_Somar_ST_Base_COFINS_Retido = 'S', IFNULL(T008_Valor_ICMS_Substituicao_Retencao, 0), 0) + IF(D006_Flag_Somar_FCP_Base_COFINS_Retido = 'S', IFNULL(T008_Valor_FCP, 0), 0)) * (T008_Aliquota_COFINS / 100), 0), 2)
         WHERE T008_Id=xT008_Id AND T007_Flag_Calcular='S' AND ifnull(T007_Numero_Protocolo_Nfe,'')='';

        UPDATE T008
     LEFT JOIN T007 ON T007_Id = T008_T007_Id
     LEFT JOIN D006 ON D006_Id = T008_D006_Id
           SET T008_Valor_Base_IRPJ = T008_Valor_Total_Preco + IF(D006_Flag_Somar_Despesas_Base_IRPJ = 'S', IFNULL(T008_Valor_Total_Despesas, 0), 0) + IF(D006_Flag_Somar_FCP_Base_IRPJ = 'S', IFNULL(T008_Valor_FCP, 0), 0) + IF(D006_Flag_Somar_IPI_Base_IRPJ = 'S', IFNULL(T008_Valor_IPI, 0), 0) + IF(D006_Flag_Somar_ST_Base_IRPJ = 'S', IFNULL(T008_Valor_ICMS_Substituicao_Retencao, 0), 0),
               T008_Valor_IRPJ = IF(T008_Aliquota_IRPJ > 0, T008_Valor_Base_IRPJ * (T008_Aliquota_IRPJ / 100), 0),
               T008_Valor_Base_CSLL = T008_Valor_Total_Preco + IF(D006_Flag_Somar_Despesas_Base_CSLL = 'S', IFNULL(T008_Valor_Total_Despesas, 0), 0) + IF(D006_Flag_Somar_FCP_Base_CSLL = 'S', IFNULL(T008_Valor_FCP, 0), 0) + IF(D006_Flag_Somar_IPI_Base_CSLL = 'S', IFNULL(T008_Valor_IPI, 0), 0) + IF(D006_Flag_Somar_ST_Base_CSLL = 'S', IFNULL(T008_Valor_ICMS_Substituicao_Retencao, 0), 0),
               T008_Valor_CSLL = IF(T008_Aliquota_CSLL > 0, T008_Valor_Base_CSLL * (T008_Aliquota_CSLL / 100), 0)
         WHERE T008_Id=xT008_Id AND T007_Flag_Calcular='S' AND ifnull(T007_Numero_Protocolo_Nfe,'')='';
    END IF;
END;
