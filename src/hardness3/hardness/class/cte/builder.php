<?php

namespace hardness;

class Builder {
    
    public $id; // Chave
    public $xml = '';
    public $CTe;
    private $ide;
    private $infCte;  
    private $toma3;
    private $toma4;
    private $emit;
    private $compl = '';
    private $rem;
    private $dest;
    private $receb;
    private $vPrest;
    private $impIBSCBS;
    private $impICMS;
    private $impvTotDFe;
    private $infCarga;
    private $infDoc;    
    private $infCTeNorm;
    private $docAnt;
    private $versaoModal = '3.00';    
    private $tpAmbiente;
    private $obsCont = array();
    private $obsFisco = array();
    private $comp = array();  
    private $infQ = array();    
    private $infNFe = array();    
    private $infOutros = array();
    private $emiDocAnt = array();
    private $idDocAntEle = array();    
    protected $infRespTec;
    private $infCTeSupl = '';
    private $Signature = '';

    //Erros
    protected $erros = array();    
    private $status = true;    
    

    public \DOMDocument $dom;

    public function __construct()
    {
        $this->dom = new \DOMDocument('1.0', 'UTF-8');
        $this->dom->preserveWhiteSpace = false;
        $this->dom->formatOutput = true;        
    }
  
    public function print() {     
        $xml =  $this->dom->saveXML();                
        return $this->limpaXML($xml);
    }

    public function taginfCte($values) {        
        $this->infCte = $this->dom->createElement('infCte');
        $this->infCte->setAttribute("Id", 'CTe' . $values->Id);
        $this->infCte->setAttribute("versao", $values->versao);
                
        $this->id = 'CTe' . $values->Id;        
        return $this->infCte;        
    }

    public function tagide($values) {

        $this->mod = $values->mod;
        $this->tpAmbiente = $values->tpAmb;
        $this->tpServ = $values->tpServ;

        $this->ide = $this->dom->createElement('ide');
        $this->appendChildElement($this->ide,'cUF',$values->cUF);
        $this->appendChildElement($this->ide,'cCT',$values->cCT);
        $this->appendChildElement($this->ide,'CFOP',$values->CFOP);
        $this->appendChildElement($this->ide,'natOp',$values->natOp);
        $this->appendChildElement($this->ide,'mod',$values->mod);
        $this->appendChildElement($this->ide,'serie',$values->serie);
        $this->appendChildElement($this->ide,'nCT',$values->nCT);
        $this->appendChildElement($this->ide,'dhEmi',$values->dhEmi);
        $this->appendChildElement($this->ide,'tpImp',$values->tpImp);
        $this->appendChildElement($this->ide,'tpEmis',$values->tpEmis);
        $this->appendChildElement($this->ide,'cDV',$values->cDV);
        $this->appendChildElement($this->ide,'tpAmb',$values->tpAmb);
        $this->appendChildElement($this->ide,'tpCTe',$values->tpCTe);
        $this->appendChildElement($this->ide,'procEmi',$values->procEmi);
        $this->appendChildElement($this->ide,'verProc', $values->verProc);
        $this->appendChildElement($this->ide,'cMunEnv', $values->cMunEnv);
        $this->appendChildElement($this->ide,'xMunEnv', $values->xMunEnv);
        $this->appendChildElement($this->ide,'UFEnv', $values->UFEnv);
        $this->appendChildElement($this->ide,'modal', $values->modal);
        $this->appendChildElement($this->ide,'tpServ', $values->tpServ);
        $this->appendChildElement($this->ide,'cMunIni', $values->cMunIni);
        $this->appendChildElement($this->ide,'xMunIni', $values->xMunIni);
        $this->appendChildElement($this->ide,'UFIni', $values->UFIni);
        $this->appendChildElement($this->ide,'cMunFim', $values->cMunFim);
        $this->appendChildElement($this->ide,'xMunFim', $values->xMunFim);
        $this->appendChildElement($this->ide,'UFFim', $values->UFFim);
        $this->appendChildElement($this->ide,'retira', $values->retira);
        $this->appendChildElement($this->ide,'xDetRetira', $values->xDetRetira,false);
        $this->appendChildElement($this->ide,'indIEToma', $values->indIEToma);
                 
        return $this->ide;
    }

    public function tagtoma3($values) {
        $this->toma3 = $this->dom->createElement('toma3');
        $this->appendChildElement($this->toma3, 'toma', $values->toma);        
        return $this->toma3;
    }

