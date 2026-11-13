CREATE PROCEDURE `T055_Gravar_Saldo_Disponivel`(xT055_Id int(11), XFlag_Acao char(1))
BEGIN
    DECLARE xT296_Id INT(10);
    DECLARE xQuantidade_Saldo INT(10);

    SELECT T055_T296_Id 
      INTO xT296_Id
      FROM T055
    WHERE T055_Id = xT055_Id;
    
    IF XFlag_Acao = 'D' THEN
      SELECT COALESCE(SUM(T055_Quantidade), 0)
        INTO xQuantidade_Saldo
        FROM T055
      WHERE T055_T296_Id = xT296_Id 
        AND T055_Id <> xT055_Id;
    ELSE
      SELECT COALESCE(SUM(T055_Quantidade) , 0)
        INTO xQuantidade_Saldo
        FROM T055
      WHERE T055_T296_Id = xT296_Id;
    END IF;

    UPDATE T296 
    SET T296_Quantidade_Saldo = IF ((T296_Quantidade -  xQuantidade_Saldo) > 0, (T296_Quantidade -  xQuantidade_Saldo), 0)
    WHERE T296_Id = xT296_Id;
END;
