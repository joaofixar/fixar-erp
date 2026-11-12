CREATE FUNCTION `D009_Quantidade_Separado_3`(xD009_Id INT(11)) RETURNS decimal(12,3)
BEGIN
     DECLARE xT006_Quantidade decimal(12,3);
      SELECT SUM(T006_Quantidade_Separacao)
        INTO xT006_Quantidade
        FROM T006
   LEFT JOIN T005 ON T006_T005_Id=T005_Id
   LEFT JOIN T008 ON T008_T006_Id=T006_Id
       WHERE T006_D009_Id=xD009_Id
         AND T005_Flag_Status!='8'
         AND IFNULL(T005_T005_Id_Agrupado,0)=0
         AND T006_Flag_Status!='3'
         AND T008_Id is null
         AND T005_Flag_Status != '5';
   IF xT006_Quantidade IS NULL THEN
      SET xT006_Quantidade = 0;
   END IF;
   RETURN xT006_Quantidade;
END;