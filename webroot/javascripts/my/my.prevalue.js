// --------------------------------------------------
// PREVALUE
// --------------------------------------------------

$(document).ready(function () {

  // inputs
  $('[pre]').each(function (i) {
    prevalue_active(this, true);
    input = $(this);
    input.focus(function (e) { prevalue_active(this, false) });
    input.blur(function (e) { prevalue_active(this, true) });
  });
  
  // clear on submit
  $('form').submit(function (e) {
    $('[pre]').each(function (i,o) {
      prevalue_active(o, false);
    });
    return true;
  });

});

function prevalue_active (input, prevalued) {
  input = $(input);
  if (prevalued) {
    if (input.attr('value')=='') {
      input.attr('value', input.attr('pre'));
      input.addClass('prevalued');
    };
  } else {
    if (input.attr('value')==input.attr('pre')) {
      input.attr('value', '');
      input.removeClass('prevalued');
    };
  };
}