CREATE FUNCTION `D009_Quantidade_Estoque_Tabela`(xD009_Id int(10)) RETURNS varchar(10) CHARSET utf8
BEGIN

     DECLARE xD069_Quantidade_Estoque_Fornecedor DECIMAL(10,3);

     SELECT SUM(IFNULL(D069_Quantidade_Estoque,0))
       INTO xD069_Quantidade_Estoque_Fornecedor
       FROM D069
      WHERE D069_D009_Id=xD009_Id;

      IF xD069_Quantidade_Estoque_Fornecedor IS NULL or xD069_Quantidade_Estoque_Fornecedor<0 THEN
        SET xD069_Quantidade_Estoque_Fornecedor = 0;
      END IF;

      RETURN xD069_Quantidade_Estoque_Fornecedor;
END;