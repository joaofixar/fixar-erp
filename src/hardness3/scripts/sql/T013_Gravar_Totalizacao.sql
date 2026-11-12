CREATE PROCEDURE `T013_Gravar_Totalizacao`(xT013_Id int(11))
BEGIN
    DECLARE xT014_Valor_Total_Custo_Final DECIMAL(19,2);
    SELECT sum(T014_Valor_Total_Custo_Final)
      INTO xT014_Valor_Total_Custo_Final
      FROM T014
    WHERE T014_T013_Id=xT013_Id;
    IF xT014_Valor_Total_Custo_Final IS NULL THEN
       SET xT014_Valor_Total_Custo_Final = 0;
    END IF;
    UPDATE T013 SET T013_Valor_Total_Custo_Final = xT014_Valor_Total_Custo_Final
     WHERE T013_Id=xT013_Id and T013_Flag_Travar_Nota!='S' and T013_Flag_Calcular='S';
END
