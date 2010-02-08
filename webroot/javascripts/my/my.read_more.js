/**
 * jQuery ReadMore Plug-In
 *
 * @author Philip Blyth (philip dot blyth at gmail)
 * @url http://www.nemodreaming.com
 * @requires jquery.truncate.js, jquery.livequery.js
*/
(function($) {

  // --------------------------------------------------
  // PLUG-IN
  // --------------------------------------------------

  $.fn.read_more = function (options) {

    // DEFAULTS
    // --------------------------------------------------

    options = $.extend({
      label: 'more',
      truncate: 256,
      ellipsis: '... ',
      prefix: '&raquo; ',
      suffix: null,
      inclusive: false,
      precise: false
    }, options || {});

    // BUILD
    // --------------------------------------------------

    // link
    var link_obj = $('<a/>')
      .addClass('jquery-read_more-link')
      .html(options.label)
      .attr('href', '#read_more');

    // control
    var control_obj = $('<span/>')
      .addClass('jquery-read_more-control')
      .append(options.ellipsis)
      .append(options.prefix)
      .append(link_obj)
      .append(options.suffix);

    // SAVE
    // --------------------------------------------------

    // this
    var self = this;

    // full (original) html
    var full_html = $(this).html();

    // short html
    var short_html = $(this)
      .truncate(
        // - inclusive?
        options.inclusive ? options.truncate - control_obj.text().length : options.truncate,
        // - options
        {
          precise: options.precise
        }
      )
      .html(); // truncate changes the actual html

    // --------------------------------------------------
    // BINDINGS
    // --------------------------------------------------

    link_obj.click(function (e) {
      showHide(true);
      e.preventDefault();
      return false;
    });

    // --------------------------------------------------
    // INIT
    // --------------------------------------------------

    showHide(false);

    // FUNCTIONS
    // --------------------------------------------------

    function showHide (onOff) {
      if (onOff) {
        // original overflow?
        var overflow = self.css('overflow');
        // start height
        var start_height = self.height();
        // fill
        self
          .addClass('expanded')
          .removeClass('collapsed')
          .html(
            // html is wrapped in overflow:hidden div
            // so that parent-height reflects contents height + content padding/margins
            $('<div/>')
              .css('overflow', 'hidden')
              .html(full_html)
          );

        // end height
        var end_height = self.height();
        // animate!
        self
          .height(start_height)
          .css('overflow', 'hidden')
          .animate(
            {
              height: end_height
            },
            'fast',
            function(){
              // reset overflow
              self.css('overflow', overflow);
            }
          );
      } else {
        self
          .addClass('collapsed')
          .removeClass('expanded')
          .css('overflow', 'hidden')
          .html(short_html);
        var last = (c=self.children('p')) ? c.eq(c.length-1) : self;
        last.append(control_obj);
      };
    }
  };



  // --------------------------------------------------
  // READY!
  // --------------------------------------------------

  $('[rel*="read_more"]').livequery(function () {
    var self = $(this);
    // get truncate
    var truncate = (truncate = self.attr('rel').match(/read_more\|(\d+)/)) ? truncate[1] : 256;
    // necessary?
    if (self.text().length > truncate) {
      // do!
      self.read_more({
        // inclusive: true,
        // precise: true,
        truncate: truncate
      });
    };
  });

})(jQuery);
