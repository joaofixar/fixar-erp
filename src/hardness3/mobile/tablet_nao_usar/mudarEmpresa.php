<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');

include('inc.header.php');
echo <<<EOT
	<div data-role="page" id="pageEmpresas">
EOT;

$etapa = 99;
include('inc.header.bar.php');
echo <<<EOT
        <div data-role="content">
        	<ul data-role="listview" style="margin-top: 1px; clear:both;">
                <li data-role="list-divider" role="heading">
                    <table style="width: 100%";>
                        <tr>
                            <td style="width: 8%; text-align: center;">Id</td>
                            <td style="width: 50%;">Nome</td>
                            <td style="width: 15%;">CNPJ</td>
                            <td style="width: 20%;">Cidade</td>
                            <td style="width: 7%;">UF</td>
                        </tr>
                    </table>
                </li>
EOT;
$res = mysql_query("SELECT * FROM C004 LEFT JOIN D020 ON D020_Id=C004_D020_Id LEFT JOIN D018 ON D018_Id=C004_D018_Id");
while ($row = mysql_fetch_assoc($res)) {
	echo <<<EOT
				<li data-theme="c" data-icon="false" onclick="\$.mobile.loadPage( '{$g['urlRaiz']}/empresa/{$row['C004_Id']}', {transition: 'fade', type:'post', reloadPage:true}); setTimeout(function(){ location.reload(); }, 100);">
					<table style="width: 100%;">
                        <tr>
                            <td style="width: 8%; text-align: center;">{$row['C004_Id']}</td>
                            <td style="width: 50%;">{$row['C004_Nome_Empresa']}</td>
                            <td style="width: 15%;">{$row['C004_Cnpj']}</td>
                            <td style="width: 20%;">{$row['D020_Nome_Cidade']}</td>
                            <td style="width: 7%;">{$row['D018_UF']}</td>
                        </tr>
                    </table>
			    </li>
EOT;
}

echo <<<EOT
            </ul>
        </div>
    </div>
    <script>
    	$( document ).undelegate("#pageEmpresas", "pageinit");
    	$( document ).delegate("#pageEmpresas", "pageinit", function() {
EOT;
include('inc.swipe.events.php');
echo <<<EOT
		{$script}
    	});
    </script>
EOT;