    public function tagtoma4($values) {
        $this->toma4 = $this->dom->createElement('toma4');
        $this->appendChildElement($this->toma4, 'toma', $values->toma);

        if(!empty($values->CNPJ)) {
            $this->appendChildElement($this->toma4, 'CNPJ', $values->CNPJ);
        } else if(!empty($values->CPF)) {
            $this->appendChildElement($this->toma4, 'CPF', $values->CPF);
        }
        $this->appendChildElement($this->toma4, 'IE', $values->IE);
        $this->appendChildElement($this->toma4, 'xNome', $values->xNome);
        if(!empty($values->xFant)) {
            $this->appendChildElement($this->toma4, 'xFant', $values->xFant,false);
        }
        $this->appendChildElement($this->toma4, 'fone', $values->fone,false);
        $this->appendChildElement($this->toma4, 'email', $values->email,false);
        

        $enderToma = $this->dom->createElement('enderToma');
        $this->appendChildElement($enderToma,'xLgr', $values->enderToma->xLgr);
        $this->appendChildElement($enderToma,'nro', $values->enderToma->nro);
        $this->appendChildElement($enderToma,'xCpl', $values->enderToma->xCpl,false);
        $this->appendChildElement($enderToma,'xBairro', $values->enderToma->xBairro);
        $this->appendChildElement($enderToma,'cMun', $values->enderToma->cMun);
        $this->appendChildElement($enderToma,'xMun', $values->enderToma->xMun);
        $this->appendChildElement($enderToma,'CEP', $values->enderToma->CEP,false);
        $this->appendChildElement($enderToma,'UF', $values->enderToma->UF);
        $this->appendChildElement($enderToma,'cPais', $values->enderToma->cPais,false);
        $this->appendChildElement($enderToma,'xPais', $values->enderToma->xPais,false);

        $this->toma4->appendChild($enderToma);                    
        return $this->toma4;
    }

    public function tagrem($values) {
        $this->rem = $this->dom->createElement('rem');        
        if(!empty($values->CNPJ)) {
            $this->appendChildElement($this->rem, 'CNPJ', $values->CNPJ);
        } else if(!empty($values->CPF)) {
            $this->appendChildElement($this->rem, 'CPF', $values->CPF);
        }

        $this->appendChildElement($this->rem, 'IE', $values->IE,false);        
        $this->appendChildElement($this->rem, 'xNome', $values->xNome);
        if(!empty($values->xFant)) {
            $this->appendChildElement($this->rem, 'xFant', $values->xFant,false);
        }

        $enderReme = $this->dom->createElement('enderReme');
        $this->appendChildElement($enderReme,'xLgr', $values->enderReme->xLgr);
        $this->appendChildElement($enderReme,'nro', $values->enderReme->nro);
        $this->appendChildElement($enderReme,'xBairro', $values->enderReme->xBairro);
        $this->appendChildElement($enderReme,'cMun', $values->enderReme->cMun);
        $this->appendChildElement($enderReme,'xMun', $values->enderReme->xMun);
        $this->appendChildElement($enderReme,'CEP', $values->enderReme->CEP,false);
        $this->appendChildElement($enderReme,'UF', $values->enderReme->UF);

        $this->rem->appendChild($enderReme);
        return $this->rem;
    }

    public function tagemit($values) {
        $this->emit = $this->dom->createElement('emit');
        $this->appendChildElement($this->emit,'CNPJ', $values->CNPJ);
        $this->appendChildElement($this->emit,'IE', $values->IE);
        $this->appendChildElement($this->emit,'xNome', $values->xNome);        
/*         if(!empty($values->xFant)) {
            $this->appendChildElement($this->emit,'xFant', $values->xFant,false);   
        } */

        $enderEmit = $this->dom->createElement('enderEmit');
        $this->appendChildElement($enderEmit,'xLgr', $values->enderEmit->xLgr);
        $this->appendChildElement($enderEmit,'nro', $values->enderEmit->nro);
        $this->appendChildElement($enderEmit,'xBairro', $values->enderEmit->xBairro);
        $this->appendChildElement($enderEmit,'cMun', $values->enderEmit->cMun);
        $this->appendChildElement($enderEmit,'xMun', $values->enderEmit->xMun);
        $this->appendChildElement($enderEmit,'CEP', $values->enderEmit->CEP);
        $this->appendChildElement($enderEmit,'UF', $values->enderEmit->UF);

        $this->emit->appendChild($enderEmit);               

        $this->appendChildElement($this->emit,'CRT', $values->CRT,false);

        return $this->emit;
    }

