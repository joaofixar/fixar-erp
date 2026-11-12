CREATE FUNCTION `D069_IPI_3`(xC004_Id int(11), xD069_Id int(11), xD024_Id int(11), xD006_Id int(11),xTipo INT(1),xDocumento INT(1)) RETURNS decimal(5,2)
BEGIN
    DECLARE xAliquota_IPI DECIMAL(5,2);
    DECLARE xPercentual_Reducao_IPI decimal(5,2);
    DECLARE xC004_Flag_Isento_IPI INT(1);
    DECLARE xD006_Flag_Isento_IPI VARCHAR(1);
    DECLARE xD024_Flag_Isento_IPI INT(1);
    DECLARE xD149_Aliquota_IPI DECIMAL(5,2);
    DECLARE xD149_Percentual_Reducao_IPI DECIMAL(5,2);
    DECLARE xD149_Flag_Isento_IPI INT(1);
    DECLARE xD020_Flag_IPI VARCHAR(1);
    DECLARE xD069_Flag_Origem_Produto VARCHAR(1);

    SELECT C004_Flag_Isento_IPI INTO xC004_Flag_Isento_IPI FROM C004 WHERE C004_Id=xC004_Id;
    SELECT D006_Flag_Isento_IPI INTO xD006_Flag_Isento_IPI FROM D006 WHERE D006_Id=xD006_Id;
    SELECT D020_Flag_IPI,
           D024_Flag_Isento_IPI
      INTO xD020_Flag_IPI,
           xD024_Flag_Isento_IPI
           FROM D024
           LEFT JOIN D020 on D020_Id=D024_D020_Id
           WHERE D024_Id=xD024_Id;
    SELECT D149_Aliquota_IPI,
           D149_Percentual_Reducao_IPI,
           D149_Flag_Isento_IPI,
           D069_Flag_Origem_Produto
      INTO xD149_Aliquota_IPI,
           xD149_Percentual_Reducao_IPI,
           xD149_Flag_Isento_IPI,
           xD069_Flag_Origem_Produto
      FROM D069
      LEFT JOIN D005 ON D005_Id=D069_D005_Id
      LEFT JOIN D149 ON D005_Id=D149_D005_Id AND D149_C004_Id=xC004_Id
      WHERE D069_Id=xD069_Id limit 1;

      SET xAliquota_IPI=xD149_Aliquota_IPI;
      SET xPercentual_Reducao_IPI=xD149_Percentual_Reducao_IPI;

      IF xDocumento=3 OR xC004_Flag_Isento_IPI=1 OR xD006_Flag_Isento_IPI='S' OR xD020_Flag_IPI='S' OR xD024_Flag_Isento_IPI=1 OR xD009_Flag_Isento_IPI=1 OR xD149_Flag_Isento_IPI=1 THEN
         SET xAliquota_IPI=0;
         SET xPercentual_Reducao_IPI=0;
      END IF;

      IF xTipo=1 THEN
          RETURN xAliquota_IPI;
      ELSEIF xTipo=2 THEN
          RETURN xPercentual_Reducao_IPI;
      END IF;
END;
