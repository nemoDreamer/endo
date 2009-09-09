$('#{$url.url}_response').html("{escape}
{if $has_errors}
  <div class="failure">
    {endo_errors key=notice}
    {endo_errors key=fatal}
    {endo_errors}
  </div>
{/if}
{if $content}
  <div class="{if $success_for_layout!==false}success{else}failure{/if}">
    {$content}
  </div>
{/if}
{/escape}").slideDown();
$('#{$url.url}_form [pre]').each({literal}function(i,o) { prevalue_active(o,true); } {/literal});