    public function tagdest($values) {
        $this->dest = $this->dom->createElement('dest');
        if(isset($values->CPF) &&  $values->CPF != '') {
            $this->appendChildElement($this->dest, 'CPF', $values->CPF);
        } else {
            $this->appendChildElement($this->dest, 'CNPJ', $values->CNPJ);
        }
        $this->appendChildElement($this->dest, 'IE', $values->IE,false);
        $this->appendChildElement($this->dest, 'xNome', $values->xNome);

        $enderDest = $this->dom->createElement('enderDest');
        $this->appendChildElement($enderDest, 'xLgr', $values->enderDest->xLgr);
        $this->appendChildElement($enderDest, 'nro', $values->enderDest->nro);
        $this->appendChildElement($enderDest, 'xBairro', $values->enderDest->xBairro);
        $this->appendChildElement($enderDest, 'cMun', $values->enderDest->cMun);
        $this->appendChildElement($enderDest, 'xMun', $values->enderDest->xMun);
        $this->appendChildElement($enderDest, 'CEP', $values->enderDest->CEP,false);
        $this->appendChildElement($enderDest, 'UF', $values->enderDest->UF);

        $this->dest->appendChild($enderDest);
        return $this->dest;
    }

    public function tagreceb($values) {
        $this->receb = $this->dom->createElement('receb');
        if(isset($values->CPF) &&  $values->CPF != '') {
            $this->appendChildElement($this->receb, 'CPF', $values->CPF);
        } else {
            $this->appendChildElement($this->receb, 'CNPJ', $values->CNPJ);
        }
        $this->appendChildElement($this->receb, 'IE', $values->IE,false);
        $this->appendChildElement($this->receb, 'xNome', $values->xNome);

        $enderReceb = $this->dom->createElement('enderReceb');
        $this->appendChildElement($enderReceb, 'xLgr', $values->enderReceb->xLgr);
        $this->appendChildElement($enderReceb, 'nro', $values->enderReceb->nro);
        $this->appendChildElement($enderReceb, 'xBairro', $values->enderReceb->xBairro);
        $this->appendChildElement($enderReceb, 'cMun', $values->enderReceb->cMun);
        $this->appendChildElement($enderReceb, 'xMun', $values->enderReceb->xMun);
        $this->appendChildElement($enderReceb, 'CEP', $values->enderReceb->CEP,false);
        $this->appendChildElement($enderReceb, 'UF', $values->enderReceb->UF);

        $this->receb->appendChild($enderReceb);
        return $this->receb;
    }

    public function tagcompl($values) {        
        $compl = $this->dom->createElement('compl');
        
        if(isset($values->xCaracAd))
            $this->appendChildElement($compl, 'xCaracAd', $values->xCaracAd,false);        

        if(isset($values->fluxo)) {
            $fluxo = $this->dom->createElement('fluxo');
            $this->appendChildElement($fluxo, 'xOrig', $values->fluxo->xOrig,false);
            $this->appendChildElement($fluxo, 'xDest', $values->fluxo->xDest,false);
            $compl->appendChild($fluxo);
        }

        if(isset($values->xObs))
            $this->appendChildElement($compl, 'xObs', $values->xObs,false);

        foreach($this->obsCont as $obs) {            
            $compl->appendChild($obs);
        }

        foreach($this->obsFisco as $obs) {
            $compl->appendChild($obs);
        }

        if($compl->hasChildNodes())
            $this->compl = $compl;

        return $this->compl;
    }

    public function tagObsCont($values) {
        if((count($this->obsCont) + 1) > 10) {
            $this->adicionaErro('Deve ter no máximo 10 observações');
        }

        $ObsCont = $this->dom->createElement('ObsCont');
        $ObsCont->setAttribute("xCampo", $values->xCampo);            
        $this->appendChildElement($ObsCont,'xTexto', $values->xTexto);
        $this->obsCont[] = $ObsCont;        
    }

    public function tagObsFisco($values) {
        if((count($this->obsFisco) + 1) > 10) {
            $this->adicionaErro('Deve ter no máximo 10 observações');
        }

        $ObsFisco = $this->dom->createElement('ObsFisco');
        $ObsFisco->setAttribute("xCampo", $values->xCampo);            
        $this->appendChildElement($ObsFisco,'xTexto', $values->xTexto);
        $this->obsFisco[] = $ObsFisco;        
    }

    public function tagComp($values) {        
        $Comp = $this->dom->createElement('Comp');
        $this->appendChildElement($Comp, 'xNome', $values->xNome);
        $this->appendChildElement($Comp, 'vComp', $values->vComp);                
        $this->comp[] = $Comp;          
    }

    public function tagVPrest($values) {
        $this->vPrest = $this->dom->createElement('vPrest');
        $this->appendChildElement($this->vPrest,'vTPrest', $values->vTPrest);
        $this->appendChildElement($this->vPrest,'vRec', $values->vRec);

        foreach($this->comp as $comp) {
            $this->vPrest->appendChild($comp);
        }
        
        return $this->vPrest;
    }

