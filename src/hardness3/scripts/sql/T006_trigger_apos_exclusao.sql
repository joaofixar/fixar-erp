CREATE TRIGGER `T006_trigger_apos_exclusao` AFTER DELETE ON `T006` FOR EACH ROW BEGIN   
  #Marcelo - Não atualizar D009 pois outros processo já atualizam o estoque do produto na exclusão
  #UPDATE D009 SET D009_Quantidade_Estoque_Liquido=0 where D009_Id=OLD.T006_D009_Id;
END;
