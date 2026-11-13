CREATE FUNCTION T225_Quantidade_Pendente_Estoque(xT225_Id INT(11)) RETURNS decimal(10,3)
BEGIN
     DECLARE xQuantidade_Pendente DECIMAL(10,3);
     SELECT IFNULL(T225_Quantidade,0)-IFNULL(SUM(IF(T013_Flag_Estoque_Finalizado = 'S', IFNULL(T014_Quantidade,0), 0)),0)
       INTO xQuantidade_Pendente
       FROM T225
  LEFT JOIN T224 on T224_Id=T225_T224_Id
  LEFT JOIN T014 on T014_T225_Id=T225_Id
  LEFT JOIN T013 on T013_Id=T014_T013_Id
      WHERE T225_Id=xT225_Id
        AND ifnull(T013_Flag_Cancelada,'N')!='S'
        AND ifnull(T014_Flag_Cancelada,'N')!='S'
        AND T225_Flag_Cancelada!='S'
        and T224_Flag_Cancelada!='S'
        and T224_Flag_Status!=11;
     IF xQuantidade_Pendente IS NULL or xQuantidade_Pendente<0 THEN
        SET xQuantidade_Pendente = 0;
     END IF;
     RETURN xQuantidade_Pendente;
END
