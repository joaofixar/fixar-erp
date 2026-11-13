<?php
namespace hardness;

        global $g;

        $con = mysql_connect('192.168.15.19','root','root');
        mysql_select_db('e001', $con) or die(mysql_error());
        // mysql_select_db('e223_novo', $con) or die(mysql_error());
        
        //conta teste
        //login yahoo
        //senha minha padrao nova

        //minha conta hardness
        $key = 'fd567cd4c13c7198e5b80f40b81a5148';
        $token = 'dcc8a9d218328c575a52adb6c19ca9073ef44c698f800a2cbd9c1f9506ed2e6a';

        
        // //$secret = "f5d911a539854f55c40ecf7b8df45a6ffc3eee288c02b324f9f97c9c9701d80e";
        // $key = "8d7edcd53687141b90e085513eaf16e6";
        // $token = "7ec165a3b0382e78bd3f88bf44b5609f273b45720e133796c6d5ac8e6d2bf13a";
        //url para conseguir o token
        //$yourAPIToken = "https://trello.com/1/authorize?expiration=1day&name=MyPersonalToken&scope=read&response_type=token&key={key}";

        $usuario = 63;
        // $usuario =  $g['usuarioAtual'];
        // $sqlC007 = mysql_query("SELECT C007_Id,C007_Key_Trello,C007_Token_Trello,C007_Nome from C007 where C007_Id={$usuario}");
        // $rC007 = mysql_fetch_array($sqlC007);
        // $token = $rC007["C007_Token_Trello"];
        // $key = $rC007["C007_Key_Trello"];


        $urlApi = "https://api.trello.com/1/";
        // $organizations="testetime86";
        $organizations = "hardnessframework";

  
        //trazer os boards
        $ch = curl_init("{$urlApi}organizations/{$organizations}/boards?key={$key}&token={$token}&showExternal=true");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        curl_close($ch);

        $board = (array) json_decode($data);

        $board = (array)$board[0];
        $idBoard = $board["id"];

        // $idBoard = '5ab92fadc6b40753f660ac97';

        echo "<pre>";
        print_r($board);
        echo "</pre>";


        // //Listas de um board
        $ch = curl_init("{$urlApi}boards/{$idBoard}/lists?key={$key}&token={$token}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        curl_close($ch);

        $list = (array) json_decode($data);

        echo "<hr>";
        echo "<pre>";
        print_r($list);
        echo "</pre>";

        // $chamado = $T234_Id;

        $chamado = 5;
        $sqlT234 = mysql_query("SELECT T234_Id,T234_D024_Id,T234_Data_Hora_Abertura,T234_Solicitante,T234_Descricao,D024_Nome_Empresa,T234_Link_Screenshot from T234 left join D024 on T234_D024_Id=D024_Id where T234_Id={$chamado}");
        $rT234 = mysql_fetch_array($sqlT234);
       

       // echo $rT234["D024_Nome_Empresa"];
// 
        // foreach ($list as $key => $listas) {
        //     $lista = (array) $listas;

        //     // echo "<br>lista nome:". $lista["name"];
        //     // echo "<br>lista id:". $lista["id"];

        //     if($lista["name"]=="primeira"){
        //         $idList = $lista["id"];
        //     }

        // }

        $idList ="5aba2f14f1c0325d71362de9";

        $nomeCard = urlencode(substr($rT234["D024_Nome_Empresa"],0,17)." - ".$rT234["T234_Id"]);

        $descCard =$rT234["T234_Data_Hora_Abertura"]." - ". $rT234["T234_Solicitante"].chr(13).chr(10).$rT234["T234_Descricao"];
        $descCard = str_replace("<div>", chr(13).chr(10),$descCard);
        $descCard = str_replace("</div>","",$descCard);
        $descCard = strip_tags($descCard);
        $descCard = urlencode($descCard);
        $urlImagem = urlencode($rT234["T234_Link_Screenshot"]);
        
        //mandar dados para card
        // $ch = curl_init("{$urlApi}cards?name={$nomeCard}&desc={$descCard}&idList={$idList}&urlSource={$urlImagem}&keepFromSource=all&key={$key}&token={$token}");
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_HEADER, 0);
        // $data = curl_exec($ch);
        // curl_close($ch);

        // echo "data:".$data;
        // echo "<br>Link: {$urlApi}cards?name={$nomeCard}&desc={$descCard}&idList={$idList}&keepFromSource=all&key={$key}&token={$token}";
 
?>