<p id="pagination"{if $page_count <= 1} style="display:none;"{/if}>
  {if $page_count > 1}
    {if $page > 1}<a href="?page={$page-1}" class="previous">&laquo; <span>Previous Page</span></a>{/if}
    {if $page > 1 and $page < $page_count}<span class="separator">|</span>{/if}
    {if $page < $page_count}<a href="?page={$page+1}" class="next"><span>Next Page</span> &raquo;</a>{/if}
  {/if}
  <input type="hidden" name="page" value="{$page}" id="page">
</p>
