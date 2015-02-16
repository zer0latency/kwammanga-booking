// Simple string.format implementation
if (!String.prototype.format) {
  String.prototype.format = function() {
    var args = arguments;
    return this.replace(/{(\d+)}/g, function(match, number) { 
      return typeof args[number] != 'undefined'
        ? args[number]
        : match
      ;
    });
  };
}


ymaps.ready(init);
var myMap;
var kwmmbItems;
var currentItem = null;
var ajax_url    = '/wp-admin/admin-ajax.php';

var refresh_table = function (from_server) {
  from_server && jQuery.post(ajax_url, 'action=kwmmb_items_get', function (data) {
    kwmmbItems = data;
    refresh_table(false);
  });
  
  var adminTable = jQuery('table.kwmmb-admin-table');
  adminTable.find('tr').not('.table-header').remove();
  jQuery.each(kwmmbItems, function (i, elem) {
    adminTable
            .append(render_template('kwmmb_admin_row', elem));
  });
  
};

function init() {
  myMap = new ymaps.Map("ya-map", {
            center: [44.808763, 37.370311],
            zoom: 9
  });
  
  refresh_map();
}

function refresh_map() {
    jQuery.each(kwmmbItems, function (i, elem) {
        myMap.geoObjects.add(new ymaps.Placemark([elem.latitude, elem.longitude], {
                balloonContent: render_template('kwmmb_baloon', elem),
                iconContent: elem.name
            }, {
                preset: "islands#greenStretchyIcon",
            })
        );
    });
}

/**
 * 
 * @returns {undefined}
 */
jQuery(function () {
  var $ = jQuery;
  refresh_table(true);
  
  $('.kwmmb-item-submit').click(function (e) {
    e.preventDefault();
    
    var postString = $('.kwmmb-item-form')
        .serialize()
        .replace(/item_/g,'')
        .split('&');
        
    
    if (currentItem === null) {
      postString.push("action="+"kwmmb_item_create");
    } else {
      postString.push("action="+"kwmmb_item_set");
      postString.push("id="+currentItem.id);
    }
    
    $.post(
      ajax_url,
      postString.join('&'),
      function (data) {
        refresh_table(true);
      }
    );

  });
});

var render_template = function (template_id, params) {
  var template_content = jQuery('#'+template_id).html();
  jQuery.each(params, function (i, elem) {
      var regex = new RegExp('{'+i+'}', 'gm');
      template_content = template_content.replace(regex, params[i]);
  });
  
  return template_content;
};