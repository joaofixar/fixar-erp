CREATE FUNCTION `T014_Quantidade_Falta_Lancar`(xT014_Id INT(11)) RETURNS DECIMAL(15,3)
BEGIN
     DECLARE xQuantidade_Falta_Lancar DECIMAL(15,3);      

     SELECT IFNULL(T014_Quantidade, 0) - IFNULL(SUM(IF(T007_Flag_Cancelada = 'S' OR T007_Flag_Inutilizado = 'S',0,T008_Quantidade)),0)
	  INTO xQuantidade_Falta_Lancar
	  FROM T014	
  LEFT JOIN T008 ON T008_T014_Id = T014_Id
  LEFT JOIN T007 ON T007_Id = T008_T007_Id
	 WHERE T014_Id = xT014_Id;
     
     IF xQuantidade_Falta_Lancar IS NULL OR xQuantidade_Falta_Lancar<0 THEN
        SET xQuantidade_Falta_Lancar = 0;
     END IF;
     
     RETURN xQuantidade_Falta_Lancar;
END;