    public function tagimpICMS($values) {        
        $ICMS = $this->dom->createElement('ICMS');  
        
        if($values->CRT == '1'){
            $ICMSSN =  $this->dom->createElement('ICMSSN');
            $this->appendChildElement($ICMSSN,'CST', '90');
            $this->appendChildElement($ICMSSN,'indSN', '1');
            $ICMS->appendChild($ICMSSN);

        }else if($values->CST == '00')  {
            $ICMS00 =  $this->dom->createElement('ICMS00');
            $this->appendChildElement($ICMS00,'CST', $values->CST);
            $this->appendChildElement($ICMS00,'vBC', $values->vBC);
            $this->appendChildElement($ICMS00,'pICMS', $values->pICMS);
            $this->appendChildElement($ICMS00,'vICMS', $values->vICMS);
            $ICMS->appendChild($ICMS00);

        } else if($values->CST == '20') {
            $ICMS20 =  $this->dom->createElement('ICMS20');
            $this->appendChildElement($ICMS20,'CST', $values->CST);
            $this->appendChildElement($ICMS20,'pRedBC', $values->pRedBC);
            $this->appendChildElement($ICMS20,'vBC', $values->vBC);
            $this->appendChildElement($ICMS20,'pICMS', $values->pICMS);
            $this->appendChildElement($ICMS20,'vICMS', $values->vICMS);
            $ICMS->appendChild($ICMS20);

        } else if($values->CST == '40' || $values->CST == '41' || $values->CST == '51') {
            $ICMS45 =  $this->dom->createElement('ICMS45');
            $this->appendChildElement($ICMS45,'CST', $values->CST);
            $ICMS->appendChild($ICMS45);

        } else if($values->CST == '60') {
            $ICMS60 =  $this->dom->createElement('ICMS60');
            $this->appendChildElement($ICMS60,'CST', $values->CST);
            $this->appendChildElement($ICMS60,'vBCSTRet', $values->vBCSTRet); //Valor da BC do ICMS ST retido         
            $this->appendChildElement($ICMS60,'vICMSSTRet', $values->vICMSSTRet); // Valor do ICMS ST retido
            $this->appendChildElement($ICMS60,'pICMSSTRet', $values->pICMSSTRet); //Alíquota do ICMS
            $this->appendChildElement($ICMS60,'vCred', $values->vCred); //Valor do Crédito outorgado/Presumido            
            $ICMS->appendChild($ICMS60);

        } else if($values->CST == '90') //ICMS Outros
        { 
            $ICMS90 =  $this->dom->createElement('ICMS90');
            $this->appendChildElement($ICMS90,'CST', $values->CST);
            $this->appendChildElement($ICMS90,'pRedBC', $values->pRedBC);
            $this->appendChildElement($ICMS90,'vBC', $values->vBC);
            $this->appendChildElement($ICMS90,'pICMS', $values->pICMS);
            $this->appendChildElement($ICMS90,'vICMS', $values->vICMS);
            $this->appendChildElement($ICMS90,'vCred', $values->vCred);
            $ICMS->appendChild($ICMS90);
        }

        $this->impICMS = $ICMS;

        // Não está em uso no momento pela cte3.class
        // if ($values->vTotTrib > 0) {
        //     $this->appendChildElement($this->imp, 'vTotTrib', $values->vTotTrib,false);
        // }

        return $this->impICMS;
    }

