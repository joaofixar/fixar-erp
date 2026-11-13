
  // FUNCOES EM JAVASCRIPT

  // Indentifica��o do Navegador:
  var ie5 = (document.getElementById && document.all);  // se for internet explorer ira retornar verdadeiro
  var ffox = (!document.all && document.getElementById); // se for se for firefox ira retornar verdadeiro

  var novalinha = "\r\r";
  var informe_suporte="\r\rPOR FAVOR INFORME AO SUPORTE TECNICO.";
  var nome_combo_div='';
  function confirma() {

     if (confirm("Confirma?")) {
        return true;
     } else {
        return false;
     }

  }

  // Confirma��o para formularios
  function Confirma_Formulario(form) {

     if (confirm("Confirma?")) {
        form.submit();
     } else {
        return false;
     }

  }
  
function formatar_geral(o, e, m, a)
{
	/*

      o = objeto
      e = evento
      m = mascara
      a = permite alfanumerico, deve receber S ou N


	Exemplos:

	- CEP
	OnKeyPress="return(formatar_geral(this, event, '#####-###','N'))"

	- CPF:
	OnKeyPress="return(formatar_geral(this, event,'###.###.###-##','N'))"

	- DATA:
	OnKeyPress="return(formatar_geral(this, event,'##/##/####','N'))"

	- CNPJ:
	OnKeyPress="return(formatar_geral(this, event,'##.###.###/####-##','N'))"

	- CPF:
	OnKeyPress="return(formatar_geral(this, event,'###.###.###-##','N'))"
	*/

	var strCheck = '0123456789';
	var cTecla = '';
	var nCodTecla = (window.Event) ? e.which : e.keyCode;
    var i = o.value.length;
    var campo=o.value;
    var saida = m.substring(0,1);
    var texto = m.substring(i);

	//-- SE FOR PRESSIONADO TAB ENTER PERMITE CONITNUAR DIGITANDO
    if ((nCodTecla==8) || (nCodTecla==0) || (nCodTecla == 13)) {
       return true;
    }

    // Verifica se � um Numero
    cTecla = String.fromCharCode(nCodTecla);

    //-- SO PERMITE NUMEROS
    if (a=='N')
    {
	   // SE NAO FOR NUMERO, RETORNA FALSO
	   if (strCheck.indexOf(cTecla) == -1) return false;
    }

    if (texto.substring(0,1) != saida)
    {
       campo += texto.substring(0,1);
    }

    o.value = campo;

    return true;
}


  // Faz o Enter pular para o proximo campo, que nao seja hidden nem esteja desabilitado.
  function Skip_Campo(campo,evento, nome_form) {
      //var nCodTecla = (window.Event) ? evento.which : evento.keyCode;
      //var proximo_campo = 0

      // 13 = Enter
      // 34 = PgDown
      // 38 = Seta Para Cima
      // 40 = Seta Para Baixo
      /*
      if (nCodTecla==xxx) {
         evento.returnValue = false;
      }
      if (nome_form==undefined) {
         nome_form='formulario';
      }
      if (evento.keyCode==xxx) {
          Confirma_Formulario(document.getElementById(nome_form))
          return false;
      }
      */
      /*
      for (var i=0 ; i< document.getElementById(nome_form).elements.length; i++) {
          if ((document.getElementById(nome_form).elements[i].type!='hidden') && (document.getElementById(nome_form).elements[i].disabled==false)) {
              if (nCodTecla==13) {
                 proximo_campo=i+parseInt(campo);
                 if ((document.getElementById(nome_form).elements[proximo_campo].type=='hidden') || (document.getElementById(nome_form).elements[proximo_campo].disabled==true)) {
                    campo=parseInt(campo)+1;
                    continue;
                 }
                 if ((document.getElementById(nome_form).elements[proximo_campo].type=='select-one') && (document.getElementById(nome_form).elements[proximo_campo].style.display=='')) {
                    continue;
                 }

                 document.getElementById(nome_form).elements[proximo_campo].focus();

                 return false;

              }
  
              if (evento.keyCode==38) {
                 proximo_campo=(i+parseInt(campo))-2;
                 if ((document.formulario.elements[proximo_campo].type=='hidden') || (document.formulario.elements[proximo_campo].disabled==true)) {
                    campo=parseInt(campo)-1;
                    continue;
                 }
                 document.formulario.elements[proximo_campo].focus();
                 return false;
              }
  

          }
      }
      */
  }

  // Funcao generica para verificar se algumas variaveis nao estao vazias
  function empty(str) {
	  if (str == "" || str == null) {
  		  return true;
      } else {
      	 return false;
      }
  }

  function ContaCaracter(Campo,Maximo)
  {
      var Total=document.getElementById(Campo).value.length;
      if (Total>=Maximo)
      {
         document.getElementById('Total_'+Campo).innerHTML='<font class=fonte>M�XIMO '+Maximo+' CARACTERES PERMITIDOS. DIGITADOS <blink><b><font style="color:red;font-size:12px">'+Total+'</font></b></blink> CARACTERES</font>';
      }
      else
      {
         document.getElementById('Total_'+Campo).innerHTML='<font class=fonte><b>'+Total+'</b> de '+Maximo+' caracteres</font>';
      }
  }

  // Funcao para Formatar datas dentro do input  formato: dd/mm/aaaa
   // Modo de Utilizacao: onKeyPress="return(Formatar_Data(this,event,'/'))"
   function Formatar_Data(campo,o,e,s,nome_form) {

       // o = objeto
       // e = evento
       // s = separador

	   var strCheck = '0123456789';
	   var cTecla = '';
	   var nCodTecla = (window.Event) ? e.which : e.keyCode;
	   var nTamanho=0;
	   var cValor='';

       // Se for um enter, pula para o proximo campo
       //Skip_Campo(campo,e,nome_form);

       if ((nCodTecla==8) || (nCodTecla==0) || (nCodTecla == 13)) {
          return true;
       }

	   cValor=o.value;

	   // Verifica se � um Valor ou Numero
	   cTecla = String.fromCharCode(nCodTecla);

	   // N�o � um N�mero ou Valor V�lido
	   if (strCheck.indexOf(cTecla) == -1) return false;

	   // Pega o Numero de Caracteres Digitados
	   nTamanho = cValor.length;


	   if (nTamanho<10) {

	     if (cValor.length==2) cValor=cValor.substring(0,2)+s;
	     if (cValor.length==5) cValor=cValor.substring(0,5)+s;

	     o.value=cValor;

	   } else {

	     // trava para digitacao se for maior que 10
	     o.value=o.value.substring(0,nTamanho-1);

	   }


  }

  function Formatar_Horas(campo,e)
  {
    var nCodTecla = (window.Event) ? e.which : e.keyCode;
    if ((nCodTecla==8) || (nCodTecla==0) || (nCodTecla == 13))
	{
	   return true;
    }

	if(nCodTecla<48 || nCodTecla>57)
	{
		return false;
	}

	if(campo.value.length==2 || campo.value.length==5)
	{
		campo.value+=":";
	}
  }

  // Funcao para Formatar datas dentro do input  formato: dd/mm/aaaa hh:mm:ss
  // Modo de Utilizacao: onKeyPress="return(Formatar_Data(this,event,'/'))"
  function Formatar_Data_Hora(campo,o,e,s,nome_form) {

      // o = objeto
      // e = evento
      // s = separador

	   var strCheck = '0123456789';
	   var cTecla = '';
	   var nCodTecla = (window.Event) ? e.which : e.keyCode;
	   var nTamanho=0;
	   var cValor='';

       // Se for um enter, pula para o proximo campo
       //Skip_Campo(campo,e,nome_form);

       if ((nCodTecla==8) || (nCodTecla==0) || (nCodTecla == 13)) {
          return true;
       }

	   cValor=o.value;

	   // Verifica se � um Valor ou Numero
	   cTecla = String.fromCharCode(nCodTecla);

	   // N�o � um N�mero ou Valor V�lido
	   if (strCheck.indexOf(cTecla) == -1) return false;

	   // Pega o Numero de Caracteres Digitados
	   nTamanho = cValor.length;


	   if (nTamanho<19) {

	     if (cValor.length==2) cValor=cValor.substring(0,2)+s;
	     if (cValor.length==5) cValor=cValor.substring(0,5)+s;
	     if (cValor.length==10) cValor=cValor.substring(0,10)+' ';
	     if (cValor.length==13) cValor=cValor.substring(0,13)+':';
	     if (cValor.length==16) cValor=cValor.substring(0,16)+':';
	     
	     o.value=cValor;

	   } else {

	     // trava para digitacao se for maior que 10
	     o.value=o.value.substring(0,nTamanho-1);

	   }


  }

  // Funcao Generica Para formatar Valor no Imput

  function Formatar_Valor(campo,fld, milSep, decSep, e, casas, tamanho, nome_form) {
      var sep = 0;
      var cTecla = '';
      var i = j = 0;
      var len = len2 = 0;
      var strCheck = '-0123456789';
      var aux = aux2 = '';
      var nCodTecla = (window.Event) ? e.which : e.keyCode;
      var zeros = 0;
      var casas;
      var tudozero=1;

      // Se for um enter, pula para o proximo campo
      //Skip_Campo(campo,e,nome_form);

      if ((nCodTecla==8) || (nCodTecla==0) || (nCodTecla==13)) {
         return true;
      }

      // Verifica se � um Valor ou Numero
      cTecla = String.fromCharCode(nCodTecla);

      // N�o � um N�mero ou Valor V�lido

      if (strCheck.indexOf(cTecla) == -1) return false;

      len = fld.value.length;



      for(i = 0; i < len; i++) {
         if ((fld.value.substr(i, 1)!=0) && (fld.value.substr(i, 1)!=',') && (fld.value.substr(i, 1)!='.')) {
            tudozero++;
         }         
      } 

      if ((len>=tamanho) && (tudozero==len)) return false;

      for(i = 0; i < len; i++)

      if ((fld.value.charAt(i) != '0') && (fld.value.charAt(i) != decSep)) break;

      aux = '';

      for(; i < len; i++)
      if (strCheck.indexOf(fld.value.charAt(i))!=-1) aux += fld.value.charAt(i);

      aux += cTecla;
      len = aux.length;

      for(z = 1; z < (casas-len); z++) zeros=zeros+'0';

      for(c = 0; c <= casas; c++) {
          if (len!=casas) {
              if (len == c) fld.value = '0'+ decSep + zeros + aux;
          } else {
              if (len == casas) fld.value = '0'+ decSep + aux;
          }
      }

      if (len > casas) {
          aux2 = '';
          for (j = 0, i = len - (casas+1); i >= 0; i--) {
              if (j == 3) {
                 aux2 += milSep;
                 j = 0;
              }
              aux2 += aux.charAt(i);
              j++;
          }
          fld.value = '';
          len2 = aux2.length;
          for (i = len2 - 1; i >= 0; i--)
              fld.value += aux2.charAt(i);
           fld.value += decSep + aux.substr(len-casas, len);
      }
      return false;
  }

  // Maximiza a Tela
  function maximiza() {
     if (navigator.appName=='Microsoft Internet Explorer') {
         window.moveTo(-4,-4);
         window.resizeTo(screen.availWidth+8,screen.availHeight+8);
     } else if (navigator.platform=='Win32') {
         window.moveTo(0,0);
         window.resizeTo(screen.availWidth,screen.availHeight);
     } else {
         window.moveTo(0,0);
         window.resizeTo(screen.availWidth,screen.availHeight);
     }
  }


