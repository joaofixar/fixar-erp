CREATE FUNCTION T012_Quantidade_Pendente_3(xT012_Id INT(11)) RETURNS decimal(10,3)
BEGIN
     DECLARE xQuantidade_Pendente DECIMAL(10,3);
     SELECT IFNULL(T012_Quantidade,0)-IFNULL(SUM(T014_Quantidade),0)
       INTO xQuantidade_Pendente
       FROM T012
  LEFT JOIN T011 on T011_Id=T012_T011_Id
  LEFT JOIN T014 on T014_T012_Id=T012_Id
  LEFT JOIN T013 on T013_Id=T014_T013_Id
      WHERE T014_T012_Id=xT012_Id
        AND T013_Flag_Travar_Nota!='S'
        AND T013_Flag_Cancelada!='S'
        AND T012_Flag_Cancelada!='S'
        and T011_Flag_Cancelada!='S';
     IF xQuantidade_Pendente IS NULL or xQuantidade_Pendente<0 THEN
        SET xQuantidade_Pendente = 0;
     END IF;
     RETURN xQuantidade_Pendente;
END
