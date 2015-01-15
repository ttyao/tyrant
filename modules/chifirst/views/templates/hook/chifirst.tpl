<!-- Block mymodule -->
<div id="chifirst_block_home" class="block">
  <h4>Welcome!</h4>
  <div class="block_content">
    <p>Hello,
       {if isset($chi_first_name) && $chi_first_name}
           {$chi_first_name}
       {else}
           World
       {/if}
       !
    </p>
    <ul>
      <li><a href="{$chi_first_link}" title="Click this link">Click me!</a></li>
    </ul>
  </div>
</div>
<!-- /Block mymodule -->
