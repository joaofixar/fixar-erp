CREATE FUNCTION `D009_IPI`(xC004_Id INT(11), xD009_Id INT(11), xD024_Id INT(11), xD006_Id INT(11),xTipo INT(1),xDocumento INT(1)) RETURNS DECIMAL(5,2)
BEGIN
    DECLARE xAliquota_IPI DECIMAL(5,2);
    DECLARE xPercentual_Reducao_IPI DECIMAL(5,2);
    DECLARE xC004_Flag_Isento_IPI INT(1);
    DECLARE xD006_Flag_Isento_IPI VARCHAR(1);
    DECLARE xD024_Flag_Isento_IPI INT(1);
    DECLARE xD009_Aliquota_IPI DECIMAL(6,2);
    DECLARE xD009_Percentual_Reducao_IPI DECIMAL(6,2);
    DECLARE xD009_Flag_Isento_IPI INT(1);
    DECLARE xD005_Aliquota_IPI DECIMAL(5,2);
    DECLARE xD005_Percentual_Reducao_IPI DECIMAL(5,2);
    DECLARE xD005_Flag_Isento_IPI INT(1);
    DECLARE xD020_Flag_IPI VARCHAR(1);
    DECLARE xD049_Flag_Nacional_Importado VARCHAR(1);
    DECLARE xD001_Flag_Tipo VARCHAR(10);
    
    SELECT C004_Flag_Isento_IPI INTO xC004_Flag_Isento_IPI FROM C004 WHERE C004_Id=xC004_Id;
    SELECT D006_Flag_Isento_IPI INTO xD006_Flag_Isento_IPI FROM D006 WHERE D006_Id=xD006_Id;
    SELECT D020_Flag_IPI,
           D024_Flag_Isento_IPI
      INTO xD020_Flag_IPI,
           xD024_Flag_Isento_IPI
           FROM D024
           LEFT JOIN D020 ON D020_Id=D024_D020_Id
           WHERE D024_Id=xD024_Id;
    SELECT D005_Aliquota_IPI,
           D005_Percentual_Reducao_IPI,
           D005_Flag_Isento_IPI,
           D009_Aliquota_IPI,
           D009_Percentual_Reducao_IPI,
           D009_Flag_Isento_IPI,
           D049_Flag_Nacional_Importado,
           D001_Flag_Tipo
      INTO xD005_Aliquota_IPI,
           xD005_Percentual_Reducao_IPI,
           xD005_Flag_Isento_IPI,
           xD009_Aliquota_IPI,
           xD009_Percentual_Reducao_IPI,
           xD009_Flag_Isento_IPI,
           xD049_Flag_Nacional_Importado,
           xD001_Flag_Tipo
      FROM D009
      LEFT JOIN D049 ON D049_Id=D009_D049_Id
      LEFT JOIN D001 ON D001_Id=D049_D001_Id      
      LEFT JOIN D005 ON D005_Id=D009_D005_Id(D009_Id)
      WHERE D009_C004_Id=xC004_Id
      AND D009_Id=xD009_Id;
   IF xD009_Aliquota_IPI>0 THEN
      SET xAliquota_IPI=xD009_Aliquota_IPI;
      SET xPercentual_Reducao_IPI=xD009_Percentual_Reducao_IPI;
   ELSE
      SET xAliquota_IPI=xD005_Aliquota_IPI;
      SET xPercentual_Reducao_IPI=xD005_Percentual_Reducao_IPI;
   END IF;
   IF xDocumento=3 OR xC004_Flag_Isento_IPI=1 OR xD006_Flag_Isento_IPI='S' OR xD020_Flag_IPI='S' OR xD024_Flag_Isento_IPI=1 OR xD009_Flag_Isento_IPI=1 OR xD005_Flag_Isento_IPI=1 THEN
      SET xAliquota_IPI=0;
      SET xPercentual_Reducao_IPI=0;
   END IF;
   IF xD049_Flag_Nacional_Importado!='I' AND xD001_Flag_Tipo!='PRODUZIDO' THEN
      SET xAliquota_IPI=0;
      SET xPercentual_Reducao_IPI=0;
   END IF;
   IF xTipo=1 THEN
       RETURN xAliquota_IPI;
   ELSEIF xTipo=2 THEN
       RETURN xPercentual_Reducao_IPI;
   END IF;
END;