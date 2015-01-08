<script type="text/javascript">

$(document).ready(function() {

	$(".tab_category-slider2").hide();
	$(".tab_category-slider2:first").show(); 

	$(".postabcateslider2 ul.tab_cates li").click(function() {
		$(".postabcateslider2 ul.tab_cates li").removeClass("active");
		$(this).addClass("active");
		$(".tab_category-slider2").hide();
		$(".tab_category-slider2").removeClass("animate1 {$tab_effect}");
		var activeTab = $(this).attr("rel"); 
		$("#"+activeTab) .addClass("animate1 {$tab_effect}");
		$("#"+activeTab).fadeIn(); 
	});
});

</script>
<div class="tab-category-container-slider postabcateslider2">
	<div class="container">
		<div class="container-inner row">
			<div class="tab-category">
				<ul class="tab_cates"> 
					{$count=0}
					{foreach from=$productCates item=productCate name=postabcateslider2}
						<li rel="tab_{$productCate.id}" {if $count==0} class="active"  {/if} > {$productCate.name}</li>
						{$count= $count+1}
					{/foreach}
				</ul>
				<div class="tab_container"> 
					{foreach from=$productCates item=productCate name=postabcateslider2}
					<div id="tab_{$productCate.id}" class="tab_category tab_category-slider2"> 
					
					<ul class="productTabCategorySlider productTabCategorySlider2 tabcate_{$productCate.id}">
					{foreach from=$productCate.product item=product name=postabcateslider2}
					<li class="cate_item">
					<div class="item-inner">
						<div class="box-img">
							<div class="img_btn">
								<a 	class="quick-view" href="{$product.link|escape:'html':'UTF-8'}" 
									rel="{$product.link|escape:'html':'UTF-8'}" 
									title="{l s='Quick view' mod='postabcateslider2'}"></a>
								<a 	onclick="WishlistCart('wishlist_block_list', 'add', '{$product.id_product|intval}', $('#idCombination').val(), 1,'tabcategory'); return false;" 
									class="add-wishlist wishlist_button" 
									title="{l s='Add to Wishlist' mod='postabcateslider2'}" ></a>
							</div>
							<div class="img_main">
								<a href="{$product.link|escape:'html'}" title="{$product.legend|escape:'html':'UTF-8'}" class="content_img clearfix">
									{if Hook::exec('rotatorImg')}
										{hook h='rotatorImg' product=$product}
									{else}
										<img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html'}"
										alt="{$product.legend|escape:'html':'UTF-8'}"
										class="img-responsive"/>
									{/if}
									{if $product.on_sale}
										<span class="sale-box no-print">
											<span class="sale-label">{l s='Sale'}</span>
										</span>
									{/if}
								</a>
							</div>
						</div>
						<h5 class="product-name">
							<a href="{$product.link|escape:'html'}" title="{$product.name|truncate:50:'...'|escape:'htmlall':'UTF-8'}">
								{$product.name|truncate:25:'...'|escape:'htmlall':'UTF-8'}
							</a>
						</h5>
						{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
							<div class="price-box">
								{if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
									<span class="old-price product-price">
										{displayWtPrice p=$product.price_without_reduction}
									</span>
								{/if}
								<span itemprop="price" class="price product-price">
									{if !$priceDisplay}
										{convertPrice price=$product.price}
									{else}
										{convertPrice price=$product.price_tax_exc}
									{/if}
								</span>
							</div>
						{/if}
						{if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.minimal_quantity <= 1 && $product.customizable != 2 && !$PS_CATALOG_MODE}
						{if ($product.allow_oosp || $product.quantity > 0)}
						{if isset($static_token)}
							<a class="cate_buy exclusive ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;token={$static_token}", false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart' mod='postabcateslider2'}" data-id-product="{$product.id_product|intval}">
								{l s='Add to cart' mod='postabcateslider2'}
							</a>
						{else}
							<a class="cate_buy exclusive ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart',false, NULL, 'add=1&amp;id_product={$product.id_product|intval}', false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart' mod='postabcateslider2'}" data-id-product="{$product.id_product|intval}">
								{l s='Add to cart' mod='postabcateslider2'}
							</a>
						{/if}						
						{else}
							<span class="cate_buy exclusive ajax_add_to_cart_button btn btn-default disabled">
								{l s='Add to cart' mod='postabcateslider2'}
							</span>
						{/if}
						{/if}
					</div>
					</li>
					{/foreach}
					</ul>
					</div>
					<script type="text/javascript"> 
					var owl2 = $(".tabcate_{$productCate.id}");

					owl2.owlCarousel({
					autoPlay : false,
					navigation: true,
					navigationText: false,
					items :4, //10 items above 1000px browser width
					itemsDesktop : [1199,3], //5 items between 1000px and 901px
					itemsDesktopSmall : [992,2], // betweem 900px and 601px
					itemsTablet: [768,2], //2 items between 600 and 0
					itemsMobile : [480,1] // itemsMobile disabled - inherit from itemsTablet option
					});
					// Custom Navigation Events
					</script>
					{/foreach}	
				</div> <!-- .tab_container -->
			</div>
		</div>
	</div>
</div>