CREATE TRIGGER `T295_trigger_apos_alteracao` AFTER UPDATE ON `T295` FOR EACH ROW BEGIN   
  #ja excuta em outro processo ao atualizar o T295
  #UPDATE D009 SET D009_Quantidade_OC=D009_Quantidade_OC_3(D009_Id, 0) where D009_Id IN (SELECT T296_D009_Id FROM T296 WHERE T296_T295_Id=NEW.T295_Id);
END;
