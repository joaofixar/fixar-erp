CREATE TRIGGER `T256_trigger_antes_exclusao` BEFORE DELETE ON `T256` FOR EACH ROW BEGIN
	DECLARE xT255_Id int(10) default 0;
	DECLARE xTotal_Itens int(10) default 0;
	DECLARE xT256_Quantidade decimal(14,3);
	DECLARE xT256_Quantidade_Produzida decimal(14,3);
	DECLARE xT256_Quantidade_Saldo decimal(14,3);
	DECLARE xT256_Quantidade_Entrada_Estoque decimal(10,3);

	SELECT T256_T255_Id INTO xT255_Id FROM T256 WHERE T256_Id=OLD.T256_Id;

  	SELECT 
	  	SUM(T256_Quantidade), 
	  	SUM(T256_Quantidade_Produzida), 
	  	SUM(T256_Quantidade_Saldo), 
	  	SUM(T256_Quantidade_Entrada_Estoque),
	  	COUNT(DISTINCT T256_Id)
  	INTO
	  	xT256_Quantidade,
		xT256_Quantidade_Produzida,
		xT256_Quantidade_Saldo,
		xT256_Quantidade_Entrada_Estoque,
		xTotal_Itens 
	FROM T256 
	WHERE T256_T255_Id = xT255_Id;

	UPDATE T255 SET T255_Quantidade = xT256_Quantidade-OLD.T256_Quantidade, T255_Quantidade_Produzida = xT256_Quantidade_Produzida-OLD.T256_Quantidade_Produzida, T255_Quantidade_Saldo = xT256_Quantidade_Saldo-OLD.T256_Quantidade_Saldo, T255_Quantidade_Entrada_Estoque = xT256_Quantidade_Entrada_Estoque-OLD.T256_Quantidade_Entrada_Estoque, T255_Total_Itens = xTotal_Itens-1 WHERE T255_Id = xT255_Id;
END;