    public function tagimpIBSCBS($values)
    {
        /* Campos disponíveis no $values até o momento
        CST, cClassTrib, vBC, pIBSUF,
        pDif_IBSUF, vDif_IBSUF, pRedAliq_IBSUF, pAliqEfet_IBSUF,
        vIBSUF, pIBSMun, pDif_IBSMun, vDif_IBSMun, 
        pRedAliq_IBSMun, pAliqEfet_IBSMun, vIBSMun, vIBS,
        pCBS, pDif_CBS, vDif_CBS, pRedAliq_CBS,
        pAliqEfet_CBS, vCBS, vTotDFe */

        $IBSCBS = $this->dom->createElement('IBSCBS');

        // Elementos simples
        $this->appendChildElement($IBSCBS, 'CST', $values->CST ?? null);
        $this->appendChildElement($IBSCBS, 'cClassTrib', $values->cClassTrib ?? null);

        // Grupo principal
        $gIBSCBS = $this->dom->createElement('gIBSCBS');

        $this->appendChildElement($gIBSCBS, 'vBC', $values->vBC ?? null);

        // =====================================================================
        // gIBSUF
        // =====================================================================
        $gIBSUF = $this->dom->createElement('gIBSUF');

        $this->appendChildElement($gIBSUF, 'pIBSUF', $values->pIBSUF ?? null);

        // gDif (IBSUF)
        if ($values->pDif_IBSUF > 0) {
            $gDif = $this->dom->createElement('gDif');
            $this->appendChildElement($gDif, 'pDif', $values->pDif_IBSUF ?? null);
            $this->appendChildElement($gDif, 'vDif', $values->vDif_IBSUF ?? null);
            $gIBSUF->appendChild($gDif);
        }

        // gRed (IBSUF)
        if ($values->pRedAliq_IBSUF > 0) {
            $gRed = $this->dom->createElement('gRed');
            $this->appendChildElement($gRed, 'pRedAliq', $values->pRedAliq_IBSUF ?? null);
            $this->appendChildElement($gRed, 'pAliqEfet', $values->pAliqEfet_IBSUF ?? null);
            $gIBSUF->appendChild($gRed);
        }

        $this->appendChildElement($gIBSUF, 'vIBSUF', $values->vIBSUF ?? null);

        $gIBSCBS->appendChild($gIBSUF);

        // =====================================================================
        // gIBSMun
        // =====================================================================
        $gIBSMun = $this->dom->createElement('gIBSMun');

        $this->appendChildElement($gIBSMun, 'pIBSMun', $values->pIBSMun ?? null);

        // gDif (IBSMun)
        if ($values->pDif_IBSMun > 0) {
            $gDif = $this->dom->createElement('gDif');
            $this->appendChildElement($gDif, 'pDif', $values->pDif_IBSMun ?? null);
            $this->appendChildElement($gDif, 'vDif', $values->vDif_IBSMun ?? null);
            $gIBSMun->appendChild($gDif);
        }

        // gRed (IBSMun)
        if ($values->pRedAliq_IBSMun > 0) {
            $gRed = $this->dom->createElement('gRed');
            $this->appendChildElement($gRed, 'pRedAliq', $values->pRedAliq_IBSMun ?? null);
            $this->appendChildElement($gRed, 'pAliqEfet', $values->pAliqEfet_IBSMun ?? null);
            $gIBSMun->appendChild($gRed);
        }

        $this->appendChildElement($gIBSMun, 'vIBSMun', $values->vIBSMun ?? null);

        $gIBSCBS->appendChild($gIBSMun);

        // =====================================================================
        // vIBS
        // =====================================================================
        $this->appendChildElement($gIBSCBS, 'vIBS', $values->vIBS ?? null);

        // =====================================================================
        // gCBS
        // =====================================================================
        $gCBS = $this->dom->createElement('gCBS');

        $this->appendChildElement($gCBS, 'pCBS', $values->pCBS ?? null);

        // gDif (CBS)
        if ($values->pDif_CBS > 0) {
            $gDif = $this->dom->createElement('gDif');
            $this->appendChildElement($gDif, 'pDif', $values->pDif_CBS ?? null);
            $this->appendChildElement($gDif, 'vDif', $values->vDif_CBS ?? null);
            $gCBS->appendChild($gDif);
        }

        // gRed (CBS)
        if ($values->pRedAliq_CBS > 0) {
            $gRed = $this->dom->createElement('gRed');
            $this->appendChildElement($gRed, 'pRedAliq', $values->pRedAliq_CBS ?? null);
            $this->appendChildElement($gRed, 'pAliqEfet', $values->pAliqEfet_CBS ?? null);
            $gCBS->appendChild($gRed);
        }

        $this->appendChildElement($gCBS, 'vCBS', $values->vCBS ?? null);

        $gIBSCBS->appendChild($gCBS);

        // Finaliza
        $IBSCBS->appendChild($gIBSCBS);

        $this->impIBSCBS = $IBSCBS;
        // Cria o totalizador (ele é depois do IBSCBS no montaCTe)
        $this->impvTotDFe = $this->dom->createElement('vTotDFe', $values->vTotDFe);

        return $this->impIBSCBS;
    }

    public function taginfQ($values) {
        // criar a tag e inserir no array
        $infQ = $this->dom->createElement('infQ');
        $this->appendChildElement($infQ, 'cUnid', $values->cUnid);
        $this->appendChildElement($infQ, 'tpMed', $values->tpMed);                
        $qCarga = explode('.',$values->qCarga);        
        $this->appendChildElement($infQ, 'qCarga',$qCarga[0] . '.' . str_pad($qCarga[1],4,'0'));                        
        $this->infQ[] = $infQ;
    }

