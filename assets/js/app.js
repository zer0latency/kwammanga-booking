Backbone.emulateHTTP = true;
Backbone.emulateJSON = true;

var KwmmbModel = Backbone.Model.extend({
  /*
  sync: function () {
    console.log("Saving to database");
  }
  */
});

var BookingItem = KwmmbModel.extend({
  defaults: {
    "id": null,
    "name": "",
    "description": "",
    "tents_count": null,
    "standards_count": null,
    "comforts_count": null,
    "ecolux_count": null,
    "points": []
  }
});

var BookingItemsCollection = Backbone.Collection.extend({
  url: '/wp-admin/admin-ajax.php?action=kwmmb_items_get',
  model: this.BookingItem
});

var Booking = KwmmbModel.extend({
  url: "/wp-admin/admin-ajax.php?action=kwmmb_item_set",
  defaults: {
    "id": null,
    "str_id": "",
    "comfort": "tent",
    "name": "",
    "email": "",
    "phone": "",
    "adults": 0,
    "child_0_5": 0,
    "child_6_12": 0,
    "date_start": new Date('2015-07-06'),
    "date_end": new Date('2015-07-13'),
    "item": null,
    "comment": ""
  }
});

var BookingView = Backbone.View.extend({
  el: jQuery('#wrap'),
  template: _.template(jQuery('#template-booking').html()),
  items: new BookingItemsCollection(bookingItems),

  initialize: function () {
    this.model = new Booking({ item: this.items.get('c1') });
    this.model.bind("change", this.render, this);
    this.render();
  },

  render: function (eventName) {
    console.log("Rendering", this.model.toJSON());
    jQuery(this.el).html(this.template(this.model.toJSON()));

    return this;
  },

  events: {
    "change input": "change",
    "click .kwmmb-item-submit": "saveBooking",
    "click .kwmmb-places-table a": "changeComfort"
  },

  change: function (event) {
    var target = event.target;
    console.log("Changing", target.id, "from", target.defaultValue, "to", target.value);
    this.model.set(target.id, target.value);
  },

  changeComfort: function (event) {
    var comfort = jQuery(event.target).attr('data-id');
    this.model.set('comfort', comfort);
  },

  saveBooking: function () {
    this.model.save();
  }
});
  
var Application = (function ($) {
  var self = this;
  
  this.bookingView = new BookingView();
  
  this.bindRangePicker = function () {
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
        self.bookingView.model.set({
          "date_start": moment(s.split(' - ')[0], 'DD.MM.YYYY').toDate(),
          "date_end": moment(s.split(' - ')[1], 'DD.MM.YYYY').toDate()
        });
      }
    });
  };

  return this;
})(jQuery);