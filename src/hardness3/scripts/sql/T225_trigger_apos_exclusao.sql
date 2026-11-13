CREATE TRIGGER `T225_trigger_apos_exclusao` AFTER DELETE ON `T225` FOR EACH ROW BEGIN   
  UPDATE D009 SET D009_Quantidade_Estoque_Liquido=0, D009_Quantidade_OC=D009_Quantidade_OC_3(D009_Id, 0) where D009_Id=OLD.T225_D009_Id;
END;
