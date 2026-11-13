CREATE FUNCTION `T224_IPV_Medio`(xT224_Id int(11),xTipo int(1)) RETURNS decimal(10,4)
BEGIN
    DECLARE xTotal_IPV DECIMAL(10,4);
    DECLARE xTotal_Itens INT(10);
    DECLARE xIPV_Medio DECIMAL(10,4);
    SELECT SUM(T225_Valor_Total_Preco)/sum(T225_Valor_Total_Custo),
           SUM(1)
      INTO xTotal_IPV,
           xTotal_Itens
           FROM T225
      LEFT JOIN T224 ON T224_Id=T225_T224_Id
           WHERE T225_T224_Id=xT224_Id
            GROUP BY T225_T224_Id;
    IF xTotal_Itens>0 THEN
       SET xIPV_Medio=xTotal_IPV;
    ELSE
       SET xIPV_Medio=0;
    END IF;
    RETURN xIPV_Medio;
END
