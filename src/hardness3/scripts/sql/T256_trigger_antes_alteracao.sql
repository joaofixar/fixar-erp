CREATE TRIGGER `T256_trigger_antes_alteracao` BEFORE UPDATE ON `T256` FOR EACH ROW BEGIN
  	SET NEW.T256_Quantidade_Entrada_Estoque = IFNULL((SELECT SUM(IFNULL(T001_Quantidade,0)) FROM T257 left join T001 on T001_T257_Id=T257_Id WHERE T257_T256_Id = NEW.T256_Id),0);
  	SET NEW.T256_Quantidade_Produzida = IFNULL((SELECT SUM(IFNULL(T257_Quantidade,0)) FROM T257 WHERE T257_T256_Id = NEW.T256_Id),0);
  	SET NEW.T256_Quantidade_Saldo = NEW.T256_Quantidade - NEW.T256_Quantidade_Produzida;
END;