    public function taginfCarga($values) {
        $this->infCarga = $this->dom->createElement('infCarga');
        $this->appendChildElement($this->infCarga,'vCarga', $values->vCarga, false);
        $this->appendChildElement($this->infCarga,'proPred', $values->proPred);
        $this->appendChildElement($this->infCarga,'xOutCat', $values->xOutCat,false);
        $this->appendChildElement($this->infCarga,'vCargaAverb', $values->vCargaAverb,false);

        if(count($this->infQ) < 1) {
            $this->adicionaErro("Tag: infQ é obrigatória");
        }

        foreach($this->infQ as $infQ) {
            $this->infCarga->appendChild($infQ);
        }
                     
        return $this->infCarga;
    }

    public function taginfDoc() {

        if(empty($this->infDoc)) {
            $this->infDoc = $this->dom->createElement('infDoc');
        }
        
        foreach($this->infNFe as $infNfe) {
            $this->infDoc->appendChild($infNfe);
        }

        foreach($this->infOutros as $infOutros) {
            $this->infDoc->appendChild($infOutros);
        }
    }

    public function taginfNfe($values) {
        $infNFe = $this->dom->createElement('infNFe');
        $this->appendChildElement($infNFe,'chave', $values->chave);
        $this->appendChildElement($infNFe,'dPrev', $values->dPrev,false);        
        $this->infNFe[] = $infNFe;
        return $infNFe;
    }

    public function taginfOutros($values) {
        $infOutros = $this->dom->createElement('infOutros');
        $this->appendChildElement($infOutros, 'tpDoc', $values->tpDoc);
        $this->appendChildElement($infOutros, 'descOutros', $values->descOutros, false);
        $this->appendChildElement($infOutros, 'nDoc', $values->nDoc, false);
        $this->appendChildElement($infOutros, 'dEmi', $values->dEmi, false);
        $this->appendChildElement($infOutros, 'vDocFisc', $values->vDocFisc, false);
        $this->appendChildElement($infOutros, 'dPrev', $values->dPrev, false);
        $this->infOutros[] = $infOutros;
        return $infOutros;
    }

    public function taginfModal($values) {
        $this->infModal  = $this->dom->createElement('infModal');
        //$this->infModal->setAttribute("versaoModal", $this->versaoModal);
        $this->infModal->setAttribute("versaoModal", $values->versao);
        $rodo = $this->dom->createElement('rodo');
        $this->appendChildElement($rodo, 'RNTRC', $values->RNTRC);
        $this->infModal->appendChild($rodo);
        return $this->infModal;
    }

    public function taginfCTeNorm() {
        if($this->infCTeNorm == '')
            $this->infCTeNorm = $this->dom->createElement('infCTeNorm');
        
        $this->infCTeNorm->appendChild($this->infCarga);
        $this->infCTeNorm->appendChild($this->infDoc);
        $this->infCTeNorm->appendChild($this->infModal);

        return $this->infCTeNorm;
    } 

    public function taginfCTeSupl($values) {     
        
        $this->infCTeSupl =  $this->dom->createElement('infCTeSupl');
        if($values->UF == 'PR'){
            $this->appendChildElement($this->infCTeSupl, 'qrCodCTe','http://www.fazenda.pr.gov.br/cte/qrcode?chCTe='. str_replace("CTe","",$this->id).'&amp;tpAmb='. $this->tpAmbiente);        
        } else{
            $this->appendChildElement($this->infCTeSupl, 'qrCodCTe','https://dfe-portal.svrs.rs.gov.br/cte/qrCode?chCTe='. str_replace("CTe","",$this->id).'&amp;tpAmb='. $this->tpAmbiente);        
        }      

        return $this->infCTeSupl;
       
    }

    public function tagCTe() {
        if (empty($this->CTe)) {
            $this->CTe = $this->dom->createElement('CTe');
            $this->CTe->setAttribute('xmlns', 'http://www.portalfiscal.inf.br/cte');
        }
        return $this->CTe;
    }    

    public function tagdocAnt() {
        $this->docAnt = $this->dom->createElement('docAnt');
        foreach($this->emiDocAnt as $emidocAnt) {
            $this->docAnt->appendChild($emidocAnt);
        }

        return $this->docAnt;
    }

    public function tagemiDocAnt($values) {
        $emiDocAnt = $this->dom->createElement('emiDocAnt');
        if(!empty($values->CNPJ)) {
            $this->appendChildElement($emiDocAnt, 'CNPJ', $values->CNPJ);
        } else if(!empty($values->CPF)) {
            $this->appendChildElement($emiDocAnt, 'CPF', $values->CPF);
        }        
        $this->appendChildElement($emiDocAnt, 'IE', $values->IE);
        $this->appendChildElement($emiDocAnt, 'UF', $values->UF);
        $this->appendChildElement($emiDocAnt, 'xNome', $values->xNome);

        foreach($this->idDocAntEle as $idDocAntEle) {
            $this->infDoc->appendChild($idDocAntEle);
        }

        $this->emiDocAnt[] = $emiDocAnt;
        return $emiDocAnt;
    }

