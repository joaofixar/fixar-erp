CREATE PROCEDURE `D024_Ativar_Cliente`(xT003_Id int(11),xT005_Id int(11),xT007_Id int(11), xD024_Id int(11), xC007_Id int(11), xD021_Id int(11),xempresaAtual int(11))
BEGIN
    DECLARE xcarteiraClienteDiasMaxContato INT(10) DEFAULT 0;
    DECLARE xcarteiraClienteDiasMaxOrcamento INT(10) DEFAULT 0; 
    DECLARE xcarteiraClienteDiasMaxPedido INT(10) DEFAULT 0; 
    DECLARE xcarteiraClienteDiasMaxNotaFiscal INT(10) DEFAULT 0; 
    DECLARE xC004_Id INT(10) DEFAULT 0;
    DECLARE xativarCliente VARCHAR(1) DEFAULT 'N';

    /* IF(xT003_Id > 0) THEN
        SELECT T003_C004_Id
          INTO xC004_Id
          FROM T003
         WHERE T003_Id = xT003_Id;

    ELSEIF(xT005_Id > 0) THEN
        SELECT T005_C004_Id
          INTO xC004_Id
          FROM T005
         WHERE T005_Id = xT005_Id;

    ELSEIF(xT007_Id > 0) THEN
        SELECT T007_C004_Id
          INTO xC004_Id
          FROM T007
         WHERE T007_Id = xT007_Id;

    ELSEIF(xC004_Id <= 0) THEN
        SET xC004_Id = xempresaAtual;
    END IF; */

    /*NO MOMENTO VAMOS USAR A EMPRESA ATUAL = 1*/
    SET xC004_Id = 1;
    
    SELECT C031_Valor INTO xcarteiraClienteDiasMaxContato FROM C031 WHERE C031_Campo = 'carteiraClienteDiasMaxContato' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xcarteiraClienteDiasMaxOrcamento FROM C031 WHERE C031_Campo = 'carteiraClienteDiasMaxOrcamento' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xcarteiraClienteDiasMaxPedido FROM C031 WHERE C031_Campo = 'carteiraClienteDiasMaxPedido' AND C031_C004_Id = xC004_Id;
    SELECT C031_Valor INTO xcarteiraClienteDiasMaxNotaFiscal FROM C031 WHERE C031_Campo = 'carteiraClienteDiasMaxNotaFiscal' AND C031_C004_Id = xC004_Id;

    IF(xT003_Id > 0 AND xcarteiraClienteDiasMaxOrcamento > 0) THEN
        SET xativarCliente = 'S';
    ELSEIF (xT005_Id > 0 AND xcarteiraClienteDiasMaxPedido > 0) THEN
        SET xativarCliente = 'S';
    ELSEIF (xT007_Id > 0 AND xcarteiraClienteDiasMaxNotaFiscal > 0) THEN
        SET xativarCliente = 'S';
    ELSEIF (xT003_Id <= 0 AND xT005_Id <= 0 AND xT007_Id <= 0 AND xcarteiraClienteDiasMaxContato > 0) THEN
        SET xativarCliente = 'S'; 
    END IF;    

    IF (xativarCliente = 'S') THEN

        UPDATE D024 SET D024_Flag_Comercial        = 'A', 
                        D024_C007_Vendedor_Interno = xC007_Id, 
                        D024_Classe                = '', 
                        D024_D021_Id               = xD021_Id, 
                        D024_Data_Ativo            = CURDATE() 
                  WHERE D024_Id= xD024_Id 
                    AND D024_Flag_Comercial='I'; 
                      
    END IF;
END;
