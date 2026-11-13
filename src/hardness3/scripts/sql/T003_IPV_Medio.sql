CREATE FUNCTION `T003_IPV_Medio`(xT003_Id INT(11),xTipo INT(1)) RETURNS DECIMAL(10,4)
BEGIN
    DECLARE xTotal_IPV DECIMAL(10,4);
    DECLARE xTotal_Itens INT(10);
    DECLARE xIPV_Medio DECIMAL(10,4);
    IF xTipo=1 THEN
       SELECT SUM(T004_Valor_Total_Preco)/SUM(T004_Valor_Total_Custo),
              SUM(1)
         INTO xTotal_IPV,
              xTotal_Itens
         FROM T004
    LEFT JOIN T003 ON T003_Id=T004_T003_Id
    LEFT JOIN D009 ON D009_Id=T004_D009_Id
        WHERE T004_T003_Id=xT003_Id
          AND T004_Quantidade>0
          AND T004_Flag_Cancelado!='S'
          AND IFNULL(D009_Flag_Preco_Tabelado,'N')!='S'
          AND T004_Id NOT IN (SELECT T004_Id FROM T006 LEFT JOIN T004 ON T004_Id=T006_T004_Id WHERE T004_T003_Id=xT003_Id)
        GROUP BY T004_T003_Id;
    ELSEIF xTipo=2 THEN
       SELECT SUM(T004_Valor_Total_Preco_Confirmado)/SUM(T004_Valor_Total_Custo_Confirmado),
              SUM(1)
         INTO xTotal_IPV,
              xTotal_Itens
         FROM T004
    LEFT JOIN T003 ON T003_Id=T004_T003_Id
    LEFT JOIN D009 ON D009_Id=T004_D009_Id
        WHERE T004_T003_Id=xT003_Id
          AND T004_Quantidade_Confirmada>0
          AND T004_Flag_Cancelado!='S'
          AND IFNULL(D009_Flag_Preco_Tabelado,'N')!='S'
          AND T004_Id NOT IN (SELECT T004_Id FROM T006 LEFT JOIN T004 ON T004_Id=T006_T004_Id WHERE T004_T003_Id=xT003_Id)
        GROUP BY T004_T003_Id;
    ELSEIF xTipo=3 THEN
       SELECT SUM(T004_Valor_Total_Preco)/SUM(T004_Valor_Total_Custo),
              SUM(1)
         INTO xTotal_IPV,
              xTotal_Itens
         FROM T004
    LEFT JOIN T003 ON T003_Id=T004_T003_Id
    LEFT JOIN D009 ON D009_Id=T004_D009_Id
        WHERE T004_T003_Id=xT003_Id
          AND T004_Quantidade>0
          AND T004_Flag_Cancelado!='S'
          AND IFNULL(D009_Flag_Preco_Tabelado,'N')!='S'
        GROUP BY T004_T003_Id;
    ELSEIF xTipo=4 THEN
       SELECT SUM(T004_Valor_Total_Preco_Confirmado)/SUM(T004_Valor_Total_Custo_Confirmado),
              SUM(1)
         INTO xTotal_IPV,
              xTotal_Itens
         FROM T004
    LEFT JOIN T003 ON T003_Id=T004_T003_Id
    LEFT JOIN D009 ON D009_Id=T004_D009_Id
        WHERE T004_T003_Id=xT003_Id
          AND T004_Quantidade_Confirmada>0
          AND T004_Flag_Cancelado!='S'
          AND IFNULL(D009_Flag_Preco_Tabelado,'N')!='S'
        GROUP BY T004_T003_Id;
    END IF;
    IF xTotal_Itens>0 THEN
       SET xIPV_Medio=xTotal_IPV;
    ELSE
       SET xIPV_Medio=0;
    END IF;
    RETURN xIPV_Medio;
END;
