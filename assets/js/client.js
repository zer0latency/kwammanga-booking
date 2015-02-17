/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var myMap;
var places = [];
var ajax_url = '/wp-admin/admin-ajax.php';

jQuery(function () {
  refreshPlaces();
});

jQuery(function () {
  jQuery('.daterange-picker').dateRangePicker({
    format: 'DD.MM.YYYY',
    separator: ' - ',
    language: 'ru',
    startOfWeek: 'monday',
    startDate: new Date('2015-07-06'),
    endDate: new Date('2015-07-13'),
    minDays: 1,
    maxDays: 8,
    showShortcuts: false
  });
});

function init() {
  myMap = new ymaps.Map("ya-map", {
            center: [44.808763, 37.370311],
            zoom: 9
  });
  
  refreshMap();
}

function refreshPlaces() {
  jQuery.ajax({
    type: "POST",
    url:  ajax_url,
    data: 'action=kwmmb_items_get',
    success:  function (data) {
      places = data;
      myMap && refreshMap();
    },
    error: function () {
      kwmmb_loading(false);
    }
  });
}

function refreshMap() {
  myMap.geoObjects.removeAll();
  jQuery.each(places, function (i, elem) {
    elem.map_object = new ymaps.Placemark([elem.latitude, elem.longitude], {
            balloonContent: render_template('kwmmb_baloon', elem),
            iconContent: elem.name
        }, {
            preset: "islands#greenStretchyIcon"
        });
    myMap.geoObjects.add(elem.map_object);
  });
}

var render_template = function (template_id, params) {
  var template_content = jQuery('#'+template_id).html();
  params = params || [];
  jQuery.each(params, function (i, elem) {
      var regex = new RegExp('{'+i+'}', 'gm');
      template_content = template_content.replace(regex, params[i]);
  });
  
  return template_content;
};

ymaps.ready(init);