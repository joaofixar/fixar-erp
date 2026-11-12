CREATE FUNCTION `D009_Quantidade_Montagem`(xD009_Id INT(11)) RETURNS INT(11)

BEGIN

     DECLARE xQuantidade_Montagem DECIMAL(10,2);
     DECLARE xD049_Id INT(11);
     DECLARE xC004_Id INT(11);

     SELECT D009_D049_Id,
            D009_C004_Id
       INTO xD049_Id,
            xC004_Id
       FROM D009
      WHERE D009_Id=xD009_Id LIMIT 1;

     SELECT FLOOR(D009_Quantidade_Estoque_Real/D042_Quantidade) Quantidade_Minima
       INTO xQuantidade_Montagem
       FROM D042
  LEFT JOIN D009 ON D009_D049_Id=D042_D049_Id_Itens AND D009_C004_Id=xC004_Id
      WHERE D042_D049_Id=xD049_Id
        AND D042_C004_Id=xC004_Id
   ORDER BY Quantidade_Minima
      LIMIT 1;

  IF xQuantidade_Montagem IS NULL OR xQuantidade_Montagem<0 THEN
     SET xQuantidade_Montagem=0;
  END IF;

  RETURN xQuantidade_Montagem;
  
END;