    public function tagidDocAntEle($values) {
        $idDocAntEle = $this->dom->createElement('idDocAntEle');
        $this->appendChildElement($idDocAntEle, 'chCTe',$values->chCTe);
        $this->idDocAntEle[] = $idDocAntEle;
        return $idDocAntEle;
    }

    public function taginfCteAnu($values) {
        $this->infCteAnu = $this->dom->createElement('infCteAnu');
        $this->appendChildElement($this->infCteAnu, 'chCte', $values->chCte);
        $this->appendChildElement($this->infCteAnu, 'dEmi', $values->dEmi);
        return $this->infCteAnu;
    }

    public function taginfCteComp($values) {
        $this->infCteComp = $this->dom->createElement('infCteComp');
        $this->appendChildElement($this->infCteComp, 'chCTe', $values->chCte);        
        return $this->infCteComp;
    }

    public function tagSignature() {
        if(!empty($this->Signature))
            return false;

        $this->Signature = $this->dom->createElement('Signature');
        $this->Signature->setAttribute("xmlns", "http://www.w3.org/2000/09/xmldsig#");
        $this->Signature->setAttribute('xmlns', 'http://www.portalfiscal.inf.br/cte');

        $SignedInfo = $this->dom->createElement('SignedInfo');
        $this->Signature->appendChild($SignedInfo);

        $CanonicalizationMethod = $this->dom->createElement('CanonicalizationMethod');
        $CanonicalizationMethod->setAttribute("Algorithm","http://www.w3.org/TR/2001/REC-xml-c14n-20010315");
        $SignedInfo->appendChild($CanonicalizationMethod);

        $SignatureMethod  = $this->dom->createElement('SignatureMethod');
        $SignatureMethod->setAttribute('Algorithm','http://www.w3.org/2000/09/xmldsig#rsa-sha1');
        $SignedInfo->appendChild($SignatureMethod);

        $Reference = $this->dom->createElement('Reference');
        $Reference->setAttribute("URI", '#' . $this->id);
        $SignedInfo->appendChild($Reference);  

        $Transforms = $this->dom->createElement('Transforms');
        $Reference->appendChild($Transforms);

        $Transform = $this->dom->createElement('Transform');
        $Transform->setAttribute('Algorithm','http://www.w3.org/2000/09/xmldsig#enveloped-signature');
        $Transforms->appendChild($Transform);
        unset($Transform);

        $Transform = $this->dom->createElement('Transform');
        $Transform-> setAttribute('Algorithm','http://www.w3.org/TR/2001/REC-xml-c14n-20010315');
        $Transforms-> appendChild($Transform);

        
        $DigestMethod  = $this->dom->createElement('DigestMethod');
        $DigestMethod->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#sha1');
        $Reference->appendChild($DigestMethod);

        $DigestValue = $this->dom->createElement('DigestValue',"");
        $Reference->appendChild($DigestValue);

        $SignatureValue = $this->dom->createElement('SignatureValue',"");
        $this->Signature->appendChild($SignatureValue);

        $KeyInfo = $this->dom->createElement('KeyInfo');
        $this->Signature->appendChild($KeyInfo);

        $X509Data = $this->dom->createElement('X509Data');
        $KeyInfo->appendChild($X509Data);

        $X509Certificate = $this->dom->createElement('X509Certificate',"");
        $X509Data->appendChild($X509Certificate);
    }


    public function montarCTe() {     
        if($this->toma3 != null) {
            $this->ide->appendChild($this->toma3);
        } else {
            $this->ide->appendChild($this->toma4);
        }
        $this->infCte->appendChild($this->ide);        
        if($this->compl instanceof \DOMElement) {
            $this->infCte->appendChild($this->compl);
        }
        $this->infCte->appendChild($this->emit);
        $this->infCte->appendChild($this->rem);
        if ($this->receb != null) {
            $this->infCte->appendChild($this->receb);
        }
        $this->infCte->appendChild($this->dest);
        $this->infCte->appendChild($this->vPrest); 
        
        // Cria a tag imposto e adiciona os grupos de impostos configurados
        $imp = $this->dom->createElement('imp');
        // Se existir ICMS, adiciona dentro de <imp>
        if (!empty($this->impICMS)) {
            $imp->appendChild($this->impICMS);
        }
        // Se existir IBSCBS, adiciona também
        if (!empty($this->impIBSCBS)) {
            $imp->appendChild($this->impIBSCBS);
        }
        // Novo totalizador adicionado na reforma vTotDFe, só enviamos caso ele tenha sido criado dentro da classe
        if (!empty($this->impvTotDFe)) {
            $imp->appendChild($this->impvTotDFe);
        }
        // Agora adiciona <imp> dentro de <infCte>
        $this->infCte->appendChild($imp);

        if($this->infCTeNorm != '') {
            $this->infCte->appendChild($this->infCTeNorm);
        }  else if($this->infCteAnu != '') {
            $this->infCte->appendChild($this->infCteAnu);
        } else if($this->infCteComp != '') {
            $this->infCte->appendChild($this->infCteComp);
        }                

        $cte = $this->tagCTe();
        $cte->appendChild($this->infCte);        
        //$this->taginfCTeSupl($this->infCTeSupl);
        $cte->appendChild($this->infCTeSupl);        
        $this->tagSignature();
        $cte->appendChild($this->Signature);        
        $this->dom->appendChild($cte);

        $this->dom->normalize();                
    }

