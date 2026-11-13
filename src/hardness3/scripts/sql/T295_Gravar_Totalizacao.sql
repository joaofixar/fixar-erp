CREATE PROCEDURE `T295_Gravar_Totalizacao`(xT295_Id int(11))
BEGIN
    DECLARE xValor_Total_Custo DECIMAL(19,2);
    
    SELECT SUM(T296_Valor_Total_Custo)
      INTO xValor_Total_Custo
      FROM T296
     WHERE T296_T295_Id=xT295_Id;

    UPDATE T295 SET T295_Valor_Total=xValor_Total_Custo,
                    T295_Valor_VMLE=xValor_Total_Custo
              WHERE T295_Id=xT295_Id;
END;
