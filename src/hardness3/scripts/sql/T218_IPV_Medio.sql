CREATE FUNCTION `T218_IPV_Medio`(xT218_Id int(11),xTipo int(1)) RETURNS decimal(10,4)
BEGIN
    DECLARE xTotal_IPV DECIMAL(10,4);
    DECLARE xTotal_Itens INT(10);
    DECLARE xIPV_Medio DECIMAL(10,4);
    IF xTipo=1 THEN
       SELECT SUM(T219_Valor_Total_Preco)/SUM(T219_Valor_Total_Custo),
              SUM(1)
         INTO xTotal_IPV,
              xTotal_Itens
         FROM T219
    LEFT JOIN T218 on T218_Id=T219_T218_Id
    LEFT JOIN D009 on D009_Id=T219_D009_Id
        WHERE T219_T218_Id=xT218_Id
          AND T219_Quantidade>0
          AND T219_Flag_Cancelado!='S'
          AND ifnull(D009_Flag_Preco_Tabelado,'N')!='S'
          AND T219_Id NOT IN (SELECT T219_Id FROM T225 LEFT JOIN T219 ON T219_Id=T225_T219_Id WHERE T219_T218_Id=xT218_Id)
        GROUP BY T219_T218_Id;
    ELSEIF xTipo=2 THEN
       SELECT SUM(T219_Valor_Total_Preco_Confirmado)/SUM(T219_Valor_Total_Custo_Confirmado),
              SUM(1)
         INTO xTotal_IPV,
              xTotal_Itens
         FROM T219
    LEFT JOIN T218 on T218_Id=T219_T218_Id
    LEFT JOIN D009 on D009_Id=T219_D009_Id
        WHERE T219_T218_Id=xT218_Id
          AND T219_Quantidade_Confirmada>0
          AND T219_Flag_Cancelado!='S'
          AND ifnull(D009_Flag_Preco_Tabelado,'N')!='S'
          AND T219_Id NOT IN (SELECT T219_Id FROM T225 LEFT JOIN T219 ON T219_Id=T225_T219_Id WHERE T219_T218_Id=xT218_Id)
        GROUP BY T219_T218_Id;
    ELSEIF xTipo=3 THEN
       SELECT SUM(T219_Valor_Total_Preco)/SUM(T219_Valor_Total_Custo),
              SUM(1)
         INTO xTotal_IPV,
              xTotal_Itens
         FROM T219
    LEFT JOIN T218 on T218_Id=T219_T218_Id
    LEFT JOIN D009 on D009_Id=T219_D009_Id
        WHERE T219_T218_Id=xT218_Id
          AND T219_Quantidade>0
          AND T219_Flag_Cancelado!='S'
          AND ifnull(D009_Flag_Preco_Tabelado,'N')!='S'
        GROUP BY T219_T218_Id;
    ELSEIF xTipo=4 THEN
       SELECT SUM(T219_Valor_Total_Preco_Confirmado)/SUM(T219_Valor_Total_Custo_Confirmado),
              SUM(1)
         INTO xTotal_IPV,
              xTotal_Itens
         FROM T219
    LEFT JOIN T218 on T218_Id=T219_T218_Id
    LEFT JOIN D009 on D009_Id=T219_D009_Id
        WHERE T219_T218_Id=xT218_Id
          AND T219_Quantidade_Confirmada>0
          AND T219_Flag_Cancelado!='S'
          AND ifnull(D009_Flag_Preco_Tabelado,'N')!='S'
        GROUP BY T219_T218_Id;
    END IF;
    IF xTotal_Itens>0 THEN
       SET xIPV_Medio=xTotal_IPV;
    ELSE
       SET xIPV_Medio=0;
    END IF;
    RETURN xIPV_Medio;
END