    private function appendChildElement($parent, $name,$value,$obrigatorio = true) {        

        if(!$obrigatorio && empty($value)) {
            return;
        }

        if($obrigatorio && !is_numeric($value)) {
            if(empty($value)) {                
                $this->adicionaErro("Tag: {$name} é obrigatório - Tag Pai: {$parent->tagName} ");
            }
        }
        
        $value = trim((string)$value);
        if($value !== '') {
            $tempElement = $this->dom->createElement($name,$value);
            $parent->appendChild($tempElement);
            return $tempElement;
        }
    }

    public function montaChave($codUF, $emissao, $cnpj, $mod,$serie,$cte,$formaEmissao, $codigoNumerico = '') {
        
        if(empty($codigoNumerico))
            $codigoNumerico = $cte;

        $chave = sprintf('%02d%04d%014d%02d%03d%09d%01d%08d', $codUF, $emissao, $cnpj, $mod,$serie,$cte,$formaEmissao, $codigoNumerico);        
        return $chave . static::gerarDV($chave);
    }

    public function getNumeroAleatorioChaveAcesso()
    {
        $tamanhoAleatorio = 8;
        if (empty($x)) {
            $x = rand(10000000, 99999999);
        }

        return str_pad($x, $tamanhoAleatorio, '0', STR_PAD_LEFT);
    }

    protected static function gerarDV($chave) {
        $mPesos=explode(",","2,3,4,5,6,7,8,9");
        $nSomaPonderada = 0;
        $nTotalPesos=strlen($chave)-1;
        $ny=0;
        for ($x = $nTotalPesos; $x >=0 ; $x--)
        {
            $np=$ny%8;
            $ny++;
            $nPonderacao = substr($chave,$x,1);
            $nSomaPonderada += $mPesos[$np]*$nPonderacao;
        }
        $nResto=$nSomaPonderada%11;

        if ($nResto<=1)
        {
           $nDV=0;
        }
        else
        {
           $nDV=11-$nResto;
        }
        return $nDV;
    }

    private function limpaXML($xml) {
        // O arquivo já é normalizado no final da montagem                
        // Remove quaisquer LF ou CR que ainda estejam no documento conforme Manual de Integração    
        $xml = str_replace(array(chr(13),chr(10),"\r","\n","\r\n")," ",$xml);
        $xml = str_replace("  ","",$xml);
        $xml = str_replace("> <","><",$xml);
        $xml = str_replace(" <","<",$xml);
        $xml = str_replace("> ",">",$xml);

        // Remover acentos
        $replace = array(
                'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A','Å'=>'A', 'Æ'=>'A', 
                'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a','å'=>'a', 'æ'=>'a', 
                'Ç'=>'C', 
                'ç' => 'c',
                'Ð' => 'D',
                'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', '&' => '&',
                'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e',
                'Ì'=>'I', 'Í'=>'I', 'Î'=>'I','Ï'=>'I', 
                'ì'=>'i', 'í'=>'i', 'î'=>'i','ï'=>'i', 
                'Ñ'=>'N', 
                'ñ'=>'n',
                'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 
                'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 
                'Ù'=>'U', 'Ú'=>'U','Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y',
                'ù'=>'u', 'ú'=>'u','û'=>'u', 'ü'=>'u', 'ý'=>'Y',
                'º' => '.', 'ª' => '.', 'Ž' => 'z', 'ž' => 'z', 'ð' => 'o','Š' => 'S','š'=>''
        );
                            
        return strtr($xml,$replace);                
    }


    public function isValid() {
        return $this->status;
    }
    public function getErrors() {
        return $this->erros;
    }

    public function adicionaErro($mensagem) {
        $this->erros[] = $mensagem;
        $this->status = false; 
    }
}
