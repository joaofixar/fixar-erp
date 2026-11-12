CREATE FUNCTION `D009_Quantidade_OP`(xD009_Id int(10)) RETURNS varchar(10) CHARSET utf8
BEGIN

     DECLARE xQuantidade_Pendente DECIMAL(10,3);

     SELECT SUM(T256_Quantidade)-SUM(IFNULL((SELECT SUM(IFNULL(IF(T001_Id IS NOT NULL,T257_Quantidade,0),0)) FROM T257 LEFT JOIN T001 ON T257_Id=T001_T257_Id WHERE T257_T256_Id=T256_Id),0))
       INTO xQuantidade_Pendente
       FROM T256
  LEFT JOIN T255 on T255_Id=T256_T255_Id
      WHERE T256_D009_Id=xD009_Id
        AND T255_Flag_Status NOT IN ('99', '98');

      IF xQuantidade_Pendente IS NULL or xQuantidade_Pendente<0 THEN
        SET xQuantidade_Pendente = 0;
      END IF;

      RETURN xQuantidade_Pendente;
END;
