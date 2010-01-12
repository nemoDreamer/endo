(function($) {

  $.fn.truncate = function( max, options ) {

    options = $.extend({
      precise: false,
      chars: /\s/
    }, options || {});

    return this.each( function() {
      var $this = $(this);
      var myStrOrig = $.compress($this.html());
      var myStr = myStrOrig;
      var myRegEx = /<\/?[^<>]*\/?>/gim;
      var myRegExArray;
      var myRegExHash = {};
      while ( ( myRegExArray = myRegEx.exec( myStr ) ) != null ) {
        myRegExHash[ myRegExArray.index ] = myRegExArray[ 0 ];
      }
      // console.log("myRegExHash:", myRegExHash);
      myStr = $.trim( myStr.split( myRegEx ).join( "" ) );
      // console.log("myStr:", myStr);
      if ( myStr.length > max ) {
        while ( max < myStr.length ) {
          if ( myStr.charAt( max ).match( options.chars ) || options.precise ) {
            myStr = myStr.substring( 0, max );
            break;
          }
          max--;
        }
        if ( myStrOrig.search( myRegEx ) != -1 ) {
          var endCap = 0;
          for ( eachEl in myRegExHash ) {
            myStr = [ myStr.substring( 0, eachEl ), myRegExHash[ eachEl ], myStr.substring( eachEl, myStr.length ) ].join( "" );
            if ( eachEl < myStr.length ) {
              endCap = myStr.length;
            }
          }
          $this.html(
            [
              myStr.substring( 0, endCap ),
              myStr.substring( endCap, myStr.length )
                .replace( /<(\w+)[^>]*>.*<\/\1>/gim, '' )
                .replace( /<(br|hr|img|input)[^<>]*\/?>/gim, '' )
            ].join( '' )
          );
        } else {
          $this.html( myStr );
        }
      }
    });
  };
  
  // --------------------------------------------------
  // EXTENSIONS
  // --------------------------------------------------

  $.compress = function(string){
    return $.trim(string).replace(/\s+/g, ' ');
  };

})(jQuery);