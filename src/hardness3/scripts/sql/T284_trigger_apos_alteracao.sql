CREATE TRIGGER T284_trigger_apos_alteracao AFTER UPDATE ON `T284` FOR EACH ROW BEGIN
    call T276_Gravar_Totalizacao(OLD.T284_T276_Id);
END;