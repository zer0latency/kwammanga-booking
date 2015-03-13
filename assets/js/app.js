jQuery( function () {
Backbone.emulateHTTP = true;
Backbone.emulateJSON = true;

var prices = {
    org: price_org || 2500,
    food:     { single:  600, full: 4000 },
    tent:     { single:  250, full: 1500 },
    standard: { single:  550, full: 3500 },
    comfort:  { single:  750, full: 5000 },
    ecolux:   { single: 1000, full: 7000 }
};

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
  url: "/wp-admin/admin-ajax.php?action=kwmmb_booking_validate",
  defaults: {
    "id": null,
    "str_id": "",
    "comfort": "tent",
    "name": "",
    "email": "",
    "phone": "",
    "adults": 1,
    "child_0_5": 0,
    "child_6_12": 0,
    "date_start": new Date('2015-07-06'),
    "date_end": new Date('2015-07-13'),
    "verified": 0,
    "item": null,
    "comment": ""
  }
});

var BookingView = Backbone.View.extend({
  el: jQuery('#wrap'),
  template: _.template(jQuery('#template-booking').html()),
  items: new BookingItemsCollection(bookingItems),

  initialize: function () {
    if (!this.model.get('item')) {
      this.model.set({ item: this.items.get('c1') });
    }
    this.model.bind("change", this.render, this);
    this.render();
  },

  render: function (eventName) {
    console.log("Rendering", {m: this.model.toJSON(), costs: this.costs()});
    jQuery(this.el).html(this.template({m: this.model.toJSON(), costs: this.costs()}));

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
    var self = this;
    this.model.save({}, {
      success: function (data) {
        console.log("saved", data);
      }
    });
  },
  
  costs: function () {
    var m = this.model;
    var days = (moment(m.get('date_end'))-moment(m.get('date_start')))/(1000 * 3600 * 24)+1;
    var peoples = parseInt(m.get('adults')) + parseInt(m.get('child_6_12')) + parseInt(m.get('child_0_5'))*0.5;

    return {
      org: prices.org*parseInt(m.get('adults')),
      live: days > 6 
                  ? prices[m.get('comfort')].full*peoples
                  : prices[m.get('comfort')].single*days*peoples,
      food:  days > 6 
                  ? prices.food.full*peoples
                  : prices.food.single*days*peoples
    };
  }
});

var Router = Backbone.Router.extend({
  routes: {
    "": "newBooking",
    "booking/:str_id": "editBooking"
  },
  
  newBooking: function () {
    var booking = new Booking();
    Application.bookingView = new BookingView({ model: booking });
  },
  
  editBooking: function (str_id) {
    var booking = new Booking({ str_id: str_id });
    booking.fetch();
    Application.bookingView = new BookingView({ model: booking });
  }
});

var Application = (function ($) {
  var self = this;
  
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
  
  $(function () {
    new Router();
    Backbone.history.start();
  });

  return this;
})(jQuery);
});