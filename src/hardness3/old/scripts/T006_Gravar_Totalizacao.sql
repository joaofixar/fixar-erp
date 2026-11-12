CREATE PROCEDURE `T006_Gravar_Totalizacao_3`(xT006_Id INT(11))
BEGIN
    DECLARE xT005_Id INT;
    DECLARE xPercentual_Desconto DECIMAL(20,10) DEFAULT 0;
    DECLARE xMultiplicador_Despesa DECIMAL(20,10) DEFAULT 0;
    DECLARE xMultiplicador_Despesas_Acessorias DECIMAL(20,10) DEFAULT 0;
    DECLARE xMultiplicador_Frete DECIMAL(20,10) DEFAULT 0;
    DECLARE xFlag_Revenda_Consumidor VARCHAR(1);
    DECLARE xFlag_Devolucao VARCHAR(1);
    DECLARE xFlag_Tipo_Devolucao VARCHAR(10);
    DECLARE xValor_Total_Frete DECIMAL(19,2) DEFAULT 0;
    DECLARE xValor_Total_Despesas_Acessorias DECIMAL(10,2) DEFAULT 0;
    DECLARE xValor_Total_Desconto DECIMAL(10,2) DEFAULT 0;
    DECLARE xFlag_ST VARCHAR(3);
    DECLARE xFlag_Venda_Compra_Outros VARCHAR(1);
    DECLARE xAliquota_ICMS_1 DECIMAL(5,2);

    DECLARE xD006_Flag_Somar_IPI_Base_ICMS ENUM('S','N') DEFAULT 'N';
    DECLARE xD006_Flag_Somar_Despesas_Base_IPI ENUM('S','N') DEFAULT 'N';
    DECLARE xD006_Flag_Isento_PIS ENUM('S','N') DEFAULT 'N';
    DECLARE xD006_Situacao_Tributaria_PIS VARCHAR(2) DEFAULT '01';
    DECLARE xD006_Flag_Isento_COFINS ENUM('S','N') DEFAULT 'N';
    DECLARE xD006_Situacao_Tributaria_COFINS VARCHAR(2) DEFAULT '01';
    DECLARE xD059_Codigo_ST VARCHAR(3);
    DECLARE xC004_Percentual_PIS DECIMAL(6,2) DEFAULT 0;
    DECLARE xC004_Percentual_COFINS DECIMAL(6,2) DEFAULT 0;
    DECLARE xD049_Flag_Nacional_Importado VARCHAR(1);
    DECLARE xD001_Peso_Unitario_Kg DECIMAL(10,3) DEFAULT 0;

    DECLARE xD006_Flag_Forma_Calculo_COFINS VARCHAR(1) DEFAULT 'V';
    DECLARE xD006_Flag_Forma_Calculo_PIS VARCHAR(1) DEFAULT 'V';

    DECLARE xC004_Casas_Decimais_Preco INT(2);
    DECLARE xC004_Id INT(10);
    
    DECLARE xT185_Qtd_Separado DECIMAL(20,2) DEFAULT 0;
    
    -- caso esteja utilizando a tabela T185, a quantidade de itens separados tem que ser pega desta tabela
    SELECT COUNT(T185_Qtd_Separado) INTO xT185_Qtd_Separado FROM T185 WHERE T185_T006_Id = xT006_Id;
    IF xT185_Qtd_Separado > 0 THEN
	SELECT SUM(T185_Qtd_Separado) INTO xT185_Qtd_Separado FROM T185 WHERE T185_T006_Id = xT006_Id;
	UPDATE T006 SET T006_Quantidade_Separacao = xT185_Qtd_Separado WHERE T006_Id = xT006_Id;
    END IF;

    -- GRAVA A CFOP POR ITEM PARA OS QUE AINDA NAO TEM CFOP
    UPDATE T006 LEFT JOIN T005 ON T005_Id=T006_T005_Id SET T006_D006_Id=T005_D006_Id WHERE T006_Id=xT006_Id AND T006_D006_Id IS NULL;

    SELECT T006_T005_Id,
           D006_Flag_Venda_Compra_Outros,
           D049_Flag_Nacional_Importado,
           D006_Flag_Somar_IPI_Base_ICMS,
           D006_Flag_Somar_Despesas_Base_IPI,
           D001_Peso_Unitario_Kg,
           D006_Flag_Forma_Calculo_COFINS,
           D006_Flag_Forma_Calculo_PIS,
           T006_C004_Id
      INTO xT005_Id,
           xFlag_Venda_Compra_Outros,
           xD049_Flag_Nacional_Importado,
           xD006_Flag_Somar_IPI_Base_ICMS,
           xD006_Flag_Somar_Despesas_Base_IPI,
           xD001_Peso_Unitario_Kg,
           xD006_Flag_Forma_Calculo_COFINS,
           xD006_Flag_Forma_Calculo_PIS,
           xC004_Id
      FROM T006
      LEFT JOIN T005 ON T005_Id=T006_T005_Id
      LEFT JOIN D006 ON D006_Id=T006_D006_Id
      LEFT JOIN D024 ON D024_Id=T005_D024_Id
      LEFT JOIN D020 ON D020_Id=D024_D020_Id
      LEFT JOIN D009 ON D009_Id=T006_D009_Id
      LEFT JOIN D049 ON D049_Id=D009_D049_Id
      LEFT JOIN D001 ON D001_Id=D049_D001_Id
     WHERE T006_Id=xT006_Id
     LIMIT 1;

      SELECT C004_Casas_Decimais_Preco
        INTO xC004_Casas_Decimais_Preco
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
     
    IF xPercentual_Desconto IS NULL THEN
       SET xPercentual_Desconto = 0;
    END IF;
    
    UPDATE T006 SET T006_Valor_Desconto_Unitario = IF(xPercentual_Desconto>0,T006_Valor_Preco_Sem_Desconto_Unitario*xPercentual_Desconto,0),
                    T006_Valor_Total_Desconto = T006_Valor_Desconto_Unitario*T006_Quantidade,
                    T006_Valor_Preco_Unitario = ROUND(T006_Valor_Preco_Sem_Desconto_Unitario - T006_Valor_Desconto_Unitario,xC004_Casas_Decimais_Preco),
                    T006_Valor_Total_Preco = T006_Valor_Preco_Unitario*T006_Quantidade,
                    T006_Valor_Total_Preco_Sem_Desconto = T006_Valor_Preco_Sem_Desconto_Unitario * T006_Quantidade
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
           SELECT T005_Valor_Frete/SUM(T006_Valor_Preco_Unitario*T006_Quantidade),
                  T005_Valor_Despesas_Acessorias/SUM(T006_Valor_Preco_Unitario*T006_Quantidade)
           INTO xMultiplicador_Frete,
                xMultiplicador_Despesas_Acessorias
         FROM T005
         LEFT JOIN T006 ON T006_T005_Id=T005_Id
         WHERE T005_Id=xT005_Id
         GROUP BY T005_Id;
        UPDATE T006 SET T006_Valor_Total_Despesas_Acessorias=T006_Valor_Total_Preco*xMultiplicador_Despesas_Acessorias,
                        T006_Valor_Despesas_Acessorias_Unitario=T006_Valor_Total_Despesas_Acessorias/T006_Quantidade,
                        T006_Valor_Total_Frete=T006_Valor_Total_Preco*xMultiplicador_Frete,
                        T006_Valor_Frete_Unitario=T006_Valor_Total_Frete/T006_Quantidade
                  WHERE T006_Id=xT006_Id;
    END IF;
    SELECT  (T005_Valor_Frete+T005_Valor_Despesas_Acessorias)/SUM(T006_Valor_Preco_Unitario*T006_Quantidade),
            T005_Flag_Revenda_Consumidor
      INTO
        xMultiplicador_Despesa,
        xFlag_Revenda_Consumidor
      FROM T005
      LEFT JOIN T006 ON T006_T005_Id=T005_Id
     WHERE T005_Id=xT005_Id
     GROUP BY T005_Id;
    IF xMultiplicador_Despesa IS NULL THEN
       SET xMultiplicador_Despesa = 0;
    END IF;
    UPDATE T006 SET T006_Valor_Total_Custo = T006_Valor_Custo_Unitario*T006_Quantidade,
                    T006_Peso_Unitario = xD001_Peso_Unitario_Kg,
                    T006_Peso_Total = T006_Peso_Unitario*T006_Quantidade,
                    T006_Valor_Total_Despesas=T006_Valor_Total_Preco*xMultiplicador_Despesa,
                    T006_Valor_Despesas_Unitario=T006_Valor_Total_Despesas/T006_Quantidade,
                    T006_Valor_Base_IPI=IF(T006_Aliquota_IPI>0,(T006_Valor_Total_Preco+IF(xD006_Flag_Somar_Despesas_Base_IPI='S',T006_Valor_Total_Frete+T006_Valor_Total_Despesas_Acessorias,0))*IF(T006_Percentual_Reducao_IPI>0,(T006_Percentual_Reducao_IPI/100),1),0),
                    T006_Valor_IPI=T006_Valor_Base_IPI*(T006_Aliquota_IPI/100),
                    T006_Valor_Total_Custo_Final=(T006_Valor_Total_Preco+T006_Valor_Total_Despesas+IF(xD006_Flag_Somar_IPI_Base_ICMS='S',T006_Valor_IPI,0)),
                    T006_Valor_Custo_Final_Unitario=T006_Valor_Total_Custo_Final/T006_Quantidade,
                    T006_Valor_Base_ICMS=IF(T006_Aliquota_ICMS>0,T006_Valor_Total_Custo_Final*IF(T006_Percentual_Reducao_ICMS>0,(T006_Percentual_Reducao_ICMS/100),1),0),
                    T006_Valor_ICMS=T006_Valor_Base_ICMS*(T006_Aliquota_ICMS/100),
                    T006_IPV=T006_Valor_Preco_Unitario/T006_Valor_Custo_Unitario,
                    T006_Valor_Base_ICMS_Substituicao=IF(T006_Flag_ST='S',(T006_Valor_Total_Preco+T006_Valor_Total_Despesas+T006_Valor_IPI)*((T006_ST_VA/100)+1),0),
                    T006_Valor_ICMS_Substituicao=(T006_Valor_Base_ICMS_Substituicao*(T006_ST_ICMS_Interno/100)),
                    T006_Valor_ICMS_Substituicao_Retencao=(T006_Valor_Base_ICMS_Substituicao*(T006_ST_ICMS_Interno/100))-((T006_Valor_Total_Custo_Final*IF(T006_Percentual_Reducao_ICMS>0,(T006_Percentual_Reducao_ICMS/100),1))*(T006_ICMS_Inter_Estadual/100)),
                    T006_Valor_ICMS_Substituicao_Retencao=IF(T006_Valor_ICMS_Substituicao_Retencao<0,0,T006_Valor_ICMS_Substituicao_Retencao),
                    T006_Valor_Base_PIS=IF(T006_Flag_Isento_PIS='S',0,IF(xD006_Flag_Forma_Calculo_PIS='L',T006_Valor_Total_Preco-T006_Valor_Total_Custo,T006_Valor_Total_Preco)),
                    T006_Aliquota_PIS=IF(T006_Flag_Isento_PIS='S',0,T006_Aliquota_PIS),
                    T006_Valor_PIS=IF(T006_Aliquota_PIS>0,T006_Valor_Base_PIS*(T006_Aliquota_PIS/100),0),
                    T006_Valor_Base_COFINS=IF(T006_Flag_Isento_COFINS='S',0,IF(xD006_Flag_Forma_Calculo_COFINS='L',T006_Valor_Total_Preco-T006_Valor_Total_Custo,T006_Valor_Total_Preco)),
                    T006_Aliquota_COFINS=IF(T006_Flag_Isento_COFINS='S',0,T006_Aliquota_COFINS),
                    T006_Valor_COFINS=IF(T006_Aliquota_COFINS>0,T006_Valor_Base_COFINS*(T006_Aliquota_COFINS/100),0)
              WHERE T006_Id=xT006_Id;
                            
    CALL T005_Gravar_Totalizacao_3(xT005_Id);
END;