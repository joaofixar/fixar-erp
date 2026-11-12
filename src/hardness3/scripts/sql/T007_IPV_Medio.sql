CREATE FUNCTION `T007_IPV_Medio`(xT007_Id INT(11),xTipo INT(1)) RETURNS DECIMAL(10,4)
BEGIN
    DECLARE xTotal_IPV DECIMAL(10,4);
    DECLARE xFalta_IPV INT(3);
    SET xTotal_IPV=0;
    SET xFalta_IPV=0;
    SELECT IFNULL(SUM(1),0)
           INTO xFalta_IPV
           FROM T008
           WHERE T008_T007_Id=xT007_Id
           AND (T008_Valor_Total_Preco<=0
           OR T008_Valor_Total_Custo<=0);
    IF xFalta_IPV<=0 THEN
        IF xTipo=2 THEN
             SELECT (SUM(T008_Valor_Total_Preco)-SUM(T008_Valor_ICMS))/SUM(T008_Valor_Total_Custo)
                    INTO xTotal_IPV
                    FROM T008
                    WHERE T008_T007_Id=xT007_Id
                    GROUP BY T008_T007_Id;
        END IF;
        IF xTipo = 1 THEN
             SELECT SUM(T008_Valor_Total_Preco)/SUM(T008_Valor_Total_Custo)
                    INTO xTotal_IPV
                    FROM T008
                    WHERE T008_T007_Id=xT007_Id
                    GROUP BY T008_T007_Id;
        END IF;
        IF xTipo = 3 THEN
             SELECT SUM(T008_Valor_Total_Preco)/SUM(T008_Valor_Custo_Compra_Unitario*T008_Quantidade)
                    INTO xTotal_IPV
                    FROM T008
                    WHERE T008_T007_Id=xT007_Id
                    GROUP BY T008_T007_Id;
        END IF;
    END IF;
    RETURN xTotal_IPV;
END;

