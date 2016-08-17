(function() {
  "use strict";
  jQuery(function() {  
    var data = { 
      'action': 'likeme',
      'ip': likeme.user_ip
    };    

    jQuery('#likeme-btn').click(function() {
      jQuery.post(likeme.ajax_url, data, function(response) {
        console.info(response);
        likeme.totallikes = response;
        jQuery('#totallike').html(likeme.totallikes);
      });
    });

    jQuery('#totallike').html(likeme.totallikes);
  });  
}());