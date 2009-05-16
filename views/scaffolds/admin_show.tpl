<h2>Showing <em>&lsquo;{$item->display_field('name')}&rsquo;</em></h2>
<div id="show">
  <div class="info">
    {foreach from=$item->_for_show() key=key item=value}
      <div id="{$key}" class="key">
        <h3>{$key}</h3>
        <span class="value">{$value}</span>
      </div>
    {/foreach}
  </div>
  <div class="dates">
    <div class="date">
      created:
      <span class="created">{$item->date(created)}</span>
    </div>
    {if $item->created!=$item->modified}
      {assign var='modified' value=true}
    {else}
      {assign var='modified' value=false}
    {/if}
    <div class="date">
      modified:
      <span class="modified {if $modified} highlight{/if}" rel="{$item->date(modified, true)}">{$item->date(modified)}</span>
    </div>
  </div>
</div>

{endo_for_layout assign="sidebar"}
  <div id="tools">
    <div id="tool_options" class="group">
      {admin_options controller=$url.controller object=$item wrap=true show_label=true}
    </div>
  </div>
  <p>{admin_link controller=$url.controller action="add" text="<span>Add a `$url.modelName`</span>" class="button add" set_gets=true}</a></p>
{/endo_for_layout}
