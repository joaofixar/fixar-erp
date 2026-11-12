CREATE PROCEDURE `T293_Gravar_Totalizacao`(xT293_Id int(11))
BEGIN
    DECLARE xValor_Total_Custo DECIMAL(19,2);
    
    SELECT SUM(T294_Valor_Total_Custo)
      INTO xValor_Total_Custo
      FROM T294
     WHERE T294_T293_Id=xT293_Id;

    UPDATE T293 SET T293_Valor_VMLE=xValor_Total_Custo
              WHERE T293_Id=xT293_Id;
END;
