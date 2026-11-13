<?php
echo <<<EOT

<div class="container-fluid">
	<div class="row-fluid">
		<div class="span3">

			<table class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th>Tabelas / Campos</th>
					</tr>
				</thead>
				<tbody id="ajudacomplete">
					<tr>
						<td>Em construção...</td>
					</tr>
				</tbody>
			</table>

		</div>
		<div class="span9">

			<textarea id="sqleditor"></textarea>
			<br style="clear: both;" />
			<table class="table table-striped table-bordered table-hover">
				<tbody>
					<tr>
						<td>...</td>
					</tr>
				</tbody>
			</table>

		</div>
	</div>
</div>
EOT;

?>

<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	//$('#sqleditor').focus();
} </script></div>