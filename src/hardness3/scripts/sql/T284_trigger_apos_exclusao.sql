CREATE TRIGGER T284_trigger_apos_exclusao 
AFTER DELETE 
ON `T284` FOR EACH ROW 
BEGIN
    call T276_Gravar_Totalizacao(OLD.T284_T276_Id);
END;