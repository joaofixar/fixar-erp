CREATE TRIGGER `T269_trigger_antes_alteracao` BEFORE UPDATE ON `T269` FOR EACH ROW BEGIN
  SET NEW.T269_Preco_Antigo=OLD.T269_Preco;
  SET NEW.T269_Preco_Total=NEW.T269_Quantidade * NEW.T269_Preco;
END;
