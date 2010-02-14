/**
 * jQuery FieldOptions Plug-In
 *
 * @author Philip Blyth (philip dot blyth at gmail)
 * @url http://www.nemodreaming.com
*/
(function($) {

  // --------------------------------------------------
  // PLUG-IN
  // --------------------------------------------------

  $.fn.field_options = function (options) {

    // DEFAULTS
    // --------------------------------------------------

    options = $.extend({
      parent_class: '.options',
      child_class: '.option'
    }, options || {});

    // BUILD
    // --------------------------------------------------

    // this
    var self = $(this);
    var field = self.attr('name');

    var container = $(options.parent_class+'[rel="'+field+'"]').eq(0);
    var options = container.find(options.child_class+'[rel]').remove();

    // --------------------------------------------------
    // BINDINGS
    // --------------------------------------------------

    self.change(function (e) {
      options.remove().filter('[rel='+self.val()+']').appendTo(container);
    }).trigger('change');

  };

})(jQuery);
