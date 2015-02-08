{capture name=path}
    <a href="{smartblog::GetSmartBlogLink('smartblog')}">{l s='All Blog News' mod='smartblog'}</a>
    {if $title_category != ''}
        <span class="navigation-pipe">{$navigationPipe}</span>
        {$title_category}
    {/if}
{/capture}
{if $postcategory == ''}
    <p class="error">{l s='No Post in Archive' mod='smartblog'}</p>
{else}
    <div id="smartblogcat" class="center_column col-xs-12 col-sm-8 col-md-9">
        {foreach from=$postcategory item=post}
            {include file="./category_loop.tpl" postcategory=$postcategory}
        {/foreach}
    </div>
 {/if}
 {if isset($smartcustomcss)}
    <style>
        {$smartcustomcss}
    </style>
{/if}

