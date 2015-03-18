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
    bookings: function () { console.log("bookings"); },
    bookings_show: function (str_id) { console.log("bookings_show"); },
    booking_items: function () { console.log("booking_items"); },
    booking_items_new: function () { console.log("booking_items_new"); },
    booking_items_edit: function () { console.log("booking_items_edit"); },
    settings: function () { console.log("settings"); }
  });
  
  this.Booking = Backbone.Model.extend({
    urlRoot: "/wp-admin/admin-ajax.php?action=kwmmb_rest&route=bookings"
  });
  
  this.BookingCollection = Backbone.Collection.extend({
    url: "/wp-admin/admin-ajax.php?action=kwmmb_rest&route=bookings",
    model: self.Booking
  });
  
  return this;
})(jQuery);

jQuery(function () {
  new KwmmbAdmin.Router();
  Backbone.history.start();
});
