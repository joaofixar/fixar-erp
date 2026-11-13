CREATE TRIGGER `T274_trigger_antes_inclusao` BEFORE INSERT ON `T274` FOR EACH ROW BEGIN
	DECLARE xT274_Total_Horas_Trabalhadas time;
	SET NEW.T274_Total_Horas_Trabalhadas = TIMEDIFF(NEW.T274_Hora_Final,NEW.T274_Hora_Inicio); 
END;