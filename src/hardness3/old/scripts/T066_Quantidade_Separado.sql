CREATE FUNCTION `T066_Quantidade_Separado`(xT066_Id INT(11)) RETURNS DECIMAL(20,5)
BEGIN
     DECLARE xT185_Qtd_Separado DECIMAL(20,5);
     
     SELECT SUM(T185_Qtd_Separado) INTO xT185_Qtd_Separado FROM T185 
			LEFT JOIN T006 ON T006_Id=T185_T006_Id
			LEFT JOIN T005 ON T005_Id=T006_T005_Id 
			WHERE 
				T185_T066_Id=xT066_Id
				AND T006_Flag_Status!='3'
				AND T005_Flag_Status!='4'
				AND T005_Flag_Status!='9'
				AND T005_Flag_Status!='5'
				AND T005_Flag_Status!='7'
				AND T005_Flag_Status!='11'
				AND T005_Flag_Status!='8';
   IF xT185_Qtd_Separado IS NULL THEN
     SET xT185_Qtd_Separado = 0;
   END IF;
   RETURN xT185_Qtd_Separado;
END;