CREATE TRIGGER `T006_trigger_after_update` AFTER UPDATE ON `T006` FOR EACH ROW BEGIN   
    IF OLD.T006_Quantidade != NEW.T006_Quantidade THEN
        UPDATE D009 SET D009_Quantidade_Compra_Emergencia=0 WHERE D009_Id=NEW.T006_D009_Id;
    END IF;
END;