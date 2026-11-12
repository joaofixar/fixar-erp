CREATE PROCEDURE `T294_Gravar_Saldo_Disponivel`(xT294_Id int(11))
BEGIN

    DECLARE xQuantidade_Saldo INT(10);

    SELECT COALESCE(SUM(T296_Quantidade), 0) 
      INTO xQuantidade_Saldo
      FROM T296
     WHERE T296_T294_Id = xT294_Id;
    
    UPDATE T294 
       SET T294_Quantidade_Saldo = IF ((T294_Quantidade -  xQuantidade_Saldo) > 0, (T294_Quantidade -  xQuantidade_Saldo), 0)
     WHERE T294_Id = xT294_Id;
END;