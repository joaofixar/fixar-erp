CREATE FUNCTION `D009_Quantidade_OC`(xD009_Id INT(11)) RETURNS DECIMAL(10,3)
BEGIN
   DECLARE xQuantidade DECIMAL(10,3);
   DECLARE xNacImp VARCHAR(1);
      SELECT D049_Flag_Nacional_Importado
        INTO xNacImp
        FROM D009
   LEFT JOIN D049 ON D049_Id=D009_D049_Id
       WHERE D009_Id=xD009_Id;
 
   IF xNacImp='I' THEN
        SELECT SUM(T053_Quantidade-IFNULL((SELECT IFNULL(SUM(T055_Quantidade),0) FROM T055 LEFT JOIN T054 ON T054_Id=T055_T054_Id WHERE T055_T053_Id=T053_Id AND T054_Data_Transferencia_Estoque!='0000-00-00 00:00:00'),0))
          INTO xQuantidade 
          FROM T053 
     LEFT JOIN T052 ON T052_Id=T053_T052_Id
         WHERE T052_Data_Emissao>=DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH)
           AND T053_D009_Id=xD009_Id
           AND T053_Flag_Cancelada!='S'
           AND T053_Flag_Cancelada!='S';
   ELSE 
        SELECT SUM(T012_Quantidade-IFNULL((SELECT IFNULL(SUM(T014_Quantidade),0) FROM T014 LEFT JOIN T013 ON T013_Id=T014_T013_Id WHERE T014_T012_Id=T012_Id AND T013_Flag_Travar_Nota='S' AND T013_Flag_Cancelada!='S'),0))
          INTO xQuantidade 
          FROM T012 
     LEFT JOIN T011 ON T011_Id=T012_T011_Id
         WHERE T011_Data_Emissao>=DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH)
           AND T012_D009_Id=xD009_Id
           AND T012_Flag_Cancelada!='S'
           AND T011_Flag_Cancelada!='S';
   END IF;
   IF xQuantidade IS NULL OR xQuantidade<0 THEN
      SET xQuantidade = 0;
   END IF;
   RETURN xQuantidade;
END;