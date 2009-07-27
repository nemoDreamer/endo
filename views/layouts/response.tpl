$('#response').html("{escape}
{if $has_errors}
  <div class="errors">
    {endo_errors key=notice}
    {endo_errors key=fatal}
    {endo_errors}
  </div>
{/if}
{if $content}
  <div class="success">
    {$content}
  </div>
{/if}
{/escape}");