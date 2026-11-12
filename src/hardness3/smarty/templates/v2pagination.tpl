<!DOCTYPE html>
<html lang="en">
<body>
<!-- row -->
<br>
<div class="row">
    <div class="align-center">
        <ul class="pagination">
            <li>
                <a href="{$url}?ajax=true&totalPagina={$totalPagina}&pagina=1&buscaProduto={$buscaProduto}" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            {for $i=$primeira to $ultima}                
                {if $i == $pagina}
                    <li><a href="{$url}?ajax=true&totalPagina={$ultima}&pagina={$i}&buscaProduto={$buscaProduto}" class="pag-selected pag">{$i}</a></li>
                {else}
                    <li><a href="{$url}?ajax=true&totalPagina={$ultima}&pagina={$i}&buscaProduto={$buscaProduto}"class="pag">{$i}</a></li>
                {/if}
            {/for}
            <li>
                <a href="{$url}?ajax=true&totalpagina={$ultima}&pagina={$totalPagina}&buscaProduto={$buscaProduto}" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>                     
    </div>
</div>
<!-- fim row -->
</body>

</html>
