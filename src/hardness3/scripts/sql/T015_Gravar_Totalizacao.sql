
CREATE PROCEDURE `T015_Gravar_Totalizacao`(xT015_Id INT(11))
BEGIN
     UPDATE T015
        SET T015_Valor_Comissao=T015_Valor_Base_Comissao*(T015_Percentual_Comissao/100),
            T015_Valor_Juros=IFNULL((SELECT SUM(T033_Valor) FROM T033 WHERE T033_T015_Id=T015_Id AND T033_Flag_Deb_Cred='C' GROUP BY T033_T015_Id),0),
            T015_Valor_Desconto=IFNULL((SELECT SUM(T033_Valor) FROM T033 WHERE T033_T015_Id=T015_Id AND T033_Flag_Deb_Cred='D' GROUP BY T033_T015_Id),0),
            T015_Valor_Total=IFNULL(T015_Valor_Duplicata,0)+IFNULL(T015_Valor_Juros,0)-IFNULL(T015_Valor_Desconto,0)
      WHERE T015_Id=xT015_Id;
END;

