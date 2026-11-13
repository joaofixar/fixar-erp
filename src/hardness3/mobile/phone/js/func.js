//Funções Globais Javascript  - Tablet


//Função para permitir somente a entrada de caracter tipo Int no campo do Formulário
function Verifica_Caracter( event, elem) {
	//var tecla = event.keyCode;
	var tecla = (window.Event) ? event.which : event.keyCode;
	if (tecla > 47 && tecla < 58 || tecla < 31){
		return true;
	}
	else
	{
		return false;   
	}
}

function isset()
{
    var a=arguments; var l=a.length; var i=0;
    while (i!=l) {
        try {
            if (typeof(a[i])=='undefined') { return false; } else { i++; }
        } catch(err) {
            return false;
        }
    }
}
