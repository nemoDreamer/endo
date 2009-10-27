{if $has_errors}
  {endo_errors key=notice layout=false}
  {endo_errors key=fatal layout=false}
  {endo_errors layout=false}
{/if}
{$content}
{$debug_dump}