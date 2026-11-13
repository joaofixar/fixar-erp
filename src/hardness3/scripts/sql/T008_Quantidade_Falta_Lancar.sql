CREATE FUNCTION `T008_Quantidade_Falta_Lancar`(xT008_Id INT(11)) RETURNS decimal(15,3)
BEGIN
     DECLARE xT014_Quantidade_Falta_Lancar DECIMAL(15,3);
     DECLARE xT008_Quantidade_Falta_Lancar DECIMAL(15,3);
     DECLARE xT008_Quantidade DECIMAL (15,6);
     DECLARE xQuantidade_Falta_Lancar DECIMAL(15,3);

     SELECT IFNULL(SUM(IF(T013_Flag_Cancelada!='S',T014_Quantidade,0)),0),
            IF(T008A_D184_Id > 0 ,T008A_Quantidade_Comercial,T008_Quantidade)
       INTO xT014_Quantidade_Falta_Lancar,
            xT008_Quantidade
       FROM T008
  LEFT JOIN T014 on T014_T008_Id=T008_Id
  LEFT JOIN T013 on T013_Id=T014_T013_Id
  LEFT JOIN T008A ON T008_Id = T008A_T008_Id
      WHERE T008_Id=xT008_Id;
   
     SELECT IFNULL(SUM(IF(T007_Flag_Cancelada!='S' AND T007_Flag_Inutilizado!='S',IF(T008A_D184_Id > 0 ,T008A_Quantidade_Comercial,T2.T008_Quantidade),0)),0)
       INTO xT008_Quantidade_Falta_Lancar
       FROM T008 as T1
  LEFT JOIN T008 as T2 on T2.T008_T008_Id=T1.T008_Id
  LEFT JOIN T007 on T007_Id=T2.T008_T007_Id
  LEFT JOIN T008A ON T2.T008_Id = T008A_T008_Id
      WHERE T1.T008_Id=xT008_Id;

     SET xQuantidade_Falta_Lancar = IFNULL(xT008_Quantidade,0) - ( IFNULL(xT008_Quantidade_Falta_Lancar,0) + IFNULL(xT014_Quantidade_Falta_Lancar,0) );

     IF xQuantidade_Falta_Lancar IS NULL or xQuantidade_Falta_Lancar<0 THEN
        SET xQuantidade_Falta_Lancar = 0;
     END IF;

     RETURN xQuantidade_Falta_Lancar;
END;