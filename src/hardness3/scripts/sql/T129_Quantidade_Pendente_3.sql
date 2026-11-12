CREATE FUNCTION T129_Quantidade_Pendente_3(xT129_Id INT(11)) RETURNS decimal(10,3)
BEGIN
     DECLARE xQuantidade_Pendente DECIMAL(10,3);
     SELECT IFNULL(T129_Quantidade,0)-IFNULL(SUM(T014_Quantidade),0)
       INTO xQuantidade_Pendente
       FROM T129
  LEFT JOIN T128 on T128_Id=T129_T128_Id
  LEFT JOIN T014 on T014_T129_Id=T129_Id
  LEFT JOIN T013 on T013_Id=T014_T013_Id
      WHERE T014_T129_Id=xT129_Id
        AND T013_Flag_Travar_Nota!='S'
        AND T013_Flag_Cancelada!='S'
        AND T129_Flag_Cancelada!='S'
        and T128_Flag_Cancelada!='S';
     IF xQuantidade_Pendente IS NULL or xQuantidade_Pendente<0 THEN
        SET xQuantidade_Pendente = 0;
     END IF;
     RETURN xQuantidade_Pendente;
END