//   var xvoltar=history.length-1;
//   var voltar_padrao=history.length;

//  function voltar() {
  
//       if (voltar_padrao==0) {
//          self.close();
//       } else {
//           zvoltar = xvoltar - history.length;
//           alert('zvoltar ' + zvoltar + ' xvoltar ' + xvoltar + 'history.length ' + history);
//           xvoltar-=1;
//
//           if (zvoltar>=0) {
//              zvoltar = -1
//           }
//           history.go(zvoltar);
//       }
//  }


  function Formatar_Cnpj(campo,o,e,nome_form){

    var vr = o.value;
    var tam = vr.length;
    var strCheck = '0123456789';
    var cTecla = '';
    var nCodTecla = (window.Event) ? e.which : e.keyCode;
	var cValor= vr;

    // Se for um enter, pula para o proximo campo
    //Skip_Campo(campo,e,nome_form);

    if ((nCodTecla==8) || (nCodTecla==0) || (nCodTecla == 13)) {
       return true;
    }

    // Verifica se � um Valor ou Numero
    cTecla = String.fromCharCode(nCodTecla);


    // N�o � um N�mero ou Valor V�lido
    if (strCheck.indexOf(cTecla) == -1) {
        return false;
    }

        if (tam == 2)
            cValor = vr.substr( 0, 2 ) + '.';

        if (tam == 6)
            cValor =  vr.substr( 0, 6 ) + '.';

        if  (tam == 10)
            cValor = vr.substr( 0, 10) + '/';

        if  (tam == 15)
            cValor = vr.substr( 0, 15) + '-';

      	o.value=cValor;

  }

  var size_hist=history.length;
  function abre() {
     if (size_hist>0) {
         top.document.all.imp.cols='60,*';
    }
  }

  function Formatar_Cpf(campo,o,e,nome_form)  {

    var vr = o.value;
    var tam = vr.length;
    var strCheck = '0123456789';
    var cTecla = '';
    var nCodTecla = (window.Event) ? e.which : e.keyCode;
    var cValor= vr;

    // Se for um enter, pula para o proximo campo
    //Skip_Campo(campo,e,nome_form);

    if ((nCodTecla==8) || (nCodTecla==0) || (nCodTecla == 13)) {
       return true;
    }

    // Verifica se � um Valor ou Numero
    cTecla = String.fromCharCode(nCodTecla);

    // N�o � um N�mero ou Valor V�lido
    if (strCheck.indexOf(cTecla) == -1) return false;

        if (tam == 3)
            cValor = vr.substr( 0, 3 ) + '.';

        if (tam == 7)
            cValor =  vr.substr( 0, 7 ) + '.';

        if  (tam == 11)
            cValor = vr.substr( 0, 11) + '-';

      	o.value=cValor;
  }

  function Formatar_Ncm_Naladi(campo,o,e,nome_form)  {

    var vr = o.value;
    var tam = vr.length;
    var strCheck = '0123456789';
    var cTecla = '';
    var nCodTecla = (window.Event) ? e.which : e.keyCode;
    var cValor= vr;

    // Se for um enter, pula para o proximo campo
    //Skip_Campo(campo,e,nome_form);

    if ((nCodTecla==8) || (nCodTecla==0) || (nCodTecla == 13)) {
       return true;
    }

    // Verifica se � um Valor ou Numero
    cTecla = String.fromCharCode(nCodTecla);

    // N�o � um N�mero ou Valor V�lido
    if (strCheck.indexOf(cTecla) == -1) return false;

        if (tam == 4)
            cValor = vr.substr( 0, 4 ) + '.';

        if (tam == 7)
            cValor =  vr.substr( 0, 7 ) + '.';

      	o.value=cValor;
  }

  function Formatar_Classificacao_Fiscal(campo,o,e,nome_form)  {

    var vr = o.value;
    var tam = vr.length;
    var strCheck = '0123456789';
    var cTecla = '';
    var nCodTecla = (window.Event) ? e.which : e.keyCode;
    var cValor= vr;

    // Se for um enter, pula para o proximo campo
    //Skip_Campo(campo,e,nome_form);

    if ((nCodTecla==8) || (nCodTecla==0) || (nCodTecla == 13)) {
       return true;
    }

    // Verifica se � um Valor ou Numero
    cTecla = String.fromCharCode(nCodTecla);

   // N�o � um N�mero ou Valor V�lido
   if (strCheck.indexOf(cTecla) == -1) return false;

        if (tam == 4)
            cValor = vr.substr( 0, 4 ) + '.';

        if (tam == 7)
            cValor =  vr.substr( 0, 7 ) + '.';

      	o.value=cValor;


  }

  function Formatar_Mascara(campo,o,e,mascara,nome_form){

    var vr = o.value;
    var tam = vr.length;
    var strCheck = '0123456789';
    var cTecla = '';
    var nCodTecla = (window.Event) ? e.which : e.keyCode;
	var cValor = '';
    var cCaracter = '' ;

    // Se for um enter, pula para o proximo campo
    //Skip_Campo(campo,e,nome_form);

    if ((nCodTecla==8) || (nCodTecla==0) || (nCodTecla == 13)) {
       return true;
    }

    // Verifica se � um Valor ou Numero
    cTecla = String.fromCharCode(nCodTecla);


    // N�o � um N�mero ou Valor V�lido
    if (strCheck.indexOf(cTecla) == -1) {
        return false;
    }
    // tira tudo que nao for numero
    for (var i=0; i<vr.length; i++) {
        if (strCheck.indexOf(vr.substr(i,1)) == -1) {
           continue;
        } else {
           vr=vr+vr.substr(i,1);
        }
    }
  	o.value=vr;
    alert(vr);
    return false;
    for (var i=0; i<vr.length; i++) {
  //        if (((i+1)>mascara.length) ||
        if (mascara.substr(i,1)==9) {
           cValor=cValor+vr.substr(i,1);
        } else {
           cValor=cValor+mascara.substr(i,1)+vr.substr(i,1);
        }
  //        cValor = cValor+vr.substr(x,1);
    }

  	o.value=cValor;

  }

  function nova_janela(Pagina,Topo,Posicao,Largura,Altura,Fixo,Botoes,Endereco,Status,Rolagem,Menu,Nome) {
        if (!Fixo)   {   var Fixo    = 'yes';                       }
        if (!Rolagem){   var Rolagem = 'yes';                       }
        if (!Menu)   {   var Menu    = 'yes';                       }
        if (!Status) {   var Status  = 'yes';                       }
        param = "menubar="+Menu+",toolbar="+Botoes+",location="+Endereco+",status="+Status+",scrollbars="+Rolagem+",resizable="+Fixo+",width="+Largura+",height="+Altura+",top="+Topo+",left="+Posicao;
        window.open(Pagina,Nome,param);
  }

  function nova_janela_full(Pagina,Topo,Posicao,Largura,Altura,Fixo,Botoes,Endereco,Status,Rolagem,Menu,Nome) {
        if (!Fixo)   {   var Fixo    = 'yes';   }
        if (!Rolagem){   var Rolagem = 'yes';   }
        if (!Menu)   {   var Menu    = 'no';    }
        if (!Status) {   var Status  = 'yes';   }
        param = "menubar="+Menu+",toolbar="+Botoes+",location="+Endereco+",status="+Status+",scrollbars="+Rolagem+",resizable="+Fixo+",width="+Largura+",height="+Altura+",top="+Topo+",left="+Posicao;
        window.open(Pagina,Nome,param);
  }

