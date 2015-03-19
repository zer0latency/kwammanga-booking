Backbone.emulateHTTP = true;
Backbone.emulateJSON = true;

var KwmmbAdmin = (function ($) {
  var self = this;

  this.Router = Backbone.Router.extend({
    routes: {
      "": "bookings",
      "bookings":          "bookings",
      "bookings/:str_id":  "bookings_show",
      "booking_items":     "booking_items",
      "booking_items/new": "booking_items_new",
      "booking_items/:id": "bookings_items_edit",
      "settings":          "settings"
    },
    bookings: function () { new self.BookingsView(); },
    bookings_show: function (str_id) {var model = new self.Booking({id: str_id}); new self.BookingView({model: model}); },
    booking_items: function () { console.log("booking_items"); },
    booking_items_new: function () { console.log("booking_items_new"); },
    booking_items_edit: function () { console.log("booking_items_edit"); },
    settings: function () { new self.SettingsView(); }
  });

  this.Booking = Backbone.Model.extend({
    urlRoot: "/wp-admin/admin-ajax.php?action=kwmmb_rest&route=bookings",
    showUrl: function () {
      return "#bookings/" + this.get('str_id');
    }
  });

  this.BookingItem = Backbone.Model.extend({
    urlRoot: "/wp-admin/admin-ajax.php?action=kwmmb_rest&route=booking_items"
  });

  this.BookingCollection = Backbone.Collection.extend({
    url: "/wp-admin/admin-ajax.php?action=kwmmb_rest&route=bookings",
    model: self.Booking
  });

  this.SettingsView = Backbone.View.extend({
    el: $('.wrap'),
    template: _.template($('#template-settings').html()),
    initialize: function () {
      this.render();
    },
    render: function () {
      $(this.el).hide().html(this.template()).fadeIn();
      return this;
    }
  });

  this.BookingsView = Backbone.View.extend({
    el: $('.wrap'),
    template: _.template($('#template-bookings').html()),
    initialize: function () {
      var bView = this;
      this.bookings = new self.BookingCollection();
      this.bookings.fetch({
        success: function () { bView.render(); }
      });
    },
    render: function () {
      $(this.el).hide().html(this.template({ bookings: this.bookings })).fadeIn();
      return this;
    }
  });

  this.BookingView = Backbone.View.extend({
    el: $('.wrap'),
    template: _.template($('#template-booking').html()),
    initialize: function () {
      var self   = this;
      console.log("Searching "+this.model.get('str_id'));
      this.model.fetch({ success: function () { self.render(); }});
    },
    render: function () {
      $(this.el).hide().html(this.template({ booking: this.model })).fadeIn();
      return this;
    }
  });

  jQuery(function () {
    self.router = new self.Router();
    Backbone.history.start();
  });

  return this;
})(jQuery);
