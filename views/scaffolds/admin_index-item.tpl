<li class="item {list_classes name='item'}{if !$item->is_published()} unpublished{/if}">
  <div class="info">
    <span class="label sort">{$item->display_field('name')}</span>
    <span class="blurb sec">{$item->display_field('description', false)|truncate:256|htmlencode}</span>
    {if isset($item->created)}
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
    {/if}
  </div>
  <div class="options">
    {admin_options controller=$url.controller object=$item wrap=true}
  </div>
</li>
