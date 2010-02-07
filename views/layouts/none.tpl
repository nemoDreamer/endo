{if $has_errors}
  {errors key=notice layout=false}
  {errors key=fatal layout=false}
  {errors layout=false}
{/if}
{$content}
{$debug_dump}