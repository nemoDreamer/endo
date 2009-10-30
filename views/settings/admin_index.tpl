<div class="panels">
  {foreach from=$groups key=group_name item=group name=group}
    <div id="panel_{$group_name}" class="panel">
      <h2 class="panel_title">{$group_name|humanize}</h2>
      <ul id="items" class="items panel_content">
      {foreach from=$groups[$group_name] item=item name=item}
        <li class="item {list_classes name='item'}{if !$item->is_published()} unpublished{/if}">
          <div class="info">
            <span class="label sort">{$item->display_field('name')}</span>
            <span class="blurb sec">{$item->display_field('description', false)|truncate:256|htmlencode}</span>
          </div>
          <div class="options">
            {admin_options controller=$url.controller object=$item wrap=true}
          </div>
        </li>
      {/foreach}
      </ul>
    </div>
  {/foreach}
</div>

{endo_for_layout assign="sidebar"}
  <!-- <p>{admin_link controller=$url.controller action="add" text="<span>Add a `$url.modelName`</span>" class="button add" set_gets=true}</a></p> -->
{/endo_for_layout}
