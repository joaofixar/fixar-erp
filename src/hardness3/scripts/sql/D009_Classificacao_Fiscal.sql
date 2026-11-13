CREATE FUNCTION `D009_Classificacao_Fiscal`(xD009_Id INT(11)) RETURNS VARCHAR(12)
BEGIN

    DECLARE xD005_Classificacao_Fiscal VARCHAR(12);

    SELECT D005_Classificacao_Fiscal
      INTO xD005_Classificacao_Fiscal
      FROM D009
 LEFT JOIN D049 ON D049_Id=D009_D049_Id
 LEFT JOIN D001 ON D001_Id=D049_D001_Id
 LEFT JOIN D005 ON D005_Id=D001_D005_Id
     WHERE D009_Id=xD009_Id
      LIMIT 1;

    IF xD005_Classificacao_Fiscal IS NULL OR xD005_Classificacao_Fiscal='' THEN
        SELECT D005_Classificacao_Fiscal
          INTO xD005_Classificacao_Fiscal
          FROM D009
     LEFT JOIN D049 ON D049_Id=D009_D049_Id
     LEFT JOIN D001 ON D001_Id=D049_D001_Id
     LEFT JOIN T025 ON T025_D015_Id=D001_D015_Id AND T025_D002_Id=D001_D002_Id AND T025_D024_Id=D049_D024_Id
     LEFT JOIN D005 ON D005_Id=T025_D005_Id
         WHERE D009_Id=xD009_Id
         LIMIT 1;
    END IF;
    
    IF xD005_Classificacao_Fiscal IS NULL OR xD005_Classificacao_Fiscal='' THEN
        SELECT D005_Classificacao_Fiscal
          INTO xD005_Classificacao_Fiscal
          FROM D009
     LEFT JOIN D049 ON D049_Id=D009_D049_Id
     LEFT JOIN D001 ON D001_Id=D049_D001_Id
     LEFT JOIN T025 ON T025_D015_Id=D001_D015_Id AND T025_D002_Id=D001_D002_Id AND (T025_D024_Id<=0 OR T025_D024_Id IS NULL)
     LEFT JOIN D005 ON D005_Id=T025_D005_Id
         WHERE D009_Id=xD009_Id
         LIMIT 1;
    END IF;

    RETURN xD005_Classificacao_Fiscal;
END;
