CREATE PROCEDURE `T225_Gravar_Totalizacao`(xT225_Id int(11))
BEGIN
		DECLARE xT224_Id int;
		DECLARE xPercentual_Desconto DECIMAL(20,10) DEFAULT 0;
		DECLARE xPercentual_Desconto_Bruto DECIMAL(20,10) DEFAULT 0;
		DECLARE xMultiplicador_Despesa DECIMAL(20,10) DEFAULT 0;
		DECLARE xMultiplicador_Despesa_Recebida DECIMAL(20,10) DEFAULT 0;
		DECLARE xMultiplicador_Despesas_Acessorias DECIMAL(20,10) DEFAULT 0;
		DECLARE xMultiplicador_Despesas_Acessorias_Recebida DECIMAL(20,10) DEFAULT 0;
		DECLARE xMultiplicador_Frete DECIMAL(20,10) DEFAULT 0;
		DECLARE xMultiplicador_Frete_Recebida DECIMAL(20,10) DEFAULT 0;
		DECLARE xFlag_Revenda_Consumidor VARCHAR(1);
		DECLARE xFlag_Devolucao VARCHAR(1);
		DECLARE xFlag_Tipo_Devolucao VARCHAR(10);
		DECLARE xValor_Total_Frete DECIMAL(19,2) DEFAULT 0;
		DECLARE xValor_Total_Frete_Recebida DECIMAL(19,2) DEFAULT 0;
		DECLARE xValor_Total_Despesas_Acessorias DECIMAL(10,2) DEFAULT 0;
		DECLARE xValor_Total_Despesas_Acessorias_Recebida DECIMAL(10,2) DEFAULT 0;
		DECLARE xValor_Total_Agregados DECIMAL(10,2) DEFAULT 0;
		DECLARE xValor_Total_Desconto DECIMAL(10,2) DEFAULT 0;
		DECLARE xValor_Total_Desconto_Recebida DECIMAL(10,2) DEFAULT 0;
		DECLARE xFlag_ST VARCHAR(3);
		DECLARE xFlag_Venda_Compra_Outros VARCHAR(1);
		DECLARE xAliquota_ICMS_1 DECIMAL(5,2);

		DECLARE xT134_Id int(10) default '0';
		DECLARE xT134_Reducao_Base_ICMS decimal(10,2) default '0';

		DECLARE xD006_Flag_Ajustar_MVA varchar(1);
		DECLARE xT224_Flag_Calcular_ST varchar(1);
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

		DECLARE xD009_IPV_1 DECIMAL(20,2) DEFAULT 0;
		DECLARE xD006_Flag_Somar_DIFAL_Custo VARCHAR(1) DEFAULT 'N';

	    DECLARE xT225_Flag_Origem_Produto varchar(1) default '';
	    DECLARE xT225_Flag_Destino_Produto varchar(1) default '';
		DECLARE xC004_D018_Id int(11) default 0;
		DECLARE xD024_D018_Id int(11) default 0;
		DECLARE xT225A_Percentual_Reducao_ICMS_IE_ST decimal(10,2) default 0;
		DECLARE xT225A_Percentual_Reducao_ICMS_INT_ST decimal(10,2) default 0;
		DECLARE xD053_Calculo_ST_Por_Dentro_Compra varchar (1);
		DECLARE xD006_Flag_Calcula_FCP_ST varchar(1);
		DECLARE xD006_Flag_Calcula_FCP varchar(1);
		DECLARE xD006_Flag_Deduz_ICMS_Base_PIS varchar(1);
		DECLARE xD006_Flag_Deduz_ICMS_Base_COFINS varchar(1);

		-- *_Recebida (para analise da OC) agora está na T014, por isso o T225_Quantidade_Recebida precisa ser a soma dos T014_Quantidade
		#  Realizando UPDATE abaixo
		# UPDATE T225 SET T225_Quantidade_Recebida = (SELECT SUM(T014_Quantidade) FROM T014 WHERE T014_T225_Id=xT225_Id) WHERE T225_Id=xT225_Id;
		
		-- caso esteja utilizando a tabela T185, a quantidade de itens separados tem que ser pega desta tabela
		SELECT COUNT(T185_Qtd_Separado) INTO xT185_Qtd_Separado FROM T185 WHERE T185_T225_Id = xT225_Id;
		IF xT185_Qtd_Separado > 0 THEN
			SELECT SUM(T185_Qtd_Separado) INTO xT185_Qtd_Separado FROM T185 WHERE T185_T225_Id = xT225_Id;
			UPDATE T225 SET T225_Quantidade_Separacao = xT185_Qtd_Separado WHERE T225_Id = xT225_Id;
		END IF;

		SELECT ifnull(SUM(T204_Valor_Unitario),0) INTO xValor_Total_Agregados FROM T204 WHERE T204_T225_Id=xT225_Id;
		
		IF xValor_Total_Agregados IS NULL THEN
        	SET xValor_Total_Agregados =0;
      	END IF;
		
		-- GRAVA A CFOP POR ITEM PARA OS QUE AINDA NAO TEM CFOP
		UPDATE T225 left join T224 on T224_Id=T225_T224_Id set T225_D006_Id=T224_D006_Id where T225_Id=xT225_Id and T225_D006_Id is null;

		SELECT T225_T224_Id,
			   D006_Flag_Venda_Compra_Outros,
			   D049_Flag_Nacional_Importado,
			   D006_Flag_Somar_IPI_Base_ICMS,
			   D006_Flag_Somar_Despesas_Base_IPI,
			   D006_Flag_Somar_Despesas_Base_ST,
			   D006_Flag_Forma_Calculo_COFINS,
			   D006_Flag_Forma_Calculo_PIS,
			   D006_Flag_Ajustar_MVA,
			   T225_C004_Id,
			   T134_Reducao_Base_ICMS,
			   T134_Id,
			   T225_Percentual_Desconto_Bruto/100,
			   T224_Flag_Calcular_ST,
			   D006_Flag_Somar_DIFAL_Custo,
			   T225_Flag_Origem_Produto,
           	   T225_Flag_Destino_Produto,
			   C004_D018_Id,
			   D024_D018_Id,
			   T225A_Percentual_Reducao_ICMS_IE_ST,
			   T225A_Percentual_Reducao_ICMS_INT_ST,
			   D053_Calculo_ST_Por_Dentro_Compra,
			   D006_Flag_Calcula_FCP_ST,
			   D006_Flag_Calcula_FCP,
			   D009_IPV_1,
			   D006_Flag_Deduz_ICMS_Base_PIS,
			   D006_Flag_Deduz_ICMS_Base_COFINS
		  INTO xT224_Id,
		  	   xFlag_Venda_Compra_Outros,
		   	   xD049_Flag_Nacional_Importado,
			   xD006_Flag_Somar_IPI_Base_ICMS,
			   xD006_Flag_Somar_Despesas_Base_IPI,
			   xD006_Flag_Somar_Despesas_Base_ST,
			   xD006_Flag_Forma_Calculo_COFINS,
			   xD006_Flag_Forma_Calculo_PIS,
			   xD006_Flag_Ajustar_MVA,
			   xC004_Id,
			   xT134_Reducao_Base_ICMS,
			   xT134_Id,
			   xPercentual_Desconto_Bruto,
			   xT224_Flag_Calcular_ST,
			   xD006_Flag_Somar_DIFAL_Custo,
			   xT225_Flag_Origem_Produto,
			   xT225_Flag_Destino_Produto,
			   xC004_D018_Id,
			   xD024_D018_Id,
			   xT225A_Percentual_Reducao_ICMS_IE_ST,
			   xT225A_Percentual_Reducao_ICMS_INT_ST,
			   xD053_Calculo_ST_Por_Dentro_Compra,
			   xD006_Flag_Calcula_FCP_ST,
			   xD006_Flag_Calcula_FCP,
			   xD009_IPV_1,
			   xD006_Flag_Deduz_ICMS_Base_PIS,
			   xD006_Flag_Deduz_ICMS_Base_COFINS
	  	  FROM T225
	 LEFT JOIN T224 ON T224_Id=T225_T224_Id
	 LEFT JOIN D006 ON D006_Id=T225_D006_Id
	 LEFT JOIN T134 ON T134_Id=T224_T134_Id
	 LEFT JOIN D024 ON D024_Id=T224_D024_Id
	 LEFT JOIN D020 ON D020_Id=D024_D020_Id
	 LEFT JOIN D009 ON D009_Id=T225_D009_Id
	 LEFT JOIN D049 ON D049_Id=D009_D049_Id
	 LEFT JOIN D001 ON D001_Id=D049_D001_Id
     LEFT JOIN C004 ON C004_Id=T224_C004_Id
	 LEFT JOIN D053 ON D053_D018_Id=D024_D018_Id AND D053_C004_Id=T224_C004_Id
	 LEFT JOIN T225A ON T225A_T225_Id=T225_Id
		 WHERE T225_Id=xT225_Id
		 LIMIT 1;

		 SELECT COUNT(*) INTO xTotal_Itens FROM T225 WHERE T225_T224_Id = xT224_Id;
		 
		SELECT T224_Percentual_Desconto/100,
			   T224_Flag_Devolucao,
			   T224_Flag_Tipo_Devolucao,
			   C004_Casas_Decimais_Preco,
	  		   C004_Flag_Rateio_Frete
		  INTO xPercentual_Desconto,
		  	   xFlag_Devolucao,
			   xFlag_Tipo_Devolucao,
			   xC004_Casas_Decimais_Preco,
			   xC004_Flag_Rateio_Frete
		  FROM T224
	 LEFT JOIN C004 ON C004_Id=T224_C004_Id
		 WHERE T224_Id=xT224_Id;
		 
		IF xPercentual_Desconto IS NULL THEN
			 set xPercentual_Desconto = 0;
		END IF;   
		
		UPDATE T225 SET T225_Quantidade_Recebida = (SELECT SUM(T014_Quantidade) FROM T014 WHERE T014_T225_Id=xT225_Id),
						T225_Valor_Desconto_Unitario = if(xPercentual_Desconto>0,T225_Valor_Preco_Sem_Desconto_Unitario*xPercentual_Desconto,0),
						T225_Valor_Desconto_Unitario_Bruto = if(xPercentual_Desconto_Bruto>0 or xPercentual_Desconto_Bruto<0,T225_Valor_Preco_Original_Bruto*xPercentual_Desconto_Bruto,0),
						T225_Valor_Total_Desconto = T225_Valor_Desconto_Unitario*T225_Quantidade,
						T225_Valor_Total_Desconto_Recebida = T225_Valor_Desconto_Unitario*T225_Quantidade_Recebida,
						T225_Valor_Total_Preco = T225_Valor_Preco_Unitario*T225_Quantidade,
						T225_Valor_Total_Preco_Recebida = T225_Valor_Preco_Unitario*T225_Quantidade_Recebida,
						T225_Valor_Total_Preco_Sem_Desconto = T225_Valor_Preco_Sem_Desconto_Unitario * T225_Quantidade,
						T225_Valor_Total_Preco_Sem_Desconto_Recebida = T225_Valor_Preco_Sem_Desconto_Unitario * T225_Quantidade_Recebida,
						T225_Valor_Preco_Bruto = round(T225_Valor_Preco_Original_Bruto - T225_Valor_Desconto_Unitario_Bruto,xC004_Casas_Decimais_Preco)
				  WHERE T225_Id=xT225_Id;

		IF xFlag_Devolucao='S' AND xFlag_Tipo_Devolucao='EMITIDA' THEN

			 UPDATE T225 SET T225_Valor_Total_Frete=T225_Valor_Frete_Unitario*T225_Quantidade,
							 T225_Valor_Total_Frete_Recebida=T225_Valor_Frete_Unitario*T225_Quantidade_Recebida,
							 T225_Valor_Total_Despesas_Acessorias=T225_Valor_Despesas_Acessorias_Unitario*T225_Quantidade,
							 T225_Valor_Total_Despesas_Acessorias_Recebida=T225_Valor_Despesas_Acessorias_Unitario*T225_Quantidade_Recebida,
							 T225_Valor_Total_Desconto=T225_Valor_Desconto_Unitario*T225_Quantidade,
							 T225_Valor_Total_Desconto_Recebida=T225_Valor_Desconto_Unitario*T225_Quantidade_Recebida
					   WHERE T225_T224_Id=xT224_Id;

			 SELECT SUM(T225_Valor_Total_Frete),
					SUM(T225_Valor_Total_Despesas_Acessorias),
					SUM(T225_Valor_Total_Desconto)
			   INTO xValor_Total_Frete,
					xValor_Total_Despesas_Acessorias,
					xValor_Total_Desconto
		       FROM T225
			  WHERE T225_T224_Id=xT224_Id
		   GROUP BY T225_T224_Id;
		
			UPDATE T224 SET T224_Valor_Frete=xValor_Total_Frete,
			   		        T224_Valor_Despesas_Acessorias=xValor_Total_Despesas_Acessorias
				      WHERE T224_Id=xT224_Id;

			SELECT SUM(T225_Valor_Total_Frete_Recebida),
				   SUM(T225_Valor_Total_Despesas_Acessorias_Recebida),
				   SUM(T225_Valor_Total_Desconto_Recebida)
			  INTO xValor_Total_Frete_Recebida,
				   xValor_Total_Despesas_Acessorias_Recebida,
				   xValor_Total_Desconto_Recebida
			  FROM T225
			 WHERE T225_T224_Id=xT224_Id
		  GROUP BY T225_T224_Id;
		
	 		UPDATE T224
			   SET T224_Valor_Frete_Recebida=xValor_Total_Frete_Recebida,
			       T224_Valor_Despesas_Acessorias_Recebida=xValor_Total_Despesas_Acessorias_Recebida
			 WHERE T224_Id=xT224_Id; 
		
		ELSE
				
			SELECT T224_Valor_Frete/sum(T225_Valor_Preco_Unitario*T225_Quantidade),
			 	   T224_Valor_Frete,
				   T224_Valor_Despesas_Acessorias/sum(T225_Valor_Preco_Unitario*T225_Quantidade),
				   T224_Valor_Frete_Recebida/sum(T225_Valor_Preco_Unitario*T225_Quantidade_Recebida),
				   T224_Valor_Frete_Recebida,
				   T224_Valor_Despesas_Acessorias_Recebida/sum(T225_Valor_Preco_Unitario*T225_Quantidade_Recebida)
			  INTO xMultiplicador_Frete,
			   	   xValor_Total_Frete,
				   xMultiplicador_Despesas_Acessorias,
				   xMultiplicador_Frete_Recebida,
			  	   xValor_Total_Frete_Recebida,
				   xMultiplicador_Despesas_Acessorias_Recebida
			  FROM T224
		 LEFT JOIN T225 ON T225_T224_Id=T224_Id
			 WHERE T224_Id=xT224_Id
		  GROUP BY T224_Id;
		
			#  Realizando UPDATE abaixo
			#  UPDATE T225 SET T225_Valor_Total_Despesas_Acessorias=T225_Valor_Total_Preco*xMultiplicador_Despesas_Acessorias,
			#				T225_Valor_Despesas_Acessorias_Unitario=T225_Valor_Total_Despesas_Acessorias/T225_Quantidade,
			#				T225_Valor_Total_Frete=IF(xC004_Flag_Rateio_Frete=1,T225_Valor_Total_Preco*xMultiplicador_Frete,xValor_Total_Frete/xTotal_Itens),
			#				T225_Valor_Frete_Unitario=T225_Valor_Total_Frete/T225_Quantidade,
			#				T225_Valor_Total_Despesas_Acessorias_Recebida=T225_Valor_Total_Preco_Recebida*xMultiplicador_Despesas_Acessorias_Recebida,
			#				T225_Valor_Despesas_Acessorias_Unitario_Recebida=T225_Valor_Total_Despesas_Acessorias_Recebida/T225_Quantidade_Recebida,
			#				T225_Valor_Total_Frete_Recebida=IF(xC004_Flag_Rateio_Frete=1,T225_Valor_Total_Preco_Recebida*xMultiplicador_Frete_Recebida,xValor_Total_Frete_Recebida/xTotal_Itens),
			#				T225_Valor_Frete_Unitario_Recebida=T225_Valor_Total_Frete_Recebida/T225_Quantidade_Recebida
			#		  WHERE T225_Id=xT225_Id;

		END IF;

		SELECT (T224_Valor_Frete+T224_Valor_Despesas_Acessorias)/sum(T225_Valor_Preco_Unitario*T225_Quantidade),
			   (T224_Valor_Frete+T224_Valor_Despesas_Acessorias_Recebida)/sum(T225_Valor_Preco_Unitario*T225_Quantidade_Recebida),
			    T224_Flag_Revenda_Consumidor
		   INTO	
		   		xMultiplicador_Despesa,
				xMultiplicador_Despesa_Recebida,
				xFlag_Revenda_Consumidor
		   FROM T224
	  LEFT JOIN T225 ON T225_T224_Id=T224_Id
	      WHERE T224_Id=xT224_Id
	   GROUP BY T224_Id;
		
		IF xMultiplicador_Despesa IS NULL THEN
			 set xMultiplicador_Despesa = 0;
		END IF;
		IF xMultiplicador_Despesa_Recebida IS NULL THEN
			 set xMultiplicador_Despesa_Recebida = 0;
		END IF;

		# realizando query la em cima quando pega dados do T225
		# SELECT D009_IPV_1 INTO xD009_IPV_1 FROM T225 LEFT JOIN D009 ON D009_Id=T225_D009_Id WHERE T225_Id=xT225_Id;
		
		IF xT224_Flag_Calcular_ST = 'S' THEN
			UPDATE T225 
	     LEFT JOIN D006 ON D006_Id=T225_D006_Id
		 	   SET T225_Valor_Total_Despesas_Acessorias=T225_Valor_Total_Preco*xMultiplicador_Despesas_Acessorias,
				   T225_Valor_Despesas_Acessorias_Unitario=T225_Valor_Total_Despesas_Acessorias/T225_Quantidade,
				   T225_Valor_Total_Frete=IF(xC004_Flag_Rateio_Frete=1,T225_Valor_Total_Preco*xMultiplicador_Frete,xValor_Total_Frete/xTotal_Itens),
				   T225_Valor_Frete_Unitario=T225_Valor_Total_Frete/T225_Quantidade,
				   T225_Valor_Total_Despesas_Acessorias_Recebida=T225_Valor_Total_Preco_Recebida*xMultiplicador_Despesas_Acessorias_Recebida,
				   T225_Valor_Despesas_Acessorias_Unitario_Recebida=T225_Valor_Total_Despesas_Acessorias_Recebida/T225_Quantidade_Recebida,
				   T225_Valor_Total_Frete_Recebida=IF(xC004_Flag_Rateio_Frete=1,T225_Valor_Total_Preco_Recebida*xMultiplicador_Frete_Recebida,xValor_Total_Frete_Recebida/xTotal_Itens),
				   T225_Valor_Frete_Unitario_Recebida=T225_Valor_Total_Frete_Recebida/T225_Quantidade_Recebida,
			   	   T225_Peso_Total = T225_Peso_Unitario*T225_Quantidade,
   				   T225_Peso_Bruto_Total = T225_Peso_Bruto*T225_Quantidade,
   				   T225_Valor_Total_Despesas=(T225_Valor_Total_Preco*xMultiplicador_Despesa)+xValor_Total_Agregados,
   				   T225_Valor_Despesas_Unitario=T225_Valor_Total_Despesas/T225_Quantidade,
   				   T225_Valor_Total_Agregados=xValor_Total_Agregados,
   				   T225_Valor_Base_IPI=If(T225_Aliquota_IPI>0,(T225_Valor_Total_Preco+if(xD006_Flag_Somar_Despesas_Base_IPI='S',T225_Valor_Total_Frete+T225_Valor_Total_Despesas_Acessorias,0))*If(T225_Percentual_Reducao_IPI>0,(T225_Percentual_Reducao_IPI/100),1),0),
   				   T225_Valor_IPI=T225_Valor_Base_IPI*(T225_Aliquota_IPI/100),
   				   T225_Valor_Total_Custo_Final=(T225_Valor_Total_Preco+if(xD006_Flag_Somar_Despesas_Base_ST='S',T225_Valor_Total_Despesas,0)+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T225_Valor_IPI,0)),
   				   T225_Valor_Custo_Final_Unitario=T225_Valor_Total_Custo_Final/T225_Quantidade,
   				   T225_Valor_Base_ICMS=If(T225_Aliquota_ICMS>0,T225_Valor_Total_Custo_Final*if(T225_Percentual_Reducao_ICMS>0,((100-T225_Percentual_Reducao_ICMS)/100),1),0),
   				   T225_Valor_ICMS=T225_Valor_Base_ICMS*(T225_Aliquota_ICMS/100),
   				   #T225_ST_VA_Ajustada = IF(xD006_Flag_Ajustar_MVA = 'S', ( ( (1+(T225_ST_VA/100)) *   ( ( 1 - ( T225_ICMS_Inter_Estadual) / 100 ) / ( 1 - ( T225_ST_ICMS_Interno / 100 ) ) ) ) - 1 ) * 100, T225_ST_VA ),
				   T225_ST_VA_Ajustada = TRIB_ST_VA_Ajustada(xD006_Flag_Ajustar_MVA, T225_ST_VA, T225_ICMS_Inter_Estadual, T225_ST_ICMS_Interno, T225_Percentual_Reducao_ICMS_ST, 'C', T225_Percentual_Reducao_ICMS, xT225A_Percentual_Reducao_ICMS_IE_ST, xT225A_Percentual_Reducao_ICMS_INT_ST, xT225_Flag_Origem_Produto, xT225_Flag_Destino_Produto, xC004_D018_Id, xD024_D018_Id),                       
				   #T225_Valor_Base_ICMS_Substituicao=IF(T225_Flag_ST='S',(T225_Valor_Total_Preco+if(xD006_Flag_Somar_Despesas_Base_ST='S',T225_Valor_Total_Despesas,0)+T225_Valor_IPI)*((T225_ST_VA_Ajustada/100)+1),0),
                   T225_Valor_Base_ICMS_Substituicao=IF(T225_Flag_ST='S',TRIB_Base_ST(T225_Flag_ST, T225_Valor_Total_Preco, if(xD006_Flag_Somar_Despesas_Base_ST='S',T225_Valor_Total_Despesas,0), T225_Valor_IPI, T225_ST_VA_Ajustada, xD024_D018_Id, xC004_D018_Id, ifnull(T225_Flag_Destino_Produto,0), xD053_Calculo_ST_Por_Dentro_Compra, T225_ST_ICMS_Interno, T225_Valor_ICMS, T225_ICMS_Inter_Estadual, 0, T225_Modalidade_ICMS_Substituicao),0),
                   T225_Valor_ICMS_Substituicao=(T225_Valor_Base_ICMS_Substituicao*(T225_ST_ICMS_Interno/100)),
                   #T225_Valor_ICMS_Substituicao_Retencao=IF(T225_Percentual_ST,(T225_Valor_Total_Preco+T225_Valor_IPI)*(T225_Percentual_ST/100),((T225_Valor_Base_ICMS_Substituicao*if(T225_Percentual_Reducao_ICMS_ST>0,((100-T225_Percentual_Reducao_ICMS_ST)/100),1))*(T225_ST_ICMS_Interno/100))-((T225_Valor_Total_Custo_Final*if(T225_Percentual_Reducao_ICMS>0,((100-T225_Percentual_Reducao_ICMS)/100),1))*(T225_ICMS_Inter_Estadual/100))),
                   T225_Valor_ICMS_Substituicao_Retencao=IF(T225_Percentual_ST,(T225_Valor_Total_Preco+T225_Valor_IPI)*(T225_Percentual_ST/100),IF(T225_Flag_ST='S',TRIB_Valor_ST(T225_Valor_Base_ICMS_Substituicao, T225_Percentual_Reducao_ICMS_ST, T225_ST_ICMS_Interno, T225_Valor_Total_Custo_Final, T225_Percentual_Reducao_ICMS, T225_ICMS_Inter_Estadual, T225_ST_VA_Ajustada, xD024_D018_Id, xC004_D018_Id, ifnull(xT225_Flag_Destino_Produto,0), 0, T225_Modalidade_ICMS_Substituicao),0)),
                   T225_Valor_ICMS_Substituicao_Retencao=IF(T225_Valor_ICMS_Substituicao_Retencao<0,0,T225_Valor_ICMS_Substituicao_Retencao),
                   T225_Valor_Base_PIS=if(T225_Flag_Isento_PIS='S',0,if(xD006_Flag_Forma_Calculo_PIS='L',T225_Valor_Total_Preco-T225_Valor_Total_Custo,T225_Valor_Total_Preco)),
				   T225_Valor_Base_PIS=if(T225_Valor_Base_PIS>0,if(xD006_Flag_Deduz_ICMS_Base_PIS='S',T225_Valor_Base_PIS-T225_Valor_ICMS,T225_Valor_Base_PIS),0),
                   T225_Aliquota_PIS=if(T225_Flag_Isento_PIS='S',0,T225_Aliquota_PIS),
                   T225_Valor_PIS=if(T225_Aliquota_PIS>0,T225_Valor_Base_PIS*(T225_Aliquota_PIS/100),0),
                   T225_Valor_Base_COFINS=if(T225_Flag_Isento_COFINS='S',0,if(xD006_Flag_Forma_Calculo_COFINS='L',T225_Valor_Total_Preco-T225_Valor_Total_Custo,T225_Valor_Total_Preco)),
				   T225_Valor_Base_COFINS=if(T225_Valor_Base_COFINS>0,if(xD006_Flag_Deduz_ICMS_Base_COFINS='S',T225_Valor_Base_COFINS-T225_Valor_ICMS,T225_Valor_Base_COFINS),0),
                   T225_Aliquota_COFINS=if(T225_Flag_Isento_COFINS='S',0,T225_Aliquota_COFINS),
				   T225_Valor_COFINS=if(T225_Aliquota_COFINS>0,T225_Valor_Base_COFINS*(T225_Aliquota_COFINS/100),0),
				   T225_Valor_ICMS_Credito=if(xT134_Id<=0 or xT134_Id='' or ifnull(xT134_Id,0)<=0,T225_Valor_ICMS_Credito,If(T225_Aliquota_ICMS_Credito>0,(T225_Valor_Total_Custo_Final+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T225_Valor_IPI,0))-((T225_Valor_Total_Custo_Final+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T225_Valor_IPI,0))*if(xT134_Reducao_Base_ICMS>0,(xT134_Reducao_Base_ICMS/100),1)),0)*(T225_Aliquota_ICMS_Credito/100)),
				   T225_Total_Cubagem = IFNULL(T225_Cubagem_Unitaria,0) * IFNULL(T225_Quantidade,0),
				   T225_Valor_Trib_Transparencia=IFNULL(T225_Valor_Total_Preco,0)*(IFNULL(T225_Percentual_Trib_Transparencia,0)/100),
				   T225_Valor_Frete=(T225_Valor_Total_Preco+T225_Valor_IPI+T225_Valor_ICMS_Substituicao_Retencao+T225_Valor_Financeiro) * (T225_Percentual_Frete/100),
				   T225_Valor_Financeiro = T225_Valor_Total_Preco * (T225_Percentual_Financeiro/100),
				   T225_Valor_Total_Custo=T225_Valor_Total_Preco+T225_Valor_IPI+T225_Valor_ICMS_Substituicao_Retencao+T225_Valor_Frete+T225_Valor_Total_Frete+T225_Valor_Financeiro+T225_Valor_ICMS_Diferenca_Aliquotas,
                   T225_Valor_Custo_Unitario=T225_Valor_Total_Custo/T225_Quantidade,
				   T225_IPV=xD009_IPV_1,
				   T225_Aliquota_ICMS_Diferenca_Aliquotas = IF(xD006_Flag_Somar_DIFAL_Custo='S', (T225_ST_ICMS_Interno - (T225_ST_ICMS_Interno * T225_Percentual_Reducao_ICMS/100)) - T225_Aliquota_ICMS, 0),                         
                   T225_Aliquota_ICMS_Diferenca_Aliquotas = IF(T225_Aliquota_ICMS_Diferenca_Aliquotas > 0, T225_Aliquota_ICMS_Diferenca_Aliquotas, 0),
				   T225_Valor_ICMS_Diferenca_Aliquotas = (T225_Aliquota_ICMS_Diferenca_Aliquotas/100) * T225_Valor_Base_ICMS
			 WHERE T225_Id=xT225_Id;

		ELSE

			UPDATE T225 
		 LEFT JOIN D006 ON D006_Id=T225_D006_Id
		  	   SET T225_Valor_Total_Despesas_Acessorias=T225_Valor_Total_Preco*xMultiplicador_Despesas_Acessorias,
   				   T225_Valor_Despesas_Acessorias_Unitario=T225_Valor_Total_Despesas_Acessorias/T225_Quantidade,
   				   T225_Valor_Total_Frete=IF(xC004_Flag_Rateio_Frete=1,T225_Valor_Total_Preco*xMultiplicador_Frete,xValor_Total_Frete/xTotal_Itens),
   				   T225_Valor_Frete_Unitario=T225_Valor_Total_Frete/T225_Quantidade,
   				   T225_Valor_Total_Despesas_Acessorias_Recebida=T225_Valor_Total_Preco_Recebida*xMultiplicador_Despesas_Acessorias_Recebida,
   				   T225_Valor_Despesas_Acessorias_Unitario_Recebida=T225_Valor_Total_Despesas_Acessorias_Recebida/T225_Quantidade_Recebida,
   				   T225_Valor_Total_Frete_Recebida=IF(xC004_Flag_Rateio_Frete=1,T225_Valor_Total_Preco_Recebida*xMultiplicador_Frete_Recebida,xValor_Total_Frete_Recebida/xTotal_Itens),
   				   T225_Valor_Frete_Unitario_Recebida=T225_Valor_Total_Frete_Recebida/T225_Quantidade_Recebida,
				   T225_Peso_Total = T225_Peso_Unitario*T225_Quantidade,
				   T225_Peso_Bruto_Total = T225_Peso_Bruto*T225_Quantidade,
				   T225_Valor_Total_Despesas=(T225_Valor_Total_Preco*xMultiplicador_Despesa)+xValor_Total_Agregados,
				   T225_Valor_Despesas_Unitario=T225_Valor_Total_Despesas/T225_Quantidade,
				   T225_Valor_Total_Agregados=xValor_Total_Agregados,
				   T225_Valor_Base_IPI=If(T225_Aliquota_IPI>0,(T225_Valor_Total_Preco+if(xD006_Flag_Somar_Despesas_Base_IPI='S',T225_Valor_Total_Frete+T225_Valor_Total_Despesas_Acessorias,0))*If(T225_Percentual_Reducao_IPI>0,(T225_Percentual_Reducao_IPI/100),1),0),
				   T225_Valor_IPI=T225_Valor_Base_IPI*(T225_Aliquota_IPI/100),
				   T225_Valor_Total_Custo_Final=(T225_Valor_Total_Preco+if(xD006_Flag_Somar_Despesas_Base_ST='S',T225_Valor_Total_Despesas,0)+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T225_Valor_IPI,0)),
				   T225_Valor_Custo_Final_Unitario=T225_Valor_Total_Custo_Final/T225_Quantidade,
				   T225_Valor_Base_ICMS=If(T225_Aliquota_ICMS>0,T225_Valor_Total_Custo_Final*if(T225_Percentual_Reducao_ICMS>0,((100-T225_Percentual_Reducao_ICMS)/100),1),0),
				   T225_Valor_ICMS=T225_Valor_Base_ICMS*(T225_Aliquota_ICMS/100),
				   T225_Valor_Base_ICMS_Substituicao=IF(T225_Flag_ST='S',(T225_Valor_Total_Preco+if(xD006_Flag_Somar_Despesas_Base_ST='S',T225_Valor_Total_Despesas,0)+T225_Valor_IPI)*((T225_ST_VA_Ajustada/100)+1),0),
				   T225_Valor_Base_PIS=if(T225_Flag_Isento_PIS='S',0,if(xD006_Flag_Forma_Calculo_PIS='L',T225_Valor_Total_Preco-T225_Valor_Total_Custo,T225_Valor_Total_Preco)),
				   T225_Valor_Base_PIS=if(T225_Valor_Base_PIS>0,if(xD006_Flag_Deduz_ICMS_Base_PIS='S',T225_Valor_Base_PIS-T225_Valor_ICMS,T225_Valor_Base_PIS),0),
				   T225_Aliquota_PIS=if(T225_Flag_Isento_PIS='S',0,T225_Aliquota_PIS),
				   T225_Valor_PIS=if(T225_Aliquota_PIS>0,T225_Valor_Base_PIS*(T225_Aliquota_PIS/100),0),
				   T225_Valor_Base_COFINS=if(T225_Flag_Isento_COFINS='S',0,if(xD006_Flag_Forma_Calculo_COFINS='L',T225_Valor_Total_Preco-T225_Valor_Total_Custo,T225_Valor_Total_Preco)),
				   T225_Valor_Base_COFINS=if(T225_Valor_Base_COFINS>0,if(xD006_Flag_Deduz_ICMS_Base_COFINS='S',T225_Valor_Base_COFINS-T225_Valor_ICMS,T225_Valor_Base_COFINS),0),
				   T225_Aliquota_COFINS=if(T225_Flag_Isento_COFINS='S',0,T225_Aliquota_COFINS),
				   T225_Valor_COFINS=if(T225_Aliquota_COFINS>0,T225_Valor_Base_COFINS*(T225_Aliquota_COFINS/100),0),
				   T225_Valor_ICMS_Credito=if(xT134_Id<=0 or xT134_Id='' or ifnull(xT134_Id,0)<=0,T225_Valor_ICMS_Credito,If(T225_Aliquota_ICMS_Credito>0,(T225_Valor_Total_Custo_Final+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T225_Valor_IPI,0))-((T225_Valor_Total_Custo_Final+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T225_Valor_IPI,0))*if(xT134_Reducao_Base_ICMS>0,(xT134_Reducao_Base_ICMS/100),1)),0)*(T225_Aliquota_ICMS_Credito/100)),
				   T225_Total_Cubagem = IFNULL(T225_Cubagem_Unitaria,0) * IFNULL(T225_Quantidade,0),
				   T225_Valor_Trib_Transparencia=IFNULL(T225_Valor_Total_Preco,0)*(IFNULL(T225_Percentual_Trib_Transparencia,0)/100),
				   T225_Valor_Frete=(T225_Valor_Total_Preco+T225_Valor_IPI+T225_Valor_ICMS_Substituicao_Retencao+T225_Valor_Financeiro) * (T225_Percentual_Frete/100),
				   T225_Valor_Financeiro = T225_Valor_Total_Preco * (T225_Percentual_Financeiro/100),
				   T225_Valor_Total_Custo=T225_Valor_Total_Preco+T225_Valor_IPI+T225_Valor_ICMS_Substituicao_Retencao+T225_Valor_Frete+T225_Valor_Total_Frete+T225_Valor_Financeiro+T225_Valor_ICMS_Diferenca_Aliquotas,
                   T225_Valor_Custo_Unitario=T225_Valor_Total_Custo/T225_Quantidade,
				   T225_IPV=xD009_IPV_1,
				   T225_Aliquota_ICMS_Diferenca_Aliquotas = IF(xD006_Flag_Somar_DIFAL_Custo='S', (T225_ST_ICMS_Interno - (T225_ST_ICMS_Interno * T225_Percentual_Reducao_ICMS/100)) - T225_Aliquota_ICMS, 0),                         
                   T225_Aliquota_ICMS_Diferenca_Aliquotas = IF(T225_Aliquota_ICMS_Diferenca_Aliquotas > 0, T225_Aliquota_ICMS_Diferenca_Aliquotas, 0),
				   T225_Valor_ICMS_Diferenca_Aliquotas = (T225_Aliquota_ICMS_Diferenca_Aliquotas/100) * T225_Valor_Base_ICMS
			 WHERE T225_Id=xT225_Id;

		END IF;

		UPDATE T225 
     LEFT JOIN T014 ON T014_T225_Id=T225_Id
     LEFT JOIN D006 ON D006_Id=T225_D006_Id
		   SET T225_Peso_Total_Recebida = T225_Peso_Unitario*T014_Quantidade,
			   T225_Peso_Bruto_Total_Recebida = T225_Peso_Bruto*T014_Quantidade,
			   T225_Valor_Total_Despesas_Recebida=(T225_Valor_Total_Preco_Recebida*xMultiplicador_Despesa_Recebida)+xValor_Total_Agregados,
			   T225_Valor_Despesas_Unitario_Recebida=T225_Valor_Total_Despesas_Recebida/T014_Quantidade,
			   -- T225_Valor_Total_Agregados=xValor_Total_Agregados,
			   T225_Valor_Base_IPI_Recebida=If(T225_Aliquota_IPI>0,(T225_Valor_Total_Preco_Recebida+if(xD006_Flag_Somar_Despesas_Base_IPI='S',T225_Valor_Total_Frete_Recebida+T225_Valor_Total_Despesas_Acessorias_Recebida,0))*If(T225_Percentual_Reducao_IPI>0,(T225_Percentual_Reducao_IPI/100),1),0),
			   T225_Valor_IPI_Recebida=T225_Valor_Base_IPI_Recebida*(T225_Aliquota_IPI/100),
			   T225_Valor_Total_Custo_Final_Recebida=(T225_Valor_Total_Preco_Recebida+if(xD006_Flag_Somar_Despesas_Base_ST='S',T225_Valor_Total_Despesas_Recebida,0)+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T225_Valor_IPI_Recebida,0)),
			   T225_Valor_Custo_Final_Unitario_Recebida=T225_Valor_Total_Custo_Final_Recebida/T014_Quantidade,
			   T225_Valor_Base_ICMS_Recebida=If(T225_Aliquota_ICMS>0,T225_Valor_Total_Custo_Final_Recebida*if(T225_Percentual_Reducao_ICMS>0,((100-T225_Percentual_Reducao_ICMS)/100),1),0),
			   T225_Valor_ICMS_Recebida=T225_Valor_Base_ICMS_Recebida*(T225_Aliquota_ICMS/100),
			   -- T225_IPV=T225_Valor_Preco_Unitario/T225_Valor_Custo_Unitario,
			   T225_Valor_Base_ICMS_Substituicao_Recebida=IF(T225_Flag_ST='S',(T225_Valor_Total_Preco_Recebida+if(xD006_Flag_Somar_Despesas_Base_ST='S',T225_Valor_Total_Despesas_Recebida,0)+T225_Valor_IPI_Recebida)*((T225_ST_VA_Ajustada/100)+1),0),
			   T225_Valor_ICMS_Substituicao_Recebida=(T225_Valor_Base_ICMS_Substituicao_Recebida*(T225_ST_ICMS_Interno/100)),
			   T225_Valor_ICMS_Substituicao_Retencao_Recebida=((T225_Valor_Base_ICMS_Substituicao_Recebida*if(T225_Percentual_Reducao_ICMS_ST>0,(T225_Percentual_Reducao_ICMS_ST/100),1))*(T225_ST_ICMS_Interno/100))-((T225_Valor_Total_Custo_Final_Recebida*if(T225_Percentual_Reducao_ICMS>0,(T225_Percentual_Reducao_ICMS/100),1))*(T225_ICMS_Inter_Estadual/100)),
			   T225_Valor_ICMS_Substituicao_Retencao_Recebida=IF(T225_Valor_ICMS_Substituicao_Retencao_Recebida<0,0,T225_Valor_ICMS_Substituicao_Retencao_Recebida),
			   T225_Valor_Base_PIS_Recebida=if(T225_Flag_Isento_PIS='S',0,if(xD006_Flag_Forma_Calculo_PIS='L',T225_Valor_Total_Preco_Recebida-T225_Valor_Total_Custo,T225_Valor_Total_Preco_Recebida)),
			   -- T225_Aliquota_PIS=if(T225_Flag_Isento_PIS='S',0,T225_Aliquota_PIS),
			   T225_Valor_PIS_Recebida=if(T225_Aliquota_PIS>0,T225_Valor_Base_PIS_Recebida*(T225_Aliquota_PIS/100),0),
			   T225_Valor_Base_COFINS_Recebida=if(T225_Flag_Isento_COFINS='S',0,if(xD006_Flag_Forma_Calculo_COFINS='L',T225_Valor_Total_Preco_Recebida-T225_Valor_Total_Custo,T225_Valor_Total_Preco_Recebida)),
			   -- T225_Aliquota_COFINS=if(T225_Flag_Isento_COFINS='S',0,T225_Aliquota_COFINS),
			   T225_Valor_COFINS_Recebida=if(T225_Aliquota_COFINS>0,T225_Valor_Base_COFINS_Recebida*(T225_Aliquota_COFINS/100),0),
			   T225_Valor_ICMS_Credito_Recebida=if(xT134_Id<=0 or xT134_Id='' or ifnull(xT134_Id,0)<=0,T225_Valor_ICMS_Credito,If(T225_Aliquota_ICMS_Credito>0,(T225_Valor_Total_Custo_Final_Recebida+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T225_Valor_IPI_Recebida,0))-((T225_Valor_Total_Custo_Final_Recebida+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T225_Valor_IPI_Recebida,0))*if(xT134_Reducao_Base_ICMS>0,(xT134_Reducao_Base_ICMS/100),1)),0)*(T225_Aliquota_ICMS_Credito/100)),
			   T225_Total_Cubagem_Recebida = IFNULL(T225_Cubagem_Unitaria,0) * IFNULL(T014_Quantidade,0),
			   T225_Valor_Trib_Transparencia_Recebida=IFNULL(T225_Valor_Total_Preco_Recebida,0)*(IFNULL(T225_Percentual_Trib_Transparencia,0)/100),
			   T225_Valor_Total_Custo_Recebida=T225_Valor_Total_Preco_Recebida+T225_Valor_IPI_Recebida+T225_Valor_ICMS_Substituicao_Retencao_Recebida+T225_Valor_Frete_Unitario_Recebida,
  			   T225_Valor_Custo_Unitario_Recebida=T225_Valor_Total_Custo_Recebida/T014_Quantidade
		 WHERE T225_Id=xT225_Id;
		-- *_Recebida (para analise da OC) agora está na T014
		
		IF xT224_Flag_Calcular_ST = 'S' THEN

			UPDATE T014
	 	 LEFT JOIN T225 ON T225_Id=T014_T225_Id
		 LEFT JOIN T224 ON T224_Id=T225_T224_Id
		 LEFT JOIN D006 ON D006_Id=T014_D006_Id
			   SET 
			   	   -- campos calculados na parte de cima deste código, replicados aqui para valer para o T014
				   T014_Valor_Total_Desconto_Recebida = T225_Valor_Desconto_Unitario*T014_Quantidade,
				   T014_Valor_Total_Preco_Recebida = T225_Valor_Preco_Unitario*T014_Quantidade,
				   T014_Valor_Total_Preco_Sem_Desconto_Recebida = T225_Valor_Preco_Sem_Desconto_Unitario * T014_Quantidade,
				   T014_Valor_Total_Frete_Recebida=((T225_Valor_Frete+T225_Valor_Total_Frete)/T225_Quantidade)*T014_Quantidade,
				   T014_Valor_Total_Despesas_Acessorias_Recebida=T225_Valor_Despesas_Acessorias_Unitario*T014_Quantidade,
				   T014_Valor_Total_Desconto_Recebida=T225_Valor_Desconto_Unitario*T014_Quantidade,
				   -- fim campos calculados...
				   T014_Peso_Total_Recebida = T225_Peso_Unitario*T014_Quantidade,
				   T014_Peso_Bruto_Total_Recebida = T225_Peso_Bruto*T014_Quantidade,
				   T014_Valor_Total_Despesas_Recebida=(T014_Valor_Total_Preco_Recebida * xMultiplicador_Despesa_Recebida ) + xValor_Total_Agregados,
				   T014_Valor_Despesas_Unitario_Recebida=T014_Valor_Total_Despesas_Recebida/T014_Quantidade,
				   -- T225_Valor_Total_Agregados=xValor_Total_Agregados,
				   T014_Valor_Base_IPI_Recebida=If(T225_Aliquota_IPI>0,(T014_Valor_Total_Preco_Recebida+if(xD006_Flag_Somar_Despesas_Base_IPI='S',T014_Valor_Total_Frete_Recebida+T014_Valor_Total_Despesas_Acessorias_Recebida,0))*If(T225_Percentual_Reducao_IPI>0,(T225_Percentual_Reducao_IPI/100),1),0),
				   T014_Valor_IPI_Recebida=T014_Valor_Base_IPI_Recebida*(T225_Aliquota_IPI/100),
				   T014_Valor_Total_Custo_Final_Recebida=(T014_Valor_Total_Preco_Recebida+if(xD006_Flag_Somar_Despesas_Base_ST='S',T014_Valor_Total_Despesas_Recebida,0)+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T014_Valor_IPI_Recebida,0)),
				   T014_Valor_Custo_Final_Unitario_Recebida=T014_Valor_Total_Custo_Final_Recebida/T014_Quantidade,
				   T014_Valor_Base_ICMS_Recebida=If(T225_Aliquota_ICMS>0,T014_Valor_Total_Custo_Final_Recebida*if(T225_Percentual_Reducao_ICMS>0,((100-T225_Percentual_Reducao_ICMS)/100),1),0),
				   T014_Valor_ICMS_Recebida=T014_Valor_Base_ICMS_Recebida*(T225_Aliquota_ICMS/100),
				   -- T225_IPV=T225_Valor_Preco_Unitario/T225_Valor_Custo_Unitario,
				   T014_Valor_Base_ICMS_Substituicao_Recebida=IF(T225_Flag_ST='S',(T014_Valor_Total_Preco_Recebida+if(xD006_Flag_Somar_Despesas_Base_ST='S',T014_Valor_Total_Despesas_Recebida,0)+T014_Valor_IPI_Recebida)*((T225_ST_VA_Ajustada/100)+1),0),
				   T014_Valor_ICMS_Substituicao_Recebida=(T014_Valor_Base_ICMS_Substituicao_Recebida*(T225_ST_ICMS_Interno/100)),
				   T014_Valor_ICMS_Substituicao_Retencao_Recebida=IF(T225_Percentual_ST,(T014_Valor_Base_ICMS_Substituicao_Recebida)*(T225_Percentual_ST/100),((T014_Valor_Base_ICMS_Substituicao_Recebida*if(T225_Percentual_Reducao_ICMS_ST>0,((100-T225_Percentual_Reducao_ICMS_ST)/100),1))*(T225_ST_ICMS_Interno/100))-((T014_Valor_Total_Custo_Final_Recebida*if(T225_Percentual_Reducao_ICMS>0,((100-T225_Percentual_Reducao_ICMS)/100),1))*(T225_ICMS_Inter_Estadual/100))),
				   T014_Valor_ICMS_Substituicao_Retencao_Recebida=IF(T014_Valor_ICMS_Substituicao_Retencao_Recebida<0,0,T014_Valor_ICMS_Substituicao_Retencao_Recebida),
				   T014_Valor_Base_PIS_Recebida=if(T225_Flag_Isento_PIS='S',0,if(xD006_Flag_Forma_Calculo_PIS='L',T014_Valor_Total_Preco_Recebida-T225_Valor_Total_Custo,T014_Valor_Total_Preco_Recebida)),
				   -- T225_Aliquota_PIS=if(T225_Flag_Isento_PIS='S',0,T225_Aliquota_PIS),
				   T014_Valor_PIS_Recebida=if(T225_Aliquota_PIS>0,T014_Valor_Base_PIS_Recebida*(T225_Aliquota_PIS/100),0),
				   T014_Valor_Base_COFINS_Recebida=if(T225_Flag_Isento_COFINS='S',0,if(xD006_Flag_Forma_Calculo_COFINS='L',T014_Valor_Total_Preco_Recebida-T225_Valor_Total_Custo,T014_Valor_Total_Preco_Recebida)),
				   -- T225_Aliquota_COFINS=if(T225_Flag_Isento_COFINS='S',0,T225_Aliquota_COFINS),
				   T014_Valor_COFINS_Recebida=if(T225_Aliquota_COFINS>0,T014_Valor_Base_COFINS_Recebida*(T225_Aliquota_COFINS/100),0),
				   T014_Valor_ICMS_Credito_Recebida=if(xT134_Id<=0 or xT134_Id='' or ifnull(xT134_Id,0)<=0,T225_Valor_ICMS_Credito,If(T225_Aliquota_ICMS_Credito>0,(T014_Valor_Total_Custo_Final_Recebida+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T014_Valor_IPI_Recebida,0))-((T014_Valor_Total_Custo_Final_Recebida+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T014_Valor_IPI_Recebida,0))*if(xT134_Reducao_Base_ICMS>0,(xT134_Reducao_Base_ICMS/100),1)),0)*(T225_Aliquota_ICMS_Credito/100)),
				   T014_Total_Cubagem_Recebida = IFNULL(T225_Cubagem_Unitaria,0) * IFNULL(T014_Quantidade,0),
				   T014_Valor_Trib_Transparencia_Recebida=IFNULL(T014_Valor_Total_Preco_Recebida,0)*(IFNULL(T225_Percentual_Trib_Transparencia,0)/100),
				   T014_Valor_Total_Custo_Recebida=T014_Valor_Total_Preco_Recebida+T014_Valor_IPI_Recebida+T014_Valor_ICMS_Substituicao_Retencao_Recebida+T014_Valor_Total_Frete_Recebida,
				   T014_Valor_Custo_Unitario_Recebida=T014_Valor_Total_Custo_Recebida/T014_Quantidade
			 WHERE T014_T225_Id=xT225_Id;

		ELSE

			UPDATE T014
		 LEFT JOIN T225 ON T225_Id=T014_T225_Id
		 LEFT JOIN T224 ON T224_Id=T225_T224_Id
		 LEFT JOIN D006 ON D006_Id=T014_D006_Id
			SET 
				-- campos calculados na parte de cima deste código, replicados aqui para valer para o T014
			    T014_Valor_Total_Desconto_Recebida = T225_Valor_Desconto_Unitario*T014_Quantidade,
			    T014_Valor_Total_Preco_Recebida = T225_Valor_Preco_Unitario*T014_Quantidade,
			    T014_Valor_Total_Preco_Sem_Desconto_Recebida = T225_Valor_Preco_Sem_Desconto_Unitario * T014_Quantidade,
			    T014_Valor_Total_Frete_Recebida=((T225_Valor_Frete+T225_Valor_Total_Frete)/T225_Quantidade)*T014_Quantidade,
			    T014_Valor_Total_Despesas_Acessorias_Recebida=T225_Valor_Despesas_Acessorias_Unitario*T014_Quantidade,
			    T014_Valor_Total_Desconto_Recebida=T225_Valor_Desconto_Unitario*T014_Quantidade,
			    -- fim campos calculados...
			    T014_Peso_Total_Recebida = T225_Peso_Unitario*T014_Quantidade,
			    T014_Peso_Bruto_Total_Recebida = T225_Peso_Bruto*T014_Quantidade,
			    T014_Valor_Total_Despesas_Recebida=(T014_Valor_Total_Preco_Recebida * xMultiplicador_Despesa_Recebida ) + xValor_Total_Agregados,
			    T014_Valor_Despesas_Unitario_Recebida=T014_Valor_Total_Despesas_Recebida/T014_Quantidade,
			    -- T225_Valor_Total_Agregados=xValor_Total_Agregados,
			    T014_Valor_Base_IPI_Recebida=If(T225_Aliquota_IPI>0,(T014_Valor_Total_Preco_Recebida+if(xD006_Flag_Somar_Despesas_Base_IPI='S',T014_Valor_Total_Frete_Recebida+T014_Valor_Total_Despesas_Acessorias_Recebida,0))*If(T225_Percentual_Reducao_IPI>0,(T225_Percentual_Reducao_IPI/100),1),0),
			    T014_Valor_IPI_Recebida=T014_Valor_Base_IPI_Recebida*(T225_Aliquota_IPI/100),
			    T014_Valor_Total_Custo_Final_Recebida=(T014_Valor_Total_Preco_Recebida+if(xD006_Flag_Somar_Despesas_Base_ST='S',T014_Valor_Total_Despesas_Recebida,0)+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T014_Valor_IPI_Recebida,0)),
			    T014_Valor_Custo_Final_Unitario_Recebida=T014_Valor_Total_Custo_Final_Recebida/T014_Quantidade,
			    T014_Valor_Base_ICMS_Recebida=If(T225_Aliquota_ICMS>0,T014_Valor_Total_Custo_Final_Recebida*if(T225_Percentual_Reducao_ICMS>0,((100-T225_Percentual_Reducao_ICMS)/100),1),0),
			    T014_Valor_ICMS_Recebida=T014_Valor_Base_ICMS_Recebida*(T225_Aliquota_ICMS/100),
			    -- T225_IPV=T225_Valor_Preco_Unitario/T225_Valor_Custo_Unitario,
			    T014_Valor_Base_ICMS_Substituicao_Recebida=T225_Valor_Base_ICMS_Substituicao,
			    T014_Valor_ICMS_Substituicao_Recebida=T225_Valor_ICMS_Substituicao_Retencao,
			    T014_Valor_ICMS_Substituicao_Retencao_Recebida=T225_Valor_ICMS_Substituicao_Retencao,
			    T014_Valor_Base_PIS_Recebida=if(T225_Flag_Isento_PIS='S',0,if(xD006_Flag_Forma_Calculo_PIS='L',T014_Valor_Total_Preco_Recebida-T225_Valor_Total_Custo,T014_Valor_Total_Preco_Recebida)),
			    -- T225_Aliquota_PIS=if(T225_Flag_Isento_PIS='S',0,T225_Aliquota_PIS),
			    T014_Valor_PIS_Recebida=if(T225_Aliquota_PIS>0,T014_Valor_Base_PIS_Recebida*(T225_Aliquota_PIS/100),0),
			    T014_Valor_Base_COFINS_Recebida=if(T225_Flag_Isento_COFINS='S',0,if(xD006_Flag_Forma_Calculo_COFINS='L',T014_Valor_Total_Preco_Recebida-T225_Valor_Total_Custo,T014_Valor_Total_Preco_Recebida)),
			    -- T225_Aliquota_COFINS=if(T225_Flag_Isento_COFINS='S',0,T225_Aliquota_COFINS),
			    T014_Valor_COFINS_Recebida=if(T225_Aliquota_COFINS>0,T014_Valor_Base_COFINS_Recebida*(T225_Aliquota_COFINS/100),0),
			    T014_Valor_ICMS_Credito_Recebida=if(xT134_Id<=0 or xT134_Id='' or ifnull(xT134_Id,0)<=0,T225_Valor_ICMS_Credito,If(T225_Aliquota_ICMS_Credito>0,(T014_Valor_Total_Custo_Final_Recebida+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T014_Valor_IPI_Recebida,0))-((T014_Valor_Total_Custo_Final_Recebida+if(xD006_Flag_Somar_IPI_Base_ICMS='S',T014_Valor_IPI_Recebida,0))*if(xT134_Reducao_Base_ICMS>0,(xT134_Reducao_Base_ICMS/100),1)),0)*(T225_Aliquota_ICMS_Credito/100)),
			    T014_Total_Cubagem_Recebida = IFNULL(T225_Cubagem_Unitaria,0) * IFNULL(T014_Quantidade,0),
			    T014_Valor_Trib_Transparencia_Recebida=IFNULL(T014_Valor_Total_Preco_Recebida,0)*(IFNULL(T225_Percentual_Trib_Transparencia,0)/100),
			    T014_Valor_Total_Custo_Recebida=T014_Valor_Total_Preco_Recebida+T014_Valor_IPI_Recebida+T014_Valor_ICMS_Substituicao_Retencao_Recebida+T014_Valor_Total_Frete_Recebida,
			    T014_Valor_Custo_Unitario_Recebida=T014_Valor_Total_Custo_Recebida/T014_Quantidade
		  WHERE T014_T225_Id=xT225_Id;
		  
		END IF;

	    UPDATE T225A
     LEFT JOIN T225 ON T225_Id=T225A_T225_Id
           SET T225A_Valor_Base_FCP_ST=IF(xD006_Flag_Calcula_FCP_ST = 'S', T225_Valor_Base_ICMS_Substituicao,0),
               T225A_Valor_FCP_ST=IF(T225A_Valor_Base_FCP_ST > 0, T225A_Valor_Base_FCP_ST*(T225A_Aliquota_FCP/100),0),
               T225A_Valor_FCP_ST=IF(T225A_Valor_FCP_ST < 0, 0,T225A_Valor_FCP_ST),
			   T225A_Valor_Base_FCP=if(xD006_Flag_Calcula_FCP = 'S', T225_Valor_Base_ICMS,0),
               T225A_Valor_FCP=T225A_Valor_Base_FCP*(T225A_Aliquota_FCP/100),
			   T225_Valor_ICMS_Substituicao_Retencao=T225_Valor_ICMS_Substituicao_Retencao - T225A_Valor_FCP_ST
         WHERE T225A_T225_Id=xT225_Id;

		CALL T224_Gravar_Totalizacao_3(xT224_Id);
END;
