$.fn.showHide = function(bool){
  bool = bool!=false; // default to true
  if (bool) {
    this.show();
  } else {
    this.hide();
  };
  return this;
};

$(document).ready(function () {

  var is_settings = $('body[id^="settings_"]').length!=0;
  var is_index = $('body.admin_index').length!=0;
  var is_publishable = $('#is_publishable').val()==1;
  var has_secondaries = $('.sec:first').length!=0;

  // --------------------------------------------------
  // SORTING
  // --------------------------------------------------

  if (is_index && !is_settings) {

    // add tool html
    $('#tools').append(
      '<div id="tool_sort" class="group">'+
        '<input type="radio" name="sort_radio" id="sort_label" value="label" /> '+
        '<label for="sort_label">sort by label</label><br />'+
        '<input type="radio" name="sort_radio" id="sort_modified" value="modified" /> '+
        '<label for="sort_modified">sort by date modified</label><br />'+
      '</div>'
    );

    // sort by label
    $('#tool_sort :radio').click(function(e) {
      // get
      sort_by = $(this).val();
      list = $('ul.items');
      // sort
      sorted = $.makeArray(list.children('li.item')).sort(sortFunction) // <- array
      // format
      formatted = $(sorted).each(function(i,o){ // <- jQuery
        $(o)
          .removeClass('odd even')
          .addClass(i%2==0 ? 'odd' : 'even'); // i starts even...
      })
      // html
      list.html(formatted);

    }).eq(0).trigger('click');

  };

  // --------------------------------------------------
  // INDEX
  // --------------------------------------------------

  if (is_index) {

    if (has_secondaries) {
      $('#tools').admin_tool('secondaries', is_settings);
    };

    if (is_publishable) {
      $('#tools').admin_tool('published');
    };

    $('#items .item').livequery(function(){
      $(this).item_options()
    });

  };

  // --------------------------------------------------
  // SHOW MORE
  // --------------------------------------------------

  // saver
  var _more_wraps = [];

  // modify more's
  $('.show_more').each(function (i,o) {
    // get objects
    var div = $(o);
    var label = ($tmp=$(o).attr('more_label')) ? $tmp : 'add '+div.find('h3').text().replace(/:\s*$/, '');

    // add link after
    var id = 'more_link_'+i;
    div.after('<div class="more_link" id="'+id+'">&darr; <a href="#">'+label+'</a></div>');

    // wrap in div (so slide is smooth, even if fieldset has padding & margin)
    _more_wraps[id] = div.wrap('<div class="more_wrap">').hide();
  });

  // modify links
  $('.more_link a').click(function (e) {
    $(_more_wraps[$(this).parent().hide().attr('id')]).slideDown('fast');
    e.preventDefault();
    return false;
  });

  // --------------------------------------------------
  // DE/SELECT ALL
  // --------------------------------------------------

  $('form a[href="#select_all"]').each(function (i,o) {
    var self = $(o);
    var rel = self.attr('rel');
    var checkboxes = $('input[name="'+rel+'[]"]');

    self
      .text('Toggle '+rel+' selection')
      .click(function(e){
        var diff = checkboxes.length != checkboxes.filter('[checked!="checked"]').length;
        checkboxes.attr('checked', diff ? "checked" : false);
        e.preventDefault();
        return false;
      });
  });

  // --------------------------------------------------
  // FILTER FORM
  // --------------------------------------------------

   $('#filter_form').each(function (i,o) {
     var self = $(this);
     $(o).find('select').change(function(e){
       self.submit();
     });
   });

});

// --------------------------------------------------
// SORTING FUNCTIONS
// --------------------------------------------------

function sortFunction(a, b) {

  if (sort_by=='label') {
    var x = pad_all($(a).find('.sort.label:first').text().toLowerCase());
    var y = pad_all($(b).find('.sort.label:first').text().toLowerCase());
  } else if(sort_by=='modified') {
    var x = Date.parse($(a).find('.sort.modified:first').attr('rel'));
    var y = Date.parse($(b).find('.sort.modified:first').attr('rel'));
  };

  var z = ((x < y) ? -1 : ((x > y) ? 1 : 0));    

  if (sort_by=='modified') {
    z = -z;
  };

  return z;
}

// for correct alpha-numerical sorting
function pad_all (string) {
  return string.replace(/\b(\d+)\b/g, pad);
}

function pad(s) {
  if (s.length < 10) {
    s = ('0000000000' + s).slice(-10); // slice wasn't working...
  }
  return s;
}
// --------------------------------------------------
// ITEM OPTIONS
// --------------------------------------------------

$.fn.item_options = function(){
  $(this).each(function (i,o) {
    var item = $(o);
    var options = item.find('.options');
    item.hover(
      function(e){ options.show() },
      function(e){ options.hide() }
    );
    options.hide();
  });
};

// --------------------------------------------------
// ADMIN TOOLS
// --------------------------------------------------

$.fn.admin_tool = function(name, pre_checked){
  var t = $(this);
  var n = 'tool_' + name;

  // labels
  var labels = {
    secondaries: 'show details?',
    published: 'only published?'
  }
  // add tool html
  t.eq(0).append(
    '<div id="' + n + '" class="group">' +
      '<label><input type="checkbox"' + (pre_checked==true ? ' checked="checked"' : null) + '/> ' + labels[name] + '</label>' +
    '</div>'
  );

  // toggle
  var checkbox = $('#' + n + ' :checkbox')
    .load(function(e){
      switch (name) {
        case 'secondaries':
          $('.sec').showHide($(this).attr('checked')==true);
          break;
        case 'published':
          $('.unpublished').showHide($(this).attr('checked')!=true);
          break;
      }
    })
    .click(function(e){
      $(this).trigger('load');
    }).trigger('load');

  // live
  $('#items .item:first').livequery(function(e){
    checkbox.trigger('load');
  });
};
