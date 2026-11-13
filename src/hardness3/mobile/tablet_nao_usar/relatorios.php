<?php
namespace hardness;

include('conf.php');

include('inc.conf.php');

$script="";

include('inc.header.php');

echo <<<EOT
	<div data-role="page" id="pageRelatorios">
EOT;

$etapa = 6;
include('inc.header.bar.php');

echo <<<EOT
    <div data-role="content">
    	Em construção.
	</div>
EOT;

include('inc.footer.php');
echo <<<EOT
    </div>
    <script>
    	$( document ).undelegate("#pageRelatorios", "pageinit");
    	$( document ).delegate("#pageRelatorios", "pageinit", function() {
			{$script}
    	});
    </script>
EOT;
