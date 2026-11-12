CREATE FUNCTION `TRIB_Base_ST`(xFlag_ST varchar(1),xValor_Total_Preco decimal(20,2),xValor_Total_Despesas decimal(20,5),xValor_IPI decimal(14,2),xST_VA decimal(10,2),xUF_Origem varchar(2),xUF_Destino varchar(2),xDestino varchar(1),xCalculo_ST_Por_Dentro varchar(1), xST_ICMS_Interno decimal(5,2), xValor_ICMS decimal(20,2), xST_ICMS_IE decimal(5,2),xPreco_Pauta_ST decimal(20,10), xModalidade varchar(1)) RETURNS decimal(20,2)
BEGIN
	IF (xModalidade = 5) THEN
			
			IF (xFlag_ST='S') THEN
			
				RETURN (xPreco_Pauta_ST+xValor_Total_Despesas+xValor_IPI);

			END IF;

	ELSE 

		IF(xDestino='3' AND xCalculo_ST_Por_Dentro='S') THEN
			
			RETURN IF(xFlag_ST='S',(((xValor_Total_Preco+xValor_Total_Despesas+xValor_IPI)*((xST_VA/100)+1) - IF(xValor_ICMS > 0, xValor_ICMS, xValor_Total_Preco*(xST_ICMS_IE/100))) / ((xST_ICMS_Interno / 100-1) * -1)),0);
		ELSE

			RETURN IF(xFlag_ST='S',(xValor_Total_Preco+xValor_Total_Despesas+xValor_IPI)*((xST_VA/100)+1),0);

		END IF;

	END IF;

END
