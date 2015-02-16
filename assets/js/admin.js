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

//ymaps.ready(init);
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
            .append('<tr data-id="{0}"><td>{1}</td><td>{2}</td><td>{3}</td><td>{4}</td><td>{5}</td></tr>'.format(
              elem.id,
              elem.name,
              elem.price,
              elem.price_full,
              elem.roominess,
              'Actions'
            ));
  });
  
};

function init() {
  myMap = new ymaps.Map("ya-map", {
            center: [44.808763, 37.370311],
            zoom: 9
  });
}

jQuery(function () {
  var $ = jQuery;
  refresh_table(true);
  refresh_table(false);
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
        location.reload();
      }
    );

  });
});

