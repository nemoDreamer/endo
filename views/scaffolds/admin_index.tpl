{if $parents}
  <form id="filter_form">
    <div class="input select">
      <label>Filter by:</label>
      {html_options name='filter' options=$parents selected=$filter}
    </div>
  </form>
{/if}
<ul class="items">
{foreach from=$items item=item name=item}
  <li class="item {list_classes name='item'}">
    <div class="info">
      <span class="label sort">{$item->display_field('name')}</span>
      <span class="blurb sec">{$item->display_field('description', false)|truncate:256|htmlencode}</span>
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
<p>{admin_link controller=$url.controller action="add" text="<span>Add a `$url.modelName`</span>" class="button add" set_gets=true}</a></p>
{/endo_for_layout}
