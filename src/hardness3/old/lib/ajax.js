//Prototype e herança de objeto
//crio um objeto principal e outros objetos que serao as subClasse para o primeiro, fazendo a herança de objetos
function ajax() {
};
//metodo iniciar
ajax.prototype.iniciar = function() {
//logo abaixo tentamos estanciar o objeto XMLHttpRequest() para o IE e os demais navegadores
    try{
        this.xmlhttp = new XMLHttpRequest();
    }catch(ee){
        try{
            this.xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
        }catch(e){
            try{
                this.xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }catch(E){
                this.xmlhttp = false;
            }
        }
    }
    return true;
}
//metodo oculpado
ajax.prototype.ocupado = function() {
    estadoAtual = this.xmlhttp.readyState;
    return (estadoAtual && (estadoAtual < 4));
}
//metodo processa
ajax.prototype.processa = function() {
    if (this.xmlhttp.readyState == 4 && this.xmlhttp.status == 200) {
        return true;
    }
}
    //metodo envia esperando a url, o metodo get ou post e o modo true ou false
    ajax.prototype.enviar = function(url, metodo, modo) {
    //se nao existir o objeto xmlhttp ele sera criado com o metodo iniciar
    if (!this.xmlhttp) {
        this.iniciar();
    }
    //se nao estiver oculpado
    if (!this.ocupado()) {
        //se o metodo passado for get
        if(metodo == "GET") {
            this.xmlhttp.open("GET", url, modo);
            this.xmlhttp.send(null);
        } else {
            //se for post        
            this.xmlhttp.open("POST", url, modo);
            this.xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
            this.xmlhttp.setRequestHeader("Cache-Control", "no-store, no-cache, must-revalidate");
            this.xmlhttp.setRequestHeader("Cache-Control", "post-check=0, pre-check=0");
            this.xmlhttp.setRequestHeader("Pragma", "no-cache");
            this.xmlhttp.send(url);
        }    

        //se estiver ok ele retorna o resultado e ja utilizamos um modo para receber acentos
        // usando a função unescape e substituindo os + por espaços mesma coisa que usar urldecode() do php

        if (this.processa) {

            //recebe o resultado da da pagina php
            return unescape(this.xmlhttp.responseText.replace(/\+/g," "));

        }
    }
    return false;
}

function str_replace(busca, repla, orig) {
    str = new String(orig);
    rExp= "/"+busca+"/g";
    rExp= eval(rExp);
    newS= String(repla);
    str = new String(str.replace(rExp, newS));
    return str;
}




var frwScriptEval = function(texto) {
    var ini, pos_src, fim, codigo;
    var objscript = null;
    var scriptOnLoad = "";
    
    ini = texto.indexOf('<script', 0)
    
    while (ini != -1){
    
        objscript = document.createElement("script");
        //Busca se tem algum src a partir do inicio do script
        pos_src = texto.indexOf(' src', ini)
        ini = texto.indexOf('>', ini) + 1;

        //Verifica se este e um bloco de script ou include para um arquivo de scripts
        if (pos_src < ini && pos_src >= 0){//Se encontrou um "src" dentro da tag script, esta e um include de um arquivo script
            //Marca como sendo o inicio do nome do arquivo para depois do src
            ini = pos_src + 4;
            //Procura pelo ponto do nome da extencao do arquivo e marca para depois dele
            fim = texto.indexOf('.', ini)+4;
            //Pega o nome do arquivo
            codigo = texto.substring(ini,fim);
            //Elimina do nome do arquivo os caracteres que possam ter sido pegos por engano
            codigo = codigo.replace("=","").replace(" ","").replace("\"","").replace("\"","").replace("\'","").replace("\'","").replace(">","");
            // Adiciona o arquivo de script ao objeto que sera adicionado ao documento
            objscript.src = codigo;
        } else { //Se nao encontrou um "src" dentro da tag script, esta e um bloco de codigo script
            // Procura o final do script
            fim = texto.indexOf('</script>', ini);
            // Extrai apenas o script
            codigo = texto.substring(ini,fim);
            // Adiciona o bloco de script ao objeto que sera adicionado ao documento
            // NÃO FUNCIONA NO IE!!!
            // objscript.text = codigo;

            // Mudança: Acumulador de scripts a serem executados no onLoad
            scriptOnLoad += codigo.replace(/\n/g,"");
        }

        //Adiciona o script ao documento
        document.body.appendChild(objscript);
        // Procura a proxima tag de <script
        ini = texto.indexOf('<script', fim);
        
        //Limpa o objeto de script
        objscript = null;
    }
    
    //if (scriptOnLoad != "") {
        //texto += "\n<img class=\"loader\" src=\"img/loader.gif\" onLoad=\"java script:" + scriptOnLoad + "\" />";
    //}
    texto = texto.replace('"));',"");
    return texto;
}





function carregar_ajax(url, metodo, modo, obj_div) {
    //estancio o ajax
    remoto    = new ajax();

    //envio os dados para uma pagina php
    envia     = remoto.enviar(url , metodo, modo );

    try
    {
        document.getElementById(obj_div).innerHTML = frwScriptEval(envia);
    }
    catch(envia)
    {
       alert('Objeto não encontrado: '+obj_div);
    }

}

