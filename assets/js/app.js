var KwmmbApp = (function ($, _, Backbone, ymaps) {
  Backbone.emulateJSON = true;
  Backbone.emulateHTTP = true;

  var self = this;

  this.bindRangePicker = function () {
    jQuery('.daterange-picker').dateRangePicker({
      format: 'DD.MM.YYYY',
      separator: ' - ',
      language: 'ru',
      startOfWeek: 'monday',
      startDate: new Date('2015-06-06'),
      endDate: new Date('2015-06-13'),
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

  var KwmmbModel = Backbone.Model.extend({

  });

    /**
   * Room Model
   */
  var Room = Backbone.Model.extend({
    urlRoot: "/wp-admin/admin-ajax.php?action=kwmmb_rest&route=rooms"
  });

  /**
   * Room Collection
   */
  var RoomCollection = Backbone.Collection.extend({
    url: "/wp-admin/admin-ajax.php?action=kwmmb_rest&route=rooms",
    model: Room,
    byItemId: function (item_id) {
      var filtered = this.filter(function (room) { return room.get('item_id') === item_id; });
      return new RoomCollection(filtered);
    }
  });

  var BookingItem = KwmmbModel.extend({
    url: "/wp-admin/admin-ajax.php?action=kwmmb_booking",
    defaults: {
      "points": []
    }
  });

  var BookingItemsCollection = Backbone.Collection.extend({
    url: '/wp-admin/admin-ajax.php?action=kwmmb_booking_items',
    model: BookingItem
  });

  var Booking = KwmmbModel.extend({
    url: function () {
      if (this.get('str_id')) {
        return "/wp-admin/admin-ajax.php?action=kwmmb_pub&route=bookings/" + this.get('str_id');
      } else {
        return "/wp-admin/admin-ajax.php?action=kwmmb_pub&route=new_booking";
      }
    },
    defaults: {
      "id": null,
      "str_id":    "",
      "adults":     1,
      "child_0_5":  0,
      "child_6_12": 0,
      "food":       0,
      "date_start": new Date('2015-06-06'),
      "date_end": new Date('2015-06-13'),
      "verified": 0,
      "item": null,
      "comment": ""
    },
    getDays: function () {
      return (moment(this.get('date_end'))-moment(this.get('date_start')))/(1000 * 3600 * 24)+1;
    },
    getRange: function () {
      return moment(this.get('date_start')).format('DD.MM.YYYY') + ' - ' + moment(this.get('date_end')).format('DD.MM.YYYY');
    },
    isVerified: function () {
      return this.get('verified') === "1";
    },
    validate: function (attrs, options) {
      if (!attrs.name) {
        return { field: "name", error: "Заполните, пожалуйста, это поле" };
      }
      if (!attrs.email || !attrs.email.match(/.+@.+\..+/)) {
        return { field: "email", error: "Укажите корректный EMail" };
      }
      if (!attrs.phone || !attrs.phone.match(/[0-9]{11}/)) {
        return { field: "phone", error: "Укажите корректный номер телефона" };
      }
      if (!(parseInt(attrs.adults) > 0)) {
        return { field: "adults", error: "Должен быть как минимум один взрослый" };
      }
    }
  });

  var Code = KwmmbModel.extend({
    url: "/wp-admin/admin-ajax.php?action=kwmmb_pub&route=check_code"
  });

  var CodeView = Backbone.View.extend({
    el: 'div#code',
    template: _.template($('#template-code').html()),
    initialize: function () {
      this.render();
    },
    render: function () {
      this.$el.html(this.template({ m: this.model }));
    },
    events: {
      "change #code": "change",
      "click .accept": "saveCode"
    },
    change: function (e) {
      var target = e.target;
      this.model.set(target.id, target.value);
    },
    saveCode: function () {
      this.model.save({
        success: function () { self.bookingView.render(); }
      });
    }
  });

  var BookingView = Backbone.View.extend({
    el: jQuery('#wrap'),
    template: _.template(jQuery('#template-booking').html()),
    balloonTemplate: _.template(jQuery('#template-map-object').html()),
    items: new BookingItemsCollection(items_prefill),
    rooms: new RoomCollection(rooms_prefill),

    initialize: function () {
      var item = this.model.get('item');
      if (!item) { this.model.set({ item: this.rooms.first() }); }
      if (parseInt(item)) { this.model.set({ item: this.rooms.get(item) }); }
      this.currentItem = this.items.get(this.model.get('item').get('item_id'));
      this.model.bind("change", this.render, this);
      this.model.bind('invalid', this.invalid, this);
      this.render();
    },

    render: function () {
      var item = this.model.get('item');
      if (parseInt(item)) { this.model.set({ item: this.rooms.get(item) }); return; }
      jQuery(this.el).html(this.template({ m: this.model, costs: this.costs(), currentItem: this.currentItem, items: this.items}));
      this.renderMap();
      if (!this.model.get('id')) {
        return this;
      }
      if (this.model.get("verified") === "0") {
        this.code = new Code({booking_id: this.model.get('id')});
        this.codeView = new CodeView({ model: this.code });
      }
      return this;
    },

    invalid: function (e) {
      var err = e.validationError;
      var $el = $(this.el);
      $el.find("#"+err.field).css("borderColor", "#ff9999").after("<small class=\"error-msg\">"+err.error+"</small>");
      console.log(e);
    },

    renderMap: function () {
      var view = this;
      self.bindRangePicker();
      ymaps.ready(function () {
          if (jQuery('#ya-map *').length > 0) { return; }
          var myMap = new ymaps.Map("ya-map", { center: JSON.parse(view.currentItem.get('points'))[0], zoom: 12 });
          view.items.each(function (el) {
              var poly = new ymaps.Polygon([JSON.parse(el.attributes.points)], {
                balloonContent: view.balloonTemplate({ m: el })
              });
              myMap.geoObjects.add(poly);
          });
      });
    },

    events: {
      "change form input": "change",
      "click select option": "selectItem",
      "click .kwmmb-item-submit": "saveBooking",
      "click .select-item": "selectItem"
    },

    change: function (event) {
      var target = event.target;
      console.log(target.name, target.value);
      this.model.set(target.name, target.value);
    },

    selectItem: function (e) {
      var item_id = $(e.target).attr('data-id');
      this.currentItem = this.items.get(item_id);
      this.render();
    },

    saveBooking: function () {
      var view = this;
      this.model.save({}, {
        success: function (data) {
          self.router.navigate("bookings/"+view.model.get('str_id'), { trigger: true });
        }
      });
    },

    costs: function () {
      var m = this.model;
      var days = (moment(m.get('date_end'))-moment(m.get('date_start')))/(1000 * 3600 * 24)+1;
      var peoples = parseInt(m.get('adults')) + parseInt(m.get('child_6_12')) + parseInt(m.get('child_0_5'))*0.5;

      return {
        org: price_org*parseInt(m.get('adults')),
        live: days > 6
                    ? m.get('item').get('price_full')*peoples
                    : m.get('item').get('price')*days*peoples,
        food:  days > 6
                    ? 4200*parseInt(m.get('food'))
                    : 600*days*parseInt(m.get('food'))
      };
    }
  });

  self.rooms = new RoomCollection(rooms_prefill);
  self.booking_items = new BookingItemsCollection(items_prefill);
  var Router = Backbone.Router.extend({
    routes: {
      "": "newBooking",
      "bookings/:str_id": "editBooking"
    },

    newBooking: function () {
      self.booking = new Booking();
      self.bookingView = new BookingView({ model: self.booking });
    },

    editBooking: function (str_id) {
      self.booking = new Booking({ str_id: str_id });
      booking.fetch({
        success: function () {
          self.bookingView = new BookingView({ model: self.booking });
        }
      });
    }
  });


  $(function () {
    self.router = new Router();
    Backbone.history.start();
  });

  return this;
})(jQuery, _, Backbone, ymaps);