CREATE TRIGGER `T006_trigger_before_update` BEFORE UPDATE ON `T006` FOR EACH ROW BEGIN   
  IF OLD.T006_Quantidade != NEW.T006_Quantidade THEN
  	SET NEW.T006_Flag_Conf_Cega = 'N';
  END IF;
END;