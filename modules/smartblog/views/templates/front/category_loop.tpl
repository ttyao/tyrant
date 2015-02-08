<div itemtype="#" itemscope="" class="sdsarticleCat clearfix">
  <div id="smartblogpost-{$post.id_post}">
    <div class="sdsarticleHeader">
      {assign var="options" value=null}
      {$options.id_post = $post.id_post}
      {$options.slug = $post.link_rewrite}
      <p class='sdstitle_block'><a title="{$post.meta_title}" href='{smartblog::GetSmartBlogLink('smartblog_post',$options)}'>{$post.meta_title}</a></p>
      {assign var="options" value=null}
      {$options.id_post = $post.id_post}
      {$options.slug = $post.link_rewrite}
      {assign var="catlink" value=null}
      {$catlink.id_category = $post.id_category}
      {$catlink.slug = $post.cat_link_rewrite}
      <span>
        {l s='Posted by' mod='smartblog'}
        <span itemprop="author">
          {if $smartshowauthor == 1}
            <i class="icon icon-user"></i>
            {if $smartshowauthorstyle != 0}{$post.firstname} {$post.lastname}{else}{$post.lastname} {$post.firstname}{/if}
          {/if}
        </span>
        <i class="icon icon-tags"></i>
        <span itemprop="articleSection">
          <a href="{smartblog::GetSmartBlogLink('smartblog_category',$catlink)}">
            {if $title_category != ''}{$title_category}{else}{$post.cat_name}{/if}
          </a>
        </span>
        <span class="comment">
          <i class="icon icon-comments"></i>
          <a title="{$post.totalcomment} Comments" href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}#articleComments">
            {$post.totalcomment} {l s=' Comments' mod='smartblog'}
          </a>
        </span>
        {if $smartshowviewed ==1}
          <i class="icon icon-eye-open"></i>
          {l s=' views' mod='smartblog'} ({$post.viewed})
        {/if}
        <i class="icon icon-calendar"></i>
        {$post.created}
      </span>
    </div>
    <div class="sdsarticle-des">
      <span itemprop="description" class="clearfix">
        <div id="lipsum">
          {$post.content}
        </div>
      </span>
    </div>
    <div class="sdsreadMore">
      {assign var="options" value=null}
      {$options.id_post = $post.id_post}
      {$options.slug = $post.link_rewrite}
      <span class="more"><a title="{$post.meta_title}" href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}" class="r_more">{l s='Read more' mod='smartblog'} </a></span>
    </div>
  </div>
</div>
