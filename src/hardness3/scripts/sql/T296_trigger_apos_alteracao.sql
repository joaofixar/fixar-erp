CREATE TRIGGER `T296_trigger_apos_alteracao` AFTER UPDATE ON `T296` FOR EACH ROW BEGIN   
	IF @disable_trigger IS NULL THEN
  		UPDATE D009 SET D009_Quantidade_OC=D009_Quantidade_OC_3(D009_Id, 0) where D009_Id=NEW.T296_D009_Id;
  	END IF;
END;