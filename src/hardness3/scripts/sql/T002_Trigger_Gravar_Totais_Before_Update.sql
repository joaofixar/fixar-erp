CREATE TRIGGER `T002_Trigger_Gravar_Totais_Before_Update` BEFORE UPDATE ON `T002` FOR EACH ROW BEGIN
	SET NEW.T002_Valor_Total=ifnull(NEW.T002_Valor_Duplicata,0)+ifnull(NEW.T002_Valor_Juros,0)-ifnull(NEW.T002_Valor_Desconto,0)-ifnull(NEW.T002_Valor_Taxas,0),
		NEW.T002_Valor_Saldo=ifnull(NEW.T002_Valor_Duplicata,0)+ifnull(NEW.T002_Valor_Juros,0)-ifnull(NEW.T002_Valor_Desconto,0)-ifnull(NEW.T002_Valor_Taxas,0)-ifnull(NEW.T002_Valor_Recebido,0);
END;
