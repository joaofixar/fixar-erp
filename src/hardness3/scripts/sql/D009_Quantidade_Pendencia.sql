CREATE FUNCTION `D009_Quantidade_Pendencia`(xD009_Id INT(11)) RETURNS DECIMAL(14,3)
BEGIN
     DECLARE xT072_Quantidade DECIMAL(14,3);
      SELECT SUM(T072_Quantidade)
        INTO xT072_Quantidade 
        FROM T072 
       WHERE T072_D009_Id=xD009_Id
         AND T072_Flag_Atualizado!='S';
   IF xT072_Quantidade IS NULL THEN
      SET xT072_Quantidade = 0;
   END IF;
   RETURN xT072_Quantidade;
END;
