<!-- Block Viewed products -->
<div id="viewed-products_home" class="v-slider products_block col-xs-12 col-sm6 col-md-6 col-lg-4">
	<div class="v-slider-title">
		<h2>{l s='Viewed products' mod='posviewedproducts'}</h2>
	</div>
	{if $productsViewedObj}
		<ul class="posviewedproducts">			
			{foreach from=$productsViewedObj item=viewedProduct name=posviewedproductss}
				<li>
				
					<div class="item">
						<div class="pos-new-product-img">
							<a class="products-block-image" href="{$viewedProduct->product_link|escape:'html':'UTF-8'}" title="{l s='More about %s' mod='blockviewed' sprintf=[$viewedProduct->name|escape:'html':'UTF-8']}" >
							<img src="{if isset($viewedProduct->id_image) && $viewedProduct->id_image}{$link->getImageLink($viewedProduct->link_rewrite, $viewedProduct->cover, 'small_default')}{else}{$img_prod_dir}{$lang_iso}-default-medium_default.jpg{/if}" alt="{$viewedProduct->legend|escape:'html':'UTF-8'}" />
							</a>
						</div>
						<div class="pos-new-product-info">
						<h5 class="s_title_block"><a class="product-name" 
							href="{$viewedProduct->product_link|escape:'html':'UTF-8'}" 
							title="{l s='More about %s' mod='blockviewed' sprintf=[$viewedProduct->name|escape:'html':'UTF-8']}">
								{$viewedProduct->name|truncate:25:'...'|escape:'html':'UTF-8'}
							</a>
						</h5>
						<div>
						{if !$PS_CATALOG_MODE}
						<div class="price-box">
							{if $viewedProduct->reduction}
								<span class="old-price product-price">
									{convertPrice price=$viewedProduct->price}
								</span>
								{if $viewedProduct->reduction_type == 'amount'}
									<span class="price">{convertPrice price=$viewedProduct->price - $viewedProduct->reduction}</span>
									
								{else}
									<span class="price">{convertPrice price=$viewedProduct->price - round($viewedProduct->price * $viewedProduct->reduction / 100,2)}</span>
								{/if}
							{else}
								<span class="price">{convertPrice price=$viewedProduct->price}</span>
							{/if}	
						</div>
						
						<div class="pos-tab-content-bottom">
							<a class="exclusive ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$viewedProduct->id|intval}&amp;token={$static_token}", false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart' mod='posviewedproducts'}" data-id-product="{$viewedProduct->id|intval}">
								<span>{l s='Add to cart' mod='posviewedproducts'}</span>
							</a>
						</div>
						{/if}
					</div>
				</li>
			{/foreach}
		</ul>
		<script>
			$(document).ready(function() {
				$('.posviewedproducts').bxSlider({
				  mode: 'vertical',
				  useCSS: false,
				  pager: false,
				  controls: true,
				  auto: false,
				  minSlides: 3,
				  maxSlides: 3,
				  pause: 2500,
				  speed: 1000,
				});
			});
		</script>
	{else}
		<p>{l s='No product has been viewed' mod='posviewedproducts'} </p>
	{/if}

</div>
