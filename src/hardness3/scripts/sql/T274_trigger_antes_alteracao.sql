CREATE TRIGGER `T274_trigger_antes_alteracao` BEFORE UPDATE ON `T274` FOR EACH ROW BEGIN	
	SET NEW.T274_Total_Horas_Trabalhadas = TIMEDIFF(NEW.T274_Hora_Final,NEW.T274_Hora_Inicio); 
END;