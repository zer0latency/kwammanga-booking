
var myMap;
ymaps.ready(init);
var kwmmbItems = [];
var currentItem = null;
var ajax_url    = '/wp-admin/admin-ajax.php';

var refresh_table = function (from_server) {
  if(from_server) {
    kwmmb_loading(true);
    jQuery.ajax({
      type: "POST",
      url:  ajax_url,
      data: 'action=kwmmb_items_get',
      success:  function (data) {
        kwmmbItems = data;
        kwmmb_loading(false);
        refresh_table(false);
        myMap && refresh_map();
      },
      error: function () {
        kwmmb_loading(false);
      }
    });
  } else {
    var adminTable = jQuery('table.kwmmb-admin-table');
    adminTable.find('tr').not('.table-header').remove();

    jQuery.each(kwmmbItems, function (i, elem) {
      adminTable.append(render_template('kwmmb_admin_row', elem));
    });
  }
};

function init() {
  myMap = new ymaps.Map("ya-map", {
            center: [44.808763, 37.370311],
            zoom: 9
  });
  
  refresh_map();
}

function refresh_map() {
  myMap.geoObjects.removeAll();
  jQuery.each(kwmmbItems, function (i, elem) {
    elem.map_object = new ymaps.Placemark([elem.latitude, elem.longitude], {
            balloonContent: render_template('kwmmb_baloon', elem),
            iconContent: elem.name
        }, {
            preset: "islands#greenStretchyIcon"
        });
    myMap.geoObjects.add(elem.map_object);
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
    kwmmb_loading(true);
    
    var postString = $('.kwmmb-item-form')
        .serialize()
        .replace(/item_/g,'')
        .split('&');
        
    
    if (currentItem === null) {
      postString.push("action="+"kwmmb_item_create");
    } else {
      postString.push("action="+"kwmmb_item_set");
      postString.push("id="+currentItem.id);
      currentItem = null;
      jQuery('.kwmmb-field input').val('');
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
  params = params || [];
  jQuery.each(params, function (i, elem) {
      var regex = new RegExp('{'+i+'}', 'gm');
      template_content = template_content.replace(regex, params[i]);
  });
  
  return template_content;
};

jQuery('.kwmmb-admin-table').on('click','.kwmmb-table-actions a', function () {
  location.hash = jQuery(this).attr('href');
  var action = /#(\w+)\//g.exec(location.hash)[1];
  var item_id = /#\w+\/(\d+)/g.exec(location.hash)[1];
  if (action && item_id) {
    switch (action) {
      case 'remove':
        kwmmb_item_remove(item_id);
        break;
      case 'edit':
        kwmmb_item_edit(item_id);
        break;
      default:
        console.log('Action: '+action, '| Id: '+id);
        break;
    }
  }
});

function kwmmb_item_remove(id) {
  kwmmb_loading(true);
  jQuery.ajax({
    type: "POST",
    url:  ajax_url,
    data: 'action=kwmmb_item_remove&item_id='+id+'&_ajax_nonce='+jQuery('#kwmmb_ajax_nonce').val(),
    success:  function (data) {
      kwmmb_loading(false);
      refresh_table(true);
    },
    error: function () {
      kwmmb_loading(false);
    }
  });
}

function kwmmb_item_edit(id) {
  jQuery.each(kwmmbItems, function (i, item) {
    if (item.id === id) {
      currentItem = item;
      currentItem.map_object.balloon.autoPan();
      currentItem.map_object.balloon.open();
      jQuery.each(item, function (j, field) {
        jQuery('#item_'+j).val(field);
      });
    }
  });
}

function kwmmb_loading(status) {
  if (status) {
    if (jQuery('.kwmmb-loading-backdrop').length === 0) {
      jQuery('body').append(render_template('kwmmb_loading'));
    }
  } else {
    jQuery('.kwmmb-loading-backdrop').remove();
  }
}