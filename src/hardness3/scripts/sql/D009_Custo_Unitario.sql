CREATE FUNCTION `D009_Custo_Unitario`(xD009_Id INT(11)) RETURNS DECIMAL(10,2)
BEGIN
  DECLARE xValor_Custo_Unitario DECIMAL(10,2);
  SELECT IF(D049_Flag_Nacional_Importado='I',D009_Valor_Custo_Unitario*D016_Dolar(''),D009_Valor_Custo_Unitario)
         INTO xValor_Custo_Unitario
    FROM D009
    LEFT JOIN D049 ON D009_D049_Id=D049_Id
   WHERE D009_Id=xD009_Id
   LIMIT 1;
  RETURN xValor_Custo_Unitario;
END;
