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
EOT;
$res = mysql_query("SELECT * FROM C004");
while ($row = mysql_fetch_assoc($res)) {
	echo <<<EOT
				<li data-theme="c" data-icon="false" style="padding: 8px;" onclick="\$.mobile.loadPage( '{$g['urlRaiz']}/empresa/{$row['C004_Id']}', {transition: 'fade', type:'post', reloadPage:true}); setTimeout(function(){ location.reload(); }, 100);">
					<div style="width: 100%; clear: both; text-align: left; margin-bottom: 2px; white-space: normal;">{$row['C004_Id']} - {$row['C004_Nome_Abreviado']}</div>
					<div style="width: 100%; clear: both; text-align: left; margin-bottom: 2px; white-space: normal;">{$row['C004_Nome_Empresa']}</div>
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
