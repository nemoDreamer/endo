<ul class="items">
{foreach from=$items item=item name=item}
  <li class="item {list_classes name='item'}">
    <div class="info">
      <span class="label sort">{$item->display_name()}</span>
      <span class="blurb sec">{$item->display_description()|truncate:256|htmlencode}</span>
      <div class="dates">
        <div class="date sec">
          created:<br/>
          <span class="created">{$item->date(created)}</span>
        </div>
        {if $item->created!=$item->modified}
          {assign var='modified' value=true}
        {else}
          {assign var='modified' value=false}
        {/if}
        <div class="date">
          <span class="sec">modified:<br/></span>
          <span class="modified sort{if $modified} highlight{/if}" rel="{$item->date(modified, true)}">{$item->date(modified)}</span>
        </div>
      </div>
    </div>
    <div class="options">
      {admin_options controller=$url.controller object=$item wrap=true}
    </div>
  </li>
{/foreach}
</ul>

{endo_for_layout assign="sidebar"}
<p>{admin_link controller=$url.controller action="add" text="<span>Add a `$url.modelName`</span>" class="button add"}</a></p>
{/endo_for_layout}
