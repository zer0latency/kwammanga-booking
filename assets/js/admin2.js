Backbone.emulateHTTP = true;
Backbone.emulateJSON = true;

var KwmmbAdmin = (function ($) {
  var self = this;

  this.currentView = {undelegateEvents: function () {}};
  /**
   * Router of our application
   */
  this.Router = Backbone.Router.extend({
    routes: {
      "": "bookings",
      "bookings":          "bookings",
      "bookings/:str_id":  "bookings_show",
      "booking_items":     "booking_items",
      "booking_items/new": "booking_items_new",
      "booking_items/:id": "booking_items_edit",
      "settings":          "settings"
    },
    bookings:           function ()       { self.currentView.undelegateEvents(); self.currentView = new self.BookingsView(); },
    bookings_show:      function (str_id) { self.currentView.undelegateEvents(); self.currentView = new self.BookingView({model: new self.Booking({id: str_id})}); },
    booking_items:      function ()       { self.currentView.undelegateEvents(); self.currentView = new self.BookingItemsView(); },
    booking_items_new:  function ()       { self.currentView.undelegateEvents(); self.currentView = new self.BookingItemView({ model: new self.BookingItem() }); },
    booking_items_edit: function (id)     { self.currentView.undelegateEvents(); self.currentView = new self.BookingItemView({ model: new self.BookingItem({id: id}) }); },
    settings:           function ()       { self.currentView.undelegateEvents(); self.currentView = new self.SettingsView(); }
  });

  /**
   * The Booking model
   */
  this.Booking = Backbone.Model.extend({
    urlRoot: "/wp-admin/admin-ajax.php?action=kwmmb_rest&route=bookings",
    showUrl: function () {
      return "#bookings/" + this.get('str_id');
    }
  });

  /**
   * The BookingItem model
   */
  this.BookingItem = Backbone.Model.extend({
    urlRoot: "/wp-admin/admin-ajax.php?action=kwmmb_rest&route=booking_items",
    editUrl: function () {
      return "#booking_items/" + this.get('id');
    }
  });

  /**
   * Bookings Collection
   */
  this.BookingCollection = Backbone.Collection.extend({
    url: "/wp-admin/admin-ajax.php?action=kwmmb_rest&route=bookings",
    model: self.Booking
  });

  /**
   * BookingItems Collection
   */
  this.BookingItemCollection = Backbone.Collection.extend({
    url: "/wp-admin/admin-ajax.php?action=kwmmb_rest&route=booking_items",
    model: self.BookingItem
  });
  
  /**
   * BookingItems View
   */
  this.BookingItemsView = Backbone.View.extend({
    el: $('.wrap'),
    template: _.template($('#template-booking-items').html()),
    initialize: function () {
      var biView = this;
      this.booking_items = new self.BookingItemCollection();
      this.booking_items.fetch({
        success: function () { biView.render(); }
      });
    },
    render: function () {
      $(this.el).hide().html(this.template({ items: this.booking_items })).fadeIn();
    },
    events: {
      "click .remove": "removeItem"
    },
    removeItem: function (event) {
      var self = this;
      var targetId = $(event.target).attr('data-id');
      console.log("Deleting BookingItem #"+targetId);
      var model = this.booking_items.get(targetId);
      model.destroy({ success: function () { self.render(); } });
    }
  });
  
  /**
   * BookingItem View
   */
  this.BookingItemView = Backbone.View.extend({
    el: $('.wrap'),
    template: _.template($('#template-booking-item').html()),
    initialize: function () {
      var biView = this;
      console.log("Navigating to booking item");
      if (this.model.get('id')) {
        this.model.fetch({
          success: function () { biView.render(); }
        });
      } else { this.render(); }
    },
    render: function () {
      $(this.el).hide().html(this.template({ m: this.model })).fadeIn();
      return this;
    },
    events: {
      "change input": "change",
      "click .save": "save"
    },
    change: function (e) {
      var target = e.target;
      console.log("Changing" + target.id + " to " + target.value);
      this.model.set(target.id, target.value);
    },
    save: function () {
      this.model.save({}, {
        success: function () { self.router.navigate("booking_items", { trigger: true }); }
      });
    }
  });

  /**
   * Settings View (wp_options)
   */
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

  /**
   * Bookings View
   */
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

  /**
   * Single Booking View
   */
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
