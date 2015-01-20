{if count($products)>1}
  <div class="grid">
    <ul>
      {foreach from=$products item=product name=posNewProducts}
        {if $smarty.foreach.posNewProducts.index == 10}
          {break}
        {/if}
        {$product1 = $products[10 - $smarty.foreach.posNewProducts.index]}
        <li class="yaoStageBox">
          <div class="item boxInner">
            <div class="productImg">
              <a class="product_img_link" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url">
                <img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html'}"
                   alt="{$product.legend|escape:'html':'UTF-8'}"
                   class="img-responsive"/>
              </a>
            </div>
            <div class="productInfo">
              <h2 class="product-name"><a href="{$product.link|escape:'html'}" title="{$product.name|truncate:50:'...'|escape:'htmlall':'UTF-8'}">{$product.name|truncate:35:'...'|escape:'htmlall':'UTF-8'}</a></h2>
              <div class="price-box">
                {if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
                  <span class="old-price product-price">
                    {displayWtPrice p=$product.price_without_reduction}
                  </span>
                {/if}
                <span class="price">{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}</span>
                <meta itemprop="priceCurrency" content="{$priceDisplay}" />
              </div>
              <div class="addToCartButton">
                {if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.minimal_quantity <= 1 && $product.customizable != 2 && !$PS_CATALOG_MODE}
                  {if ($product.allow_oosp || $product.quantity > 0)}
                    {if isset($static_token)}
                      <a class="ajax_add_to_cart_button" href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;token={$static_token}", false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart' mod='posnewproduct'}" data-id-product="{$product.id_product|intval}">
                      </a>
                    {else}
                      <a class="ajax_add_to_cart_button" href="{$link->getPageLink('cart',false, NULL, 'add=1&amp;id_product={$product.id_product|intval}', false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart' mod='posnewproduct'}" data-id-product="{$product.id_product|intval}">
                      </a>
                    {/if}
                  {/if}
                {/if}
              </div>
              <div class="addToWishList">
                <a onclick="WishlistCart('wishlist_block_list', 'add', '{$product.id_product|intval}', $('#idCombination').val(), 1,'tabcategory'); return false;"
                  class="wishListButton"
                  title="{l s='Add to Wishlist' mod='postabcateslider1'}"
                  href="#">
                </a>
              </div>
              <!-- <div class="quickView">
                <a class="quickViewButton" href="{$product.link|escape:'html':'UTF-8'}" rel="{$product.link|escape:'html':'UTF-8'}" title="{l s='Quick view' mod='postabcateslider1'}" />
              </div> -->
          </div>
        </li>
      {/foreach}
    </ul>
  </div>
{/if}