/*  function nova_janela(Pagina,Topo,Posicao,Largura,Altura,Fixo,Botoes,Endereco,Status,Rolagem,Menu,Nome) {
        if (!Topo)   {   var Topo    = 0;                           }
        if (!Posicao){   var Posicao = 0;                           }
        if (!Largura){   var Largura = window.screen.availWidth-10; }
        if (!Altura) {   var Altura  = window.screen.availHeight-30;}
        if (!Fixo)   {   var Fixo    = 'yes';                       }
        if (!Rolagem){   var Rolagem = 'yes';                       }
        if (!Menu)   {   var Menu    = 'yes';                       }
        if (!Status) {   var Status  = 'yes';                       }
        param = "menubar="+Menu+",toolbar="+Botoes+",location="+Endereco+",status="+Status+",scrollbars="+Rolagem+",resizable="+Fixo+",width="+Largura+",height="+Altura+",top="+Topo+",left="+Posicao;
        window.open(Pagina,Nome,param);
  } */

  function first_focus() {
    for (var i=0 ; i< document.formulario.elements.length; i++) {
        if ((document.formulario.elements[i].type!='hidden') && (document.formulario.elements[i].disabled==false) && (document.formulario.elements[i].type!='button')) {
           document.formulario.elements[i].focus();
           return;
        }
    }
  }

  function LimpaCombo(inSelect){
     sLength = inSelect.length;
     if(sLength > 0) {
         for(i = (sLength -1); i >= 1 ; i--) {
             inSelect.options[i] = null;
         }
     }
     inSelect.options[0].selected=true;
  }

  //-- Substituir caracteres
  function trocar_string(velho,novo,texto) {
    for (var i=0; i<texto.length; i++) {
           texto = texto.replace(velho,novo);
    }
    return texto;
  }


    var UltimoDivCalendario='';
    function AbreDivCalendarioClasse(e,Campo_Data,Nome_Div,Funcao)
    {
        var PosicaoX=0;
        var PosicaoY=0;
        var Posicao_FinalX_Div=0;
        var Posicao_FinalY_Div=0;
        var mouseX=0;
        var mouseY=0;
        var mouse2X=0;
        var mouse2Y=0;
        var Tamanho_Total_Menu_Registro=200;
        if (ffox)
        {
        	mouseX = window.pageXOffset + e.clientX;
            mouseY = window.pageYOffset + e.clientY;
        	mouse2X = parent.window.pageXOffset + e.clientX;
            mouse2Y = parent.window.pageYOffset + e.clientY;
        }
        else
        {
            mouseX = window.event.x + document.body.scrollLeft;
        	mouseY = window.event.y + document.body.scrollTop;
            mouse2X = parent.window.event.x + document.body.scrollLeft;
        	mouse2Y = parent.window.event.y + document.body.scrollTop;

        }
        mouseOut = false;

    	if (document.getElementById(Nome_Div).style.display=='inline')
    	{
    		document.getElementById(Nome_Div).style.display='none';
    	}
    	else
    	{
    		if (UltimoDivCalendario!='')
    		{
    			document.getElementById(UltimoDivCalendario).style.display='none';
    		}
    		UltimoDivCalendario=Nome_Div;

    	    var Data=document.getElementById(Campo_Data).value;
    		cLink='../include/calendario.php?Data='+Data+'&Campo_Data='+Campo_Data+'&Nome_Div='+Nome_Div+'&FuncaoCalendario='+Funcao;
    		document.getElementById(Nome_Div).style.display='inline';
    		carregar_ajax(cLink,'POST',false,Nome_Div);
    	}
    }
    function AtualizaDivCalendario(Data,Campo_Data,Nome_Div)
    {
    	cLink='../include/calendario.php?Data='+Data+'&Campo_Data='+Campo_Data+'&Nome_Div='+Nome_Div;
    	carregar_ajax(cLink,'POST',false,Nome_Div);
    }
    function FechaDivCalendarioClasse(Nome_Div)
    {
    	document.getElementById(Nome_Div).style.display='none';
    }
    
// *****************************************

function DANFE(Query)
{
	param = "toolbar=yes,menubar=yes,location=no,scrollbars=yes,status=yes,resizable=yes,width=990,height=542,top=0,left=0";
	window.open("../../danfe/imprimir_danfe.php?Query="+Query,"DANFE"+Query,param);
}

