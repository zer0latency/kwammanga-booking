/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var organize_price = 2500;
var food_price     = 500;

var myMap;
var places = [];
var ajax_url = '/wp-admin/admin-ajax.php';
var currentPlace = null;
var currentDays = [];

jQuery(function () {
  refreshPlaces();
  
  jQuery('#phone').change(function () {
    var filteredPhone = jQuery(this).val().replace(/[^\d]/g,'');
    jQuery('#phone').val(filteredPhone.toString());
  });
  
  jQuery('.kwmmb-item-form input').change(calculate_costs);
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
    showShortcuts: false,
    setValue: function (s) {
      this.value = s;
      currentDays = [
        s.split(' - ')[0],
        s.split(' - ')[1],
        (moment(s.split(' - ')[1], 'DD.MM.YYYY')-moment(s.split(' - ')[0], 'DD.MM.YYYY'))/(1000 * 3600 * 24)+1
      ];
      jQuery('#date_range').val(s);
      calculate_costs();
    }
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
    elem.map_object.events.add('click', function (event) {
      currentPlace = this;
      jQuery('#place').val(this.name);
      calculate_costs();
    }, elem);
    myMap.geoObjects.add(elem.map_object);
  });
}

function calculate_costs() {
  if(currentPlace === null)
    return;

  if(!currentDays.length)
    return;

  var adults      = parseInt(jQuery('#adults').val());
  var childs_0_5  = parseInt(jQuery('#childs_0_5').val());
  var childs_6_12 = parseInt(jQuery('#childs_6_12').val());

  var livingPrice = currentDays[2] === 7 
                  ? parseInt(currentPlace.price_full) 
                  : (parseInt(currentPlace.price) * currentDays[2]);

  var organizeCost = organize_price*adults;
  var livingCost   = livingPrice*(adults + childs_6_12 + 0.5*childs_0_5);
  var foodCost     = food_price*currentDays[2]*(adults + childs_6_12 + 0.5*childs_0_5);

  var fullCost     = organizeCost + livingCost + foodCost;


  jQuery('#cost-of-org').html(organizeCost.toString() + " рублей");
  jQuery('#cost-of-living').html(livingCost.toString() + " рублей");
  jQuery('#cost-of-food').html(foodCost.toString() + " рублей");
  jQuery('#cost-of-all').html(fullCost.toString() + " рублей");
}

function serialize_form() {
  return {
    
  };
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