CREATE FUNCTION `D016_Dolar`(xData DATE) RETURNS DECIMAL(10,4)
BEGIN
    DECLARE xD016_Valor_Dolar DECIMAL(10,4);
    IF xData='0000-00-00'  THEN
          SELECT D016_Valor_Dolar INTO xD016_Valor_Dolar
            FROM D016
           WHERE D016_Valor_Dolar>0
        ORDER BY D016_Data DESC
           LIMIT 0,1;
    ELSE
          SELECT D016_Valor_Dolar INTO xD016_Valor_Dolar
            FROM D016
           WHERE D016_Valor_Dolar>0
             AND D016_Data<=xData
        ORDER BY D016_Data DESC
           LIMIT 0,1;
    END IF;
    IF xD016_Valor_Dolar IS NULL THEN
       SET xD016_Valor_Dolar = 0;
    END IF;
    RETURN xD016_Valor_Dolar;
END;
