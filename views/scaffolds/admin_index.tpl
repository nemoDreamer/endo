{if $filter->parents}
  <form id="filter_form">
    <div class="input select">
      <label>Filter by:</label>
      {html_options name='filter' options=$filter->parents selected=$filter->value}
    </div>
  </form>
{/if}

{if $is_ajax}
  {include file='admin_index-pagination.tpl'}
  <p id="search_form">
    <label for="search">Search:</label>
    <input type="text" name="search" prevalue="id, name, description, ..." id="search">
  </p>
{/if}

<ul id="items" class="items">
{foreach from=$items item=item name=item}
  {include file='admin_index-item.tpl'}
{/foreach}
</ul>

{endo_for_layout assign="sidebar"}
  <input type="hidden" name="url" value="/{$ADMIN_ROUTE}/{$url.controller}" id="url">
  <input type="hidden" name="is_publishable" value="{$is_publishable}" id="is_publishable">
  <p>{admin_link controller=$url.controller action="add" text="<span>Add a `$url.modelName`</span>" class="button add" set_gets=true}</a></p>
{/endo_for_layout}

<script type="text/javascript" charset="utf-8">
  <literal>
  $(document).ready(function () {

    var url = $('input#url').val() + '.xml';
    var search = $('input#search');

    $('#pagination a').live('click', function(e){

      $.ajax({
        url: url + $(this).attr('href'),
        data: {
          search: search.val()
        },
        dataType: 'xml'
      });

      e.preventDefault();
      return false;
    });

    function timeOutScope(){
      var timer = null;
      function do_search(){
        $.ajax({
          url: url,
          data: {
            search: search.val()
          },
          dataType: 'xml'
        });
      }
      this.start = function(){
        clearTimeout(timer);
        timer = setTimeout(do_search, 250);
      }
    }
    var timer = new timeOutScope();

    search.keyup(function(e){
      timer.start();
    });

  });
  </literal>
</script>