CREATE FUNCTION `T206_Quantidade_Produto`(xD009_Id INT(11)) RETURNS DECIMAL(14,3)
BEGIN
     DECLARE xT206_Quantidade DECIMAL(14,3);
      SELECT SUM(T206_Quantidade)
        INTO xT206_Quantidade 
        FROM T206                        
       WHERE T206_D009_Id=xD009_Id;
   IF xT206_Quantidade IS NULL THEN
      SET xT206_Quantidade = 0;
   END IF;
   RETURN xT206_Quantidade;
END;