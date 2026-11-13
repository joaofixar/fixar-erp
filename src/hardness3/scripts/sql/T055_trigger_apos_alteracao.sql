CREATE TRIGGER `T055_trigger_apos_alteracao` AFTER UPDATE ON `T055` FOR EACH ROW BEGIN   
	IF @disable_trigger IS NULL THEN
        UPDATE D009 SET D009_Quantidade_OC=D009_Quantidade_OC_3(D009_Id, 0) where D009_Id=NEW.T055_D009_Id;
    END IF;
END;