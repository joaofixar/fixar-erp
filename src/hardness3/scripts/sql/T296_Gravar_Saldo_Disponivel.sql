CREATE PROCEDURE `T296_Gravar_Saldo_Disponivel`(xT296_Id int(11), XFlag_Acao char(1))
BEGIN
    DECLARE xT294_Id INT(10);
    DECLARE xQuantidade_Saldo INT(10);
    DECLARE xQuantidade_Restante INT(10);

    SELECT T296_T294_Id 
      INTO xT294_Id
      FROM T296
    WHERE T296_Id = xT296_Id;
    
    IF XFlag_Acao = 'D' THEN
      SELECT COALESCE(SUM(T296_Quantidade), 0)
        INTO xQuantidade_Saldo
        FROM T296
      WHERE T296_T294_Id = xT294_Id 
        AND T296_Id <> xT296_Id;
    ELSEIF XFlag_Acao = 'U' THEN
      SELECT COALESCE(SUM(T055_Quantidade) , 0)
        INTO xQuantidade_Saldo
        FROM T055
      WHERE T055_T296_Id = xT296_Id;

      UPDATE T296 
      SET T296_Quantidade_Saldo = IF ((T296_Quantidade -  xQuantidade_Saldo) > 0, (T296_Quantidade -  xQuantidade_Saldo), 0)
      WHERE T296_Id = xT296_Id;

      SELECT COALESCE(SUM(T296_Quantidade) , 0)
        INTO xQuantidade_Saldo
        FROM T296
      WHERE T296_T294_Id = xT294_Id;
    ELSE
      SELECT COALESCE(SUM(T296_Quantidade) , 0)
        INTO xQuantidade_Saldo
        FROM T296
      WHERE T296_T294_Id = xT294_Id;
    END IF;

    UPDATE T294 
    SET T294_Quantidade_Saldo = IF ((T294_Quantidade -  xQuantidade_Saldo) > 0, (T294_Quantidade -  xQuantidade_Saldo), 0)
    WHERE T294_Id = xT294_Id;
END;
