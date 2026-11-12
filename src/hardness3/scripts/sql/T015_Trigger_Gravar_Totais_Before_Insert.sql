CREATE TRIGGER `T015_Trigger_Gravar_Totais_Before_Insert` BEFORE INSERT ON `T015` FOR EACH ROW BEGIN
  SET NEW.T015_Valor_Total=ifnull(NEW.T015_Valor_Duplicata,0)+ifnull(NEW.T015_Valor_Juros,0)-ifnull(NEW.T015_Valor_Desconto,0),
      NEW.T015_Valor_Saldo=ifnull(NEW.T015_Valor_Duplicata,0)+ifnull(NEW.T015_Valor_Juros,0)-ifnull(NEW.T015_Valor_Desconto,0)-ifnull(NEW.T015_Valor_Pago,0);
END;
