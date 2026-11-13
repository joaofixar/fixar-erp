CREATE PROCEDURE `T219_Gravar_Totalizacao`(xT219_Id int(11))                                            
BEGIN
    DECLARE xT218_Id INT;
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
    DECLARE xD006_Flag_Ajustar_MVA varchar(1);
    DECLARE xT218_Flag_Calcular_ST varchar(1);
    DECLARE xD006_Flag_Somar_IPI_Base_ICMS varchar(1);
    DECLARE xValor_Total_Frete DECIMAL(19,2) DEFAULT 0;
    DECLARE xMultiplicador_Frete DECIMAL(20,10) DEFAULT 0;
    DECLARE xTotal_Itens INT(5);
    DECLARE xC004_Flag_Rateio_Frete VARCHAR(1) DEFAULT '1';
    DECLARE xD006_Flag_Somar_DIFAL_Custo VARCHAR(1) DEFAULT 'N';
    DECLARE xD006_Flag_Somar_Despesas_Base_IPI VARCHAR(1) DEFAULT 'N';
    DECLARE xT219_Flag_Origem_Produto varchar(1) default '';
    DECLARE xT219_Flag_Destino_Produto varchar(1) default '';
    DECLARE xC004_D018_Id int(11) default 0;
    DECLARE xD024_D018_Id int(11) default 0;
    DECLARE xT219A_Percentual_Reducao_ICMS_IE_ST decimal(10,2) default 0;
    DECLARE xT219A_Percentual_Reducao_ICMS_INT_ST decimal(10,2) default 0;
    DECLARE xD053_Calculo_ST_Por_Dentro_Compra varchar(1);
    DECLARE xD006_Flag_Calcula_FCP_ST varchar(1);
    DECLARE xD006_Flag_Calcula_FCP varchar(1);
    DECLARE xD006_Flag_Deduz_ICMS_Base_PIS varchar(1);
		DECLARE xD006_Flag_Deduz_ICMS_Base_COFINS varchar(1);

      SELECT T219_T218_Id,
           D006_Flag_Forma_Calculo_COFINS,
           D006_Flag_Forma_Calculo_PIS,
           D006_Flag_Somar_IPI_Base_ICMS,
           D006_Flag_Somar_Despesas_Base_ST,
           T219_Percentual_Desconto/100,
           T219_Percentual_Desconto/100,
           T219_Percentual_Desconto_Bruto/100,
           T219_C004_Id,
           D006_Flag_Ajustar_MVA,
           T218_Flag_Calcular_ST,
           D006_Flag_Somar_DIFAL_Custo,
           D006_Flag_Somar_Despesas_Base_IPI,
           T219_Flag_Origem_Produto,
           T219_Flag_Destino_Produto,
           C004_D018_Id,
           D024_D018_Id,
           T219A_Percentual_Reducao_ICMS_IE_ST,
           T219A_Percentual_Reducao_ICMS_INT_ST,
           D053_Calculo_ST_Por_Dentro_Compra,
           D006_Flag_Calcula_FCP_ST,
           D006_Flag_Calcula_FCP,
			     D006_Flag_Deduz_ICMS_Base_PIS,
			     D006_Flag_Deduz_ICMS_Base_COFINS
      INTO xT218_Id,
           xD006_Flag_Forma_Calculo_COFINS,
           xD006_Flag_Forma_Calculo_PIS,
           xD006_Flag_Somar_IPI_Base_ICMS,
           xD006_Flag_Somar_Despesas_Base_ST,
           xPercentual_Desconto,
           xPercentual_Desconto_Confirmado,
           xPercentual_Desconto_Bruto,
           xC004_Id,
           xD006_Flag_Ajustar_MVA,
           xT218_Flag_Calcular_ST,
           xD006_Flag_Somar_DIFAL_Custo,
           xD006_Flag_Somar_Despesas_Base_IPI,
           xT219_Flag_Origem_Produto,
           xT219_Flag_Destino_Produto,
           xC004_D018_Id,
           xD024_D018_Id,
           xT219A_Percentual_Reducao_ICMS_IE_ST,
           xT219A_Percentual_Reducao_ICMS_INT_ST,
           xD053_Calculo_ST_Por_Dentro_Compra,
           xD006_Flag_Calcula_FCP_ST,
           xD006_Flag_Calcula_FCP,
			     xD006_Flag_Deduz_ICMS_Base_PIS,
			     xD006_Flag_Deduz_ICMS_Base_COFINS
      FROM T219
      LEFT JOIN D006 ON D006_Id=T219_D006_Id
      LEFT JOIN T218 ON T218_Id=T219_T218_Id
      LEFT JOIN D024 ON D024_Id=T218_D024_Id
      LEFT JOIN C004 ON C004_Id=T218_C004_Id
      LEFT JOIN T219A ON T219A_T219_Id=T219_Id
      LEFT JOIN D053 ON D053_D018_Id=D024_D018_Id AND D053_C004_Id=T218_C004_Id
      WHERE T219_Id=xT219_Id;

      SELECT C004_Casas_Decimais_Preco,
      		 C004_Flag_Rateio_Frete
        INTO xC004_Casas_Decimais_Preco,
        	 xC004_Flag_Rateio_Frete
        FROM C004
       WHERE C004_Id=xC004_Id;

      if !(xPercentual_Desconto>0 OR xPercentual_Desconto<0) THEN
         SELECT T218_Percentual_Desconto/100,
                T218_Percentual_Desconto_Confirmado/100
           INTO xPercentual_Desconto,
                xPercentual_Desconto_Confirmado
         FROM T218
         LEFT JOIN C004 ON C004_Id=T218_C004_Id
         WHERE T218_Id=xT218_Id;
      END IF;

      SELECT SUM(T202_Valor_Unitario) INTO xValor_Total_Agregados FROM T202 WHERE T202_T219_Id=xT219_Id;

      IF xPercentual_Desconto IS NULL THEN
         set xPercentual_Desconto = 0;
      END IF;
      IF xPercentual_Desconto_Bruto IS NULL THEN
         set xPercentual_Desconto_Bruto = 0;
      END IF;
       IF xPercentual_Desconto_Confirmado IS NULL THEN
         set xPercentual_Desconto_Confirmado = 0;
      END IF;

      IF xValor_Total_Agregados IS NULL THEN
        set xValor_Total_Agregados =0;
      END IF;

      UPDATE T219
                SET T219_Valor_Desconto_Unitario = if(xPercentual_Desconto>0 or xPercentual_Desconto<0,T219_Valor_Preco_Original*xPercentual_Desconto,0),
                    T219_Valor_Desconto_Unitario_Bruto = if(xPercentual_Desconto_Bruto>0 or xPercentual_Desconto_Bruto<0,T219_Valor_Preco_Original_Bruto*xPercentual_Desconto_Bruto,0),
                    T219_Valor_Desconto_Unitario_Confirmado = if(xPercentual_Desconto_Confirmado>0 or xPercentual_Desconto_Confirmado<0,T219_Valor_Preco_Original*xPercentual_Desconto_Confirmado,0),
                    T219_Valor_Total_Desconto = T219_Valor_Desconto_Unitario*T219_Quantidade,
                    T219_Valor_Total_Desconto_Confirmado = T219_Valor_Desconto_Unitario_Confirmado*T219_Quantidade_Confirmada,
                    T219_Valor_Preco_Bruto = round(T219_Valor_Preco_Original_Bruto - T219_Valor_Desconto_Unitario_Bruto,xC004_Casas_Decimais_Preco),
                    T219_Valor_Preco_Unitario_Confirmado = round(T219_Valor_Preco_Original - T219_Valor_Desconto_Unitario_Confirmado,xC004_Casas_Decimais_Preco),
                    T219_Valor_Total_Preco = T219_Valor_Preco_Unitario*T219_Quantidade,
                    T219_Valor_Total_Preco_Confirmado = T219_Valor_Preco_Unitario_Confirmado * T219_Quantidade_Confirmada,
                    T219_Valor_Total_Preco_Sem_Desconto = T219_Valor_Preco_Original * T219_Quantidade,
                    T219_Valor_Total_Preco_Sem_Desconto_Confirmado = T219_Valor_Preco_Original * T219_Quantidade_Confirmada,
                    T219_Valor_Total_Agregados=xValor_Total_Agregados,
                    T219_Valor_Trib_Transparencia=IFNULL(T219_Valor_Total_Preco,0)*(IFNULL(T219_Percentual_Trib_Transparencia,0)/100)
                 WHERE T219_Id=xT219_Id;
                 SELECT (if (T218_Flag_Frete=1,T218_Valor_Frete,0)+T218_Valor_Despesas_Acessorias)/sum(T219_Valor_Preco_Unitario*T219_Quantidade),
                           (if (T218_Flag_Frete=1,T218_Valor_Frete,0)+T218_Valor_Despesas_Acessorias)/sum(T219_Valor_Preco_Unitario*T219_Quantidade_Confirmada),
                           T218_Flag_Revenda_Consumidor
                  INTO xMultiplicador_Despesa,
                        xMultiplicador_Despesa_Confirmado,
                        xFlag_Revenda_Consumidor
                  FROM T218
                  LEFT JOIN T219 ON T219_T218_Id=T218_Id
                 WHERE T218_Id=xT218_Id
                 GROUP BY T218_Id;
       IF xMultiplicador_Despesa IS NULL THEN
           set xMultiplicador_Despesa = 0;
       END IF;
       IF xMultiplicador_Despesa_Confirmado IS NULL THEN
           set xMultiplicador_Despesa_Confirmado = 0;
       END IF;
       
       	SELECT COUNT(*) INTO xTotal_Itens FROM T219 WHERE T219_T218_Id = xT218_Id;

		SELECT T218_Valor_Frete/SUM(T219_Valor_Preco_Unitario*T219_Quantidade),
				T218_Valor_Frete,
				T218_Valor_Despesas_Acessorias/SUM(T219_Valor_Preco_Unitario*T219_Quantidade)
		INTO xMultiplicador_Frete,
			xValor_Total_Frete,
			xMultiplicador_Despesas_Acessorias
		FROM T218
		LEFT JOIN T219 ON T219_T218_Id=T218_Id
		WHERE T218_Id=xT218_Id
		GROUP BY T218_Id;

		UPDATE T219 SET T219_Valor_Total_Despesas_Acessorias=T219_Valor_Total_Preco*xMultiplicador_Despesas_Acessorias,
				T219_Valor_Despesas_Acessorias_Unitario=T219_Valor_Total_Despesas_Acessorias/T219_Quantidade,
				T219_Valor_Frete_Total=IF(xC004_Flag_Rateio_Frete=1,T219_Valor_Total_Preco*xMultiplicador_Frete,xValor_Total_Frete/xTotal_Itens),
				T219_Valor_Frete_Unitario=T219_Valor_Frete_Total/T219_Quantidade
		WHERE T219_Id=xT219_Id;

        IF xT218_Flag_Calcular_ST = 'S' THEN
    		UPDATE T219
            LEFT JOIN D009 ON D009_Id=T219_D009_Id
            LEFT JOIN D006 ON D006_Id=T219_D006_Id
    		            SET T219_Peso_Total=T219_Peso_Unitario*T219_Quantidade,
                        T219_Peso_Bruto_Total=T219_Peso_Bruto*T219_Quantidade,
                        T219_Peso_Total_Confirmado=T219_Peso_Unitario*T219_Quantidade_Confirmada,
                        T219_Valor_Total_Despesas=(T219_Valor_Total_Preco*xMultiplicador_Despesa)+xValor_Total_Agregados,
                        T219_Valor_Total_Despesas_Confirmado=T219_Valor_Total_Preco_Confirmado*xMultiplicador_Despesa_Confirmado,
                        T219_Valor_Despesas_Unitario=T219_Valor_Total_Despesas/T219_Quantidade,
                        T219_Valor_Despesas_Unitario_Confirmado=T219_Valor_Total_Despesas_Confirmado/T219_Quantidade_Confirmada,
                        T219_Valor_Base_IPI=IF(T219_Aliquota_IPI>0,(T219_Valor_Total_Preco+IF(xD006_Flag_Somar_Despesas_Base_IPI='S',T219_Valor_Frete_Total+T219_Valor_Total_Despesas_Acessorias,0))*IF(T219_Percentual_Reducao_IPI>0,(T219_Percentual_Reducao_IPI/100),1),0),
                        T219_Valor_Base_IPI_Confirmado=IF(T219_Aliquota_IPI>0,(T219_Valor_Total_Preco_Confirmado+IF(xD006_Flag_Somar_Despesas_Base_IPI='S',T219_Valor_Frete_Total+T219_Valor_Total_Despesas_Acessorias,0))*IF(T219_Percentual_Reducao_IPI,(T219_Percentual_Reducao_IPI/100),1),0),
                        T219_Valor_IPI=T219_Valor_Base_IPI*(T219_Aliquota_IPI/100),
                        T219_Valor_IPI_Confirmado=T219_Valor_Base_IPI_Confirmado*(T219_Aliquota_IPI/100),
                        T219_Valor_Total_Custo_Final=(T219_Valor_Total_Preco+if(xD006_Flag_Somar_Despesas_Base_ST='S',ifnull(T219_Valor_Total_Despesas,0),0)+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T219_Valor_IPI,0)),
                        T219_Valor_Total_Custo_Final_Confirmado=(T219_Valor_Total_Preco_Confirmado+if(xD006_Flag_Somar_Despesas_Base_ST='S',T219_Valor_Total_Despesas_Confirmado,0)+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T219_Valor_IPI,0)),
                        T219_Valor_Custo_Final_Unitario=T219_Valor_Total_Custo_Final/T219_Quantidade,
                        T219_Valor_Custo_Final_Unitario_Confirmado=T219_Valor_Total_Custo_Final_Confirmado/T219_Quantidade_Confirmada,
                        T219_Valor_Base_ICMS=IF(T219_Aliquota_ICMS>0,T219_Valor_Total_Custo_Final*IF(T219_Percentual_Reducao_ICMS>0,((100-T219_Percentual_Reducao_ICMS)/100),1),0),
                        T219_Valor_Base_ICMS_Confirmado=IF(T219_Aliquota_ICMS>0,T219_Valor_Total_Custo_Final_Confirmado*IF(T219_Percentual_Reducao_ICMS>0,((100-T219_Percentual_Reducao_ICMS)/100),1),0),
                        T219_Valor_ICMS=T219_Valor_Base_ICMS*(T219_Aliquota_ICMS/100),
                        T219_Valor_ICMS_Confirmado=T219_Valor_Base_ICMS_Confirmado*(T219_Aliquota_ICMS/100), 
                        T219_Aliquota_PIS=if(T219_Flag_Isento_PIS='S',0,T219_Aliquota_PIS),
                        T219_Aliquota_COFINS=if(T219_Flag_Isento_COFINS='S',0,T219_Aliquota_COFINS),
                        T219_Valor_Base_PIS=if(T219_Flag_Isento_PIS='S',0,if(xD006_Flag_Forma_Calculo_PIS='L',if(T219_Quantidade_Confirmada>0,T219_Valor_Total_Preco_Confirmado-T219_Valor_Total_Custo_Confirmado,T219_Valor_Total_Preco-T219_Valor_Total_Custo),if(T219_Quantidade_Confirmada>0,T219_Valor_Total_Preco_Confirmado,T219_Valor_Total_Preco))),
				                T219_Valor_Base_PIS=if(T219_Valor_Base_PIS>0,if(xD006_Flag_Deduz_ICMS_Base_PIS='S',T219_Valor_Base_PIS-T219_Valor_ICMS,T219_Valor_Base_PIS),0),
                        T219_Valor_PIS=if(T219_Aliquota_PIS>0,T219_Valor_Base_PIS*(T219_Aliquota_PIS/100),0),
                        T219_Valor_Base_COFINS=if(T219_Flag_Isento_COFINS='S',0,if(xD006_Flag_Forma_Calculo_COFINS='L',if(T219_Quantidade_Confirmada>0,T219_Valor_Total_Preco_Confirmado-T219_Valor_Total_Custo_Confirmado,T219_Valor_Total_Preco-T219_Valor_Total_Custo),if(T219_Quantidade_Confirmada>0,T219_Valor_Total_Preco_Confirmado,T219_Valor_Total_Preco))),
				                T219_Valor_Base_COFINS=if(T219_Valor_Base_COFINS>0,if(xD006_Flag_Deduz_ICMS_Base_COFINS='S',T219_Valor_Base_COFINS-T219_Valor_ICMS,T219_Valor_Base_COFINS),0),
                        T219_Valor_COFINS=if(T219_Aliquota_COFINS>0,T219_Valor_Base_COFINS*(T219_Aliquota_COFINS/100),0),
                        #T219_ST_VA_Ajustada = IF(xD006_Flag_Ajustar_MVA = 'S', ( ( (1+(T219_ST_VA/100)) *   ( ( 1 - ( T219_ICMS_Inter_Estadual / 100 ) ) / ( 1 - if(( T219_ST_ICMS_Interno / 100 ) ) ) ) - 1 ) * 100, T219_ST_VA),
                        T219_ST_VA_Ajustada = TRIB_ST_VA_Ajustada(xD006_Flag_Ajustar_MVA, T219_ST_VA, T219_ICMS_Inter_Estadual, T219_ST_ICMS_Interno, T219_Percentual_Reducao_ICMS_ST, 'C', T219_Percentual_Reducao_ICMS, xT219A_Percentual_Reducao_ICMS_IE_ST, xT219A_Percentual_Reducao_ICMS_INT_ST, xT219_Flag_Origem_Produto, xT219_Flag_Destino_Produto, xC004_D018_Id, xD024_D018_Id),                       
                        #T219_Valor_Base_ST=IF(T219_Flag_ST='S',(T219_Valor_Total_Preco+if(xD006_Flag_Somar_Despesas_Base_ST='S',ifnull(T219_Valor_Total_Despesas,0),0)+T219_Valor_IPI)*((T219_ST_VA_Ajustada/100)+1),0),
                        T219_Valor_Base_ST=IF(T219_Flag_ST='S',TRIB_Base_ST(T219_Flag_ST, T219_Valor_Total_Preco, if(xD006_Flag_Somar_Despesas_Base_ST='S',T219_Valor_Total_Despesas,0),T219_Valor_IPI, T219_ST_VA_Ajustada,xD024_D018_Id,xC004_D018_Id, ifnull(xT219_Flag_Destino_Produto,0), xD053_Calculo_ST_Por_Dentro_Compra, T219_ST_ICMS_Interno, T219_Valor_ICMS, T219_ICMS_Inter_Estadual,0 ,T219_Modalidade_ICMS_Substituicao),0),
                        #T219_Valor_ST=IF(T219_Percentual_ST>0,(T219_Valor_Total_Preco+T219_Valor_IPI)*(T219_Percentual_ST/100),IF(T219_Flag_ST='S',((T219_Valor_Base_ST*IF(T219_Percentual_Reducao_ICMS_ST>0,((100-T219_Percentual_Reducao_ICMS_ST)/100),1))*(T219_ST_ICMS_Interno/100))-((T219_Valor_Total_Custo_Final*IF(T219_Percentual_Reducao_ICMS>0,((100-T219_Percentual_Reducao_ICMS)/100),1))*(T219_ICMS_Inter_Estadual/100)),0)),
                        T219_Valor_ST=IF(T219_Percentual_ST>0,(T219_Valor_Total_Preco+T219_Valor_IPI)*(T219_Percentual_ST/100),IF(T219_Valor_ST_Digitado > 0, T219_Valor_ST_Digitado, IF(T219_Flag_ST='S',TRIB_Valor_ST(T219_Valor_Base_ST, T219_Percentual_Reducao_ICMS_ST, T219_ST_ICMS_Interno, T219_Valor_Total_Custo_Final, T219_Percentual_Reducao_ICMS, T219_ICMS_Inter_Estadual,T219_ST_VA_Ajustada, xD024_D018_Id, xC004_D018_Id, ifnull(xT219_Flag_Destino_Produto,0), 0, T219_Modalidade_ICMS_Substituicao),0))),
                        T219_Valor_ST=IF(T219_Valor_ST<0,0,T219_Valor_ST),
                        T219_Valor_Base_ST_Confirmado=IF(T219_Flag_ST='S',(T219_Valor_Total_Custo_Final_Confirmado+T219_Valor_Despesas_Unitario_Confirmado+T219_Valor_IPI_Confirmado)*((T219_ST_VA_Ajustada/100)+1),0),
                        T219_Valor_ST_Confirmado=IF(T219_Flag_ST='S',(T219_Valor_Base_ST_Confirmado*(T219_ST_ICMS_Interno/100))-((T219_Valor_Total_Custo_Final_Confirmado*IF(T219_Percentual_Reducao_ICMS>0,(T219_Percentual_Reducao_ICMS/100),1))*(T219_ICMS_Inter_Estadual/100)),0),
                        T219_Valor_ST_Confirmado=IF(T219_Valor_ST_Confirmado<0,0,T219_Valor_ST_Confirmado),
                        T219_IPV=D009_IPV_1,
                        T219_Total_Cubagem = IFNULL(T219_Cubagem_Unitaria, 0) * IFNULL(T219_Quantidade,0),
                        T219_Valor_Trib_Transparencia=IFNULL(T219_Valor_Total_Preco,0)*(IFNULL(T219_Percentual_Trib_Transparencia,0)/100),
                        T219_Valor_Frete=(T219_Valor_Total_Preco+T219_Valor_IPI+T219_Valor_ST+T219_Valor_Financeiro) * (T219_Percentual_Frete/100),
                        T219_Valor_Financeiro=T219_Valor_Total_Preco * (T219_Percentual_Financeiro/100),
                        T219_Valor_Total_Custo=T219_Valor_Total_Preco+T219_Valor_IPI+T219_Valor_ST+T219_Valor_Frete+T219_Valor_Financeiro+T219_Valor_Frete_Total+T219_Valor_ICMS_Diferenca_Aliquotas,
                        T219_Valor_Custo_Unitario=T219_Valor_Total_Custo/T219_Quantidade,
                        T219_Valor_Total_Custo_Confirmado=T219_Valor_Custo_Unitario*T219_Quantidade_Confirmada,
                        T219_Aliquota_ICMS_Diferenca_Aliquotas = IF(xD006_Flag_Somar_DIFAL_Custo='S', (T219_ST_ICMS_Interno - (T219_ST_ICMS_Interno * T219_Percentual_Reducao_ICMS/100)) - T219_Aliquota_ICMS, 0),                         
                        T219_Aliquota_ICMS_Diferenca_Aliquotas = IF(T219_Aliquota_ICMS_Diferenca_Aliquotas > 0 , T219_Aliquota_ICMS_Diferenca_Aliquotas, 0),
                        T219_Valor_ICMS_Diferenca_Aliquotas = (T219_Aliquota_ICMS_Diferenca_Aliquotas/100) * T219_Valor_Base_ICMS
                        WHERE T219_Id=xT219_Id;
        ELSE
            UPDATE T219
            LEFT JOIN D009 ON D009_Id=T219_D009_Id
            LEFT JOIN D006 ON D006_Id=T219_D006_Id
                        SET T219_Peso_Total=T219_Peso_Unitario*T219_Quantidade,
                        T219_Peso_Bruto_Total=T219_Peso_Bruto*T219_Quantidade,
                        T219_Peso_Total_Confirmado=T219_Peso_Unitario*T219_Quantidade_Confirmada,
                        T219_Valor_Total_Despesas=(T219_Valor_Total_Preco*xMultiplicador_Despesa)+xValor_Total_Agregados,
                        T219_Valor_Total_Despesas_Confirmado=T219_Valor_Total_Preco_Confirmado*xMultiplicador_Despesa_Confirmado,
                        T219_Valor_Despesas_Unitario=T219_Valor_Total_Despesas/T219_Quantidade,
                        T219_Valor_Despesas_Unitario_Confirmado=T219_Valor_Total_Despesas_Confirmado/T219_Quantidade_Confirmada,
                        T219_Valor_Base_IPI=IF(T219_Aliquota_IPI>0,(T219_Valor_Total_Preco+IF(xD006_Flag_Somar_Despesas_Base_IPI='S',T219_Valor_Frete_Total+T219_Valor_Total_Despesas_Acessorias,0))*IF(T219_Percentual_Reducao_IPI>0,(T219_Percentual_Reducao_IPI/100),1),0),
                        T219_Valor_Base_IPI_Confirmado=IF(T219_Aliquota_IPI>0,(T219_Valor_Total_Preco_Confirmado+IF(xD006_Flag_Somar_Despesas_Base_IPI='S',T219_Valor_Frete_Total+T219_Valor_Total_Despesas_Acessorias,0))*IF(T219_Percentual_Reducao_IPI,(T219_Percentual_Reducao_IPI/100),1),0),
                        T219_Valor_IPI=T219_Valor_Base_IPI*(T219_Aliquota_IPI/100),
                        T219_Valor_IPI_Confirmado=T219_Valor_Base_IPI_Confirmado*(T219_Aliquota_IPI/100),
                        T219_Valor_Total_Custo_Final=(T219_Valor_Total_Preco+if(xD006_Flag_Somar_Despesas_Base_ST='S',ifnull(T219_Valor_Total_Despesas,0),0)+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T219_Valor_IPI,0)),
                        T219_Valor_Total_Custo_Final_Confirmado=(T219_Valor_Total_Preco_Confirmado+if(xD006_Flag_Somar_Despesas_Base_ST='S',T219_Valor_Total_Despesas_Confirmado,0)+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T219_Valor_IPI,0)),
                        T219_Valor_Custo_Final_Unitario=T219_Valor_Total_Custo_Final/T219_Quantidade,
                        T219_Valor_Custo_Final_Unitario_Confirmado=T219_Valor_Total_Custo_Final_Confirmado/T219_Quantidade_Confirmada,
                        T219_Valor_Base_ICMS=IF(T219_Aliquota_ICMS>0,T219_Valor_Total_Custo_Final*IF(T219_Percentual_Reducao_ICMS>0,((100-T219_Percentual_Reducao_ICMS)/100),1),0),
                        T219_Valor_Base_ICMS_Confirmado=IF(T219_Aliquota_ICMS>0,T219_Valor_Total_Custo_Final_Confirmado*IF(T219_Percentual_Reducao_ICMS>0,((100-T219_Percentual_Reducao_ICMS)/100),1),0),
                        T219_Valor_ICMS=T219_Valor_Base_ICMS*(T219_Aliquota_ICMS/100),
                        T219_Valor_ICMS_Confirmado=T219_Valor_Base_ICMS_Confirmado*(T219_Aliquota_ICMS/100),
                        T219_Aliquota_PIS=if(T219_Flag_Isento_PIS='S',0,T219_Aliquota_PIS),
                        T219_Aliquota_COFINS=if(T219_Flag_Isento_COFINS='S',0,T219_Aliquota_COFINS),
                        T219_Valor_Base_PIS=if(T219_Flag_Isento_PIS='S',0,if(xD006_Flag_Forma_Calculo_PIS='L',if(T219_Quantidade_Confirmada>0,T219_Valor_Total_Preco_Confirmado-T219_Valor_Total_Custo_Confirmado,T219_Valor_Total_Preco-T219_Valor_Total_Custo),if(T219_Quantidade_Confirmada>0,T219_Valor_Total_Preco_Confirmado,T219_Valor_Total_Preco))),
				                T219_Valor_Base_PIS=if(T219_Valor_Base_PIS>0,if(xD006_Flag_Deduz_ICMS_Base_PIS='S',T219_Valor_Base_PIS-T219_Valor_ICMS,T219_Valor_Base_PIS),0),
                        T219_Valor_PIS=if(T219_Aliquota_PIS>0,T219_Valor_Base_PIS*(T219_Aliquota_PIS/100),0),
                        T219_Valor_Base_COFINS=if(T219_Flag_Isento_COFINS='S',0,if(xD006_Flag_Forma_Calculo_COFINS='L',if(T219_Quantidade_Confirmada>0,T219_Valor_Total_Preco_Confirmado-T219_Valor_Total_Custo_Confirmado,T219_Valor_Total_Preco-T219_Valor_Total_Custo),if(T219_Quantidade_Confirmada>0,T219_Valor_Total_Preco_Confirmado,T219_Valor_Total_Preco))),
				                T219_Valor_Base_COFINS=if(T219_Valor_Base_COFINS>0,if(xD006_Flag_Deduz_ICMS_Base_COFINS='S',T219_Valor_Base_COFINS-T219_Valor_ICMS,T219_Valor_Base_COFINS),0),
                        T219_Valor_COFINS=if(T219_Aliquota_COFINS>0,T219_Valor_Base_COFINS*(T219_Aliquota_COFINS/100),0),
                        T219_IPV=D009_IPV_1,
                        T219_Total_Cubagem = IFNULL(T219_Cubagem_Unitaria, 0) * IFNULL(T219_Quantidade,0),
                        T219_Valor_Trib_Transparencia=IFNULL(T219_Valor_Total_Preco,0)*(IFNULL(T219_Percentual_Trib_Transparencia,0)/100),
                        T219_Valor_Frete=(T219_Valor_Total_Preco+T219_Valor_IPI+T219_Valor_ST+T219_Valor_Financeiro) * (T219_Percentual_Frete/100),
                        T219_Valor_Financeiro=T219_Valor_Total_Preco * (T219_Percentual_Financeiro/100),
                        T219_Valor_Total_Custo=T219_Valor_Total_Preco+T219_Valor_IPI+T219_Valor_ST+T219_Valor_Frete+T219_Valor_Financeiro+T219_Valor_Frete_Total+T219_Valor_ICMS_Diferenca_Aliquotas,
                        T219_Valor_Custo_Unitario=T219_Valor_Total_Custo/T219_Quantidade,
                        T219_Valor_Total_Custo_Confirmado=T219_Valor_Custo_Unitario*T219_Quantidade_Confirmada,
                        T219_Aliquota_ICMS_Diferenca_Aliquotas = IF(xD006_Flag_Somar_DIFAL_Custo='S', (T219_ST_ICMS_Interno - (T219_ST_ICMS_Interno * T219_Percentual_Reducao_ICMS/100)) - T219_Aliquota_ICMS, 0),                         
                        T219_Aliquota_ICMS_Diferenca_Aliquotas = IF(T219_Aliquota_ICMS_Diferenca_Aliquotas > 0 , T219_Aliquota_ICMS_Diferenca_Aliquotas, 0),
                        T219_Valor_ICMS_Diferenca_Aliquotas = (T219_Aliquota_ICMS_Diferenca_Aliquotas/100) * T219_Valor_Base_ICMS
                        WHERE T219_Id=xT219_Id;
        END IF;

        UPDATE T219A
     LEFT JOIN T219 ON T219_Id=T219A_T219_Id
           SET T219A_Valor_Base_FCP_ST=IF(xD006_Flag_Calcula_FCP_ST = 'S', T219_Valor_Base_ST,0),
               T219A_Valor_FCP_ST=IF(T219A_Valor_Base_FCP_ST > 0, T219A_Valor_Base_FCP_ST*(T219A_Aliquota_FCP/100),0),
               T219A_Valor_FCP_ST= IF(T219A_Valor_FCP_ST < 0, 0, T219A_Valor_FCP_ST),
               T219A_Valor_Base_FCP=if(xD006_Flag_Calcula_FCP = 'S', T219_Valor_Base_ICMS,0),
               T219A_Valor_FCP=T219A_Valor_Base_FCP*(T219A_Aliquota_FCP/100),
               T219_Valor_ST = T219_Valor_ST - T219A_Valor_FCP_ST
         WHERE T219A_T219_Id=xT219_Id;

        CALL T219_Gravar_Totalizacao_Reforma(xT219_Id);
        CALL T218_Gravar_Totalizacao_3(xT218_Id);
END;
