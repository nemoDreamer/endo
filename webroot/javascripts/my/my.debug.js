// --------------------------------------------------
// DEBUG
// --------------------------------------------------

$(document).ready(function () {
  
  $('.debug').hide();
  
  debug='debug';
  keystrokes='';
  
  $(document).keypress(function (e) {
    keystrokes += String.fromCharCode(e.which);
    if (keystrokes.substr(-debug.length)==debug) {
      keystrokes='';
      $('.debug').toggle();
    };
  });
  
});