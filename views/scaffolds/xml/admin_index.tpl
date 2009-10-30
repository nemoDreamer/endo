<taconite>
  <replace select="#items">
    {cdata}
      <ul id="items" class="items">
        {foreach from=$items item=item name=item}
          {include file='admin_index-item.tpl'}
        {/foreach}
      </ul>
    {/cdata}
  </replace>
  <replace select="#pagination">
    {cdata}
      {include file='admin_index-pagination.tpl'}
    {/cdata}
  </replace>
</taconite>