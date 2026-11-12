CREATE FUNCTION `T005_IPV_Medio`(xT005_Id INT(11),xTipo INT(1)) RETURNS DECIMAL(10,4)
BEGIN
    DECLARE xTotal_IPV DECIMAL(10,4);
    DECLARE xTotal_Itens INT(10);
    DECLARE xIPV_Medio DECIMAL(10,4);
    SELECT SUM(T006_Valor_Total_Preco)/SUM(T006_Valor_Total_Custo),
           SUM(1)
      INTO xTotal_IPV,
           xTotal_Itens
           FROM T006
      LEFT JOIN T005 ON T005_Id=T006_T005_Id
           WHERE T006_T005_Id=xT005_Id
            GROUP BY T006_T005_Id;
    IF xTotal_Itens>0 THEN
       SET xIPV_Medio=xTotal_IPV;
    ELSE
       SET xIPV_Medio=0;
    END IF;
    RETURN xIPV_Medio;
END;