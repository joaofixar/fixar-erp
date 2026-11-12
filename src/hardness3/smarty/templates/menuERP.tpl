<ul id="barra" class="menubar ui-widget-header ui-corner-bottom">
	<li class="menuvertical" style="margin-top: 24px; float: right; margin-right: 166px;"><span onclick="window.location = '/logout';" style="cursor: pointer; font-weight: normal;">Sair</span></li>
	{foreach from=$menuArray key=menu item=menuA}
		<li class="menuvertical" style="float: right;"><a href="#" class="ui-corner-top">{$menu}</a> 
			<ul class="menu ui-corner-bottom ui-corner-tr"> 
				{foreach from=$menuA key=menu2 item=menuA2}
					{if is_array($menuA2)}
						<li class="submenu">
							{* color: rgb(68, 118, 164); *}
							<span style="float: right; font-weight: bolder; margin: 10px 6px 0px 0px; font-size: 0.9em; color: rgba(0,0,0,0.40);">{$menuA2[0]}</span>
							<a href="#" class="ui-corner-tr" onclick="{$menuA2[1]} return false;">{$menu2}</a>
						</li>
					{else}
						<li class="submenu"><a href="#" class="ui-corner-tr" onclick="{$menuA2} return false;">{$menu2}</a></li>
					{/if}
				{/foreach}
			</ul>
	   </li>
	{/foreach}
</ul>