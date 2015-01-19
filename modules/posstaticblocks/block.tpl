{foreach from=$staticblocks key=key item=block}
	{if $block.active == 1}
		<div class="yaoStageTitle">
	    <h2>
	    	{l s={$block.title} }
	    </h2>
	  </div>
	{/if}
	<div class="{if $block.insert_module}yaoStageBoxOuter{/if}">
    {$block.description}
		{if $block.insert_module == 1}
			{$block.block_module}
		{/if}
	</div>
{/foreach}
