var KwmmbAdmin = (function ($, ymaps, _, Backbone) {
  Backbone.emulateHTTP = true;
  Backbone.emulateJSON = true;

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
    bookings_show:      function (str_id) { self.currentView.undelegateEvents(); self.currentView = new self.BookingView({model: new self.Booking({str_id: str_id})}); },
    booking_items:      function ()       { self.currentView.undelegateEvents(); self.currentView = new self.BookingItemsView(); },
    booking_items_new:  function ()       { self.currentView.undelegateEvents(); self.currentView = new self.BookingItemView({ model: new self.BookingItem() }); },
    booking_items_edit: function (id)     { self.currentView.undelegateEvents(); self.currentView = new self.BookingItemView({ model: new self.BookingItem({id: id}) }); },
    settings:           function ()       { self.currentView.undelegateEvents(); self.currentView = new self.SettingsView(); }
  });

  /**
   * The Booking model
   */
  this.Booking = Backbone.Model.extend({
    url: function () {
      var id = this.get('str_id')!=="" ? this.get('str_id') : this.get('id');
      return "/wp-admin/admin-ajax.php?action=kwmmb_rest&route=bookings/" + id;
    },
    showUrl: function () {
      return "#bookings/" + this.get('str_id');
    },
    getRoom: function () {
      return self.rooms.get(this.get('item'));
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
    },
    setPayed: function () {
      console.log(this.get('payed'), parseInt(this.get('payed')) ? 0 : 1);
      this.set('payed', parseInt(this.get('payed')) ? 0 : 1);
      this.save();
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
   * Room Model
   */
  this.Room = Backbone.Model.extend({
    urlRoot: "/wp-admin/admin-ajax.php?action=kwmmb_rest&route=rooms",
    getItem: function (callback) {
      this.item = new BookingItem({id: this.get('item_id') });
      this.item.fetch({ success: callback });
    }
  });

  /**
   * Room Collection
   */
  this.RoomCollection = Backbone.Collection.extend({
    url: "/wp-admin/admin-ajax.php?action=kwmmb_rest&route=rooms",
    model: self.Room,
    byItemId: function (item_id) {
      var filtered = this.filter(function (room) { return room.get('item_id') === item_id; });
      return new self.RoomCollection(filtered);
    }
  });

  /**
   * Room View
   */
  this.RoomView = Backbone.View.extend({
    tagName: "tr",
    template: _.template($('#template-room').html()),
    render: function () {
      $(this.el).html(this.template({ m: this.model }));
    },
    events: { "click .delete": "deleteRoom" },
    deleteRoom: function () {
      var view = this;
      this.model.destroy({
        success: function () {
          view.remove();
        }
      });
    }
  });

  /**
   * RoomCollection View
   */
  this.RoomCollectionView = Backbone.View.extend({
    el: 'table#rooms',
    template: _.template($('#template-rooms').html()),
    initialize: function () { this.render(); },
    render: function () {
      var rcView = this;
      this.$el.html(this.template({ m: this.model }));
      this.collection.byItemId(this.model.get('id')).each(function (room) {
        var roomView = new self.RoomView({ id: "room" + room.get('id'), model: room });
        rcView.$el.append(roomView.el);
        roomView.render();
      });
    },
    events: {
      "click button.create": "createRoom"
    },
    createRoom: function () {
      var view = this;
      var room = new self.Room({
        name: this.$('#room_name').val(),
        count: this.$("#room_count").val(),
        price: this.$('#room_price').val(),
        price_full: this.$('#room_price_full').val(),
        item_id: this.model.get('id')
      });
      room.save({},{
        success: function () {
          view.collection.push(room);
          view.render();
        }
      });
      this.render();
    }
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
      var model = this.booking_items.get(targetId);
      model.destroy({ success: function () { self.render(); } });
    }
  });

  this.rooms = new self.RoomCollection(rooms_prefill);

  /**
   * BookingItem View
   */
  this.BookingItemView = Backbone.View.extend({
    el: $('.wrap'),
    template: _.template($('#template-booking-item').html()),
    baloonTemplate: _.template($('#template-map-object').html()),
    initialize: function () {
      var biView = this;
      if (this.model.get('id')) {
        this.model.fetch({
          success: function () { biView.render(); }
        });
      } else { this.render(); }
    },
    render: function () {
      $(this.el).hide().html(this.template({ m: this.model })).fadeIn();
      var view = this;
      ymaps.ready(function () {
        view.map = new ymaps.Map("ya-map", {
          center: [44.808763, 37.370311],
          zoom: 10
        });
        view.fillMap();
        new self.RoomCollectionView({ collection: self.rooms, model: view.model });
      });
      return this;
    },
    events: {
      "change .booking-item input": "change",
      "click .save": "save"
    },
    change: function (e) {
      var target = e.target;
      this.model.set(target.id, target.value);
    },
    save: function () {
      var model = this.model;
      var toEdit = !(model.get('id'));
      model.save({}, {
        success: function () {
          self.router.navigate(
            toEdit ? "booking_items/"+model.get('id') : "booking_items",
            { trigger: true }
          );
        }
      });
    },
    fillMap: function () {
      this.map.geoObjects.removeAll();
      var points = this.model.get('points') === undefined ? [] : [JSON.parse(this.model.get('points'))];
      this.map_object = new ymaps.Polygon(points,
        { balloonContent: this.baloonTemplate({ m: this.model }) },
        { editorDrawingCursor: "crosshair" });
      this.map.geoObjects.add(this.map_object);
      if (points.length === 0) { this.map_object.editor.startDrawing(); }
        else { this.map_object.editor.startEditing(); }
      this.map_object.events.add(["geometrychange"], this.changeMap, this);
    },
    changeMap:  function () {
      this.model.set('points', JSON.stringify(this.map_object.geometry.get(0)));
      $('#points').val(JSON.stringify(this.map_object.geometry.get(0)));
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
    },
    events: {
      "click .delete": "deleteItem"
    },
    deleteItem: function (e) {
      var item_id = $(e.target).attr('data-id'),
          booking = this.bookings.get(item_id),
          view = this;
      booking.destroy({
        success: function () {
          view.render();
        }
      });
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
      this.model.on("sync", function () {
        self.render();
      });
      this.model.fetch();
    },
    render: function () {
      var self = this;
      if (!this.room) {
        this.room = this.model.getRoom();
        this.room.getItem(function () { self.render(); });
        return;
      }
      console.log(this.model);
      $(this.el).hide().html(this.template({ booking: this.model, room: this.room })).fadeIn();
      return this;
    },
    events: {
      "click .setPayed": function () {
        this.model.setPayed();
      }
    }
  });

  $(function () {
    self.router = new self.Router();
    Backbone.history.start();
  });

  $(document).ajaxStart(function () {
    $('#wp-admin-bar-root-default').append(
      _.template($('#template-loading').html())()
    );
  });

  $(document).ajaxStop(function () {
    $('#wp-admin-bar-root-default .kwmmb-loading').remove();
  });

  return this;
})(jQuery, ymaps, _, Backbone);
