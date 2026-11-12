CREATE PROCEDURE `T284_Gravar_Totalizacao`(xT284_Id int(11))
BEGIN
    DECLARE xPeso_Total DECIMAL(20,2);
    DECLARE xValor_Total DECIMAL(20,2);
    SELECT  sum(T007_Peso_Bruto),
            sum(T007_Valor_Total)
      INTO xPeso_Total,
           xValor_Total
           FROM T007
      LEFT JOIN T284 ON T284_T007_Id=T007_Id
          WHERE T284_Id=xT284_Id;

    UPDATE T284 SET T284_Peso_Bruto_Total=xValor_Total_Custo,
                    T284_Valor_Carga=xValor_Total_Produtos
              WHERE T284_Id=xT284_Id;
END;
