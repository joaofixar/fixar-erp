CREATE FUNCTION `D009_Estoque_Conjunto`(xD009_Id int(10)) RETURNS varchar(255) CHARSET utf8
BEGIN

   DECLARE xQuantidade_Estoque_Padrao DECIMAL(10,2) DEFAULT 0;
   DECLARE xQuantidade_Estoque_Origem DECIMAL(10,2) DEFAULT 0;
   DECLARE xD001_Id_Origem DECIMAL(10,2) DEFAULT 0;
   DECLARE xC004_Id DECIMAL(10,2) DEFAULT 0;
   DECLARE xRetorno VARCHAR(50) DEFAULT 0;
   DECLARE xD049_Flag_Tipo VARCHAR(2) DEFAULT 0;

    SELECT D009_Quantidade_Estoque_Liquido,
           D001_D001_Id,
           D009_C004_Id,
           D049_Flag_Tipo
        INTO 
           xQuantidade_Estoque_Padrao,
           xD001_Id_Origem,
           xC004_Id,
           xD049_Flag_Tipo
    FROM D009 
        LEFT JOIN D049 ON D049_Id=D009_D049_Id
        LEFT JOIN D001 ON D001_Id=D049_D001_Id
    WHERE D009_Id=xD009_Id;

    SELECT D009_Quantidade_Estoque_Liquido 
        INTO
            xQuantidade_Estoque_Origem
    FROM D001
        LEFT JOIN D049 ON D049_D001_Id=D001_Id 
        LEFT JOIN D009 ON D009_D049_Id=D049_Id
    WHERE D001_Id=xD001_Id_Origem AND D009_C004_Id=xC004_Id;

    IF xD049_Flag_Tipo = 'CP' THEN
        SET xRetorno=IFNULL(REPLACE(REPLACE(REPLACE(FORMAT(xQuantidade_Estoque_Padrao, 2), '.', '|'), ',', '.'), '|', ','), 0);
    ELSE 
        SET xRetorno=CONCAT(IFNULL(REPLACE(REPLACE(REPLACE(FORMAT(xQuantidade_Estoque_Padrao, 2), '.', '|'), ',', '.'), '|', ','),0), ' / ', IFNULL(REPLACE(REPLACE(REPLACE(FORMAT(xQuantidade_Estoque_Origem, 2), '.', '|'), ',', '.'), '|', ','),0));
    END IF;
    

    RETURN xRetorno;

END;

