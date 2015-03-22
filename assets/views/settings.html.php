<?php wp_enqueue_style('kwmmb-admin', KwmmbAssetic::get('stylesheet', 'admin')) ?>
<?php wp_enqueue_script('ymaps','http://api-maps.yandex.ru/2.1/?lang=ru_RU'); ?>
<?php wp_enqueue_script('kwmmb-admin', KwmmbAssetic::get('script', 'admin2'), array('jquery', 'ymaps', 'backbone'), '', true) ?>

<script>
  var rooms_prefill = <?= json_encode(KwmmbDb::select("kwmmb_rooms")) ?>;
</script>
<div class="wrap">

</div>

<script type="text/html" id="template-map-object">
    <h3><%= m.get('name') %></h3>
    <p><%= m.get('description') %></p>
    <ul>
      <% KwmmbAdmin.rooms.byItemId(m.get('id')).each(function (room) { %>
      <li><%= room.get('name') %>: <%= room.get('count') %> (<strong><%= room.get('price') %> руб. за человека</strong>)</li>
      <% }); %>
    </ul>
</script>

<script type="text/html" id="kwmmb_admin_row">
  <tr>
    <td>{name}</td>
    <td>{tents_count}</td>
    <td>{standards_count}</td>
    <td>{comforts_count}</td>
    <td>{ecolux_count}</td>
    <td class="kwmmb-table-actions">
      <a href="#edit/{id}">Изменить</a>
      <a href="#remove/{id}">Удалить</a>
    </td>
  </tr>
</script>

<script type="text/html" id="template-loading">
  <li class="kwmmb-loading">
    <img src="<?= KwmmbAssetic::get('animation', 'loading') ?>" alt="Загрузка...">
  </li>
</script>

<!-- Room Template -->
<script type='text/html' id="template-room">
  <td><%= m.get('name') %></td>
  <td><%= m.get('count') %></td>
  <td><%= m.get('price') %></td>
  <td><%= m.get('price_full') %></td>
  <td><a class="delete" style="cursor: pointer">Удалить</a></td>
</script>

<!-- RoomCollection Template -->
<script type='text/html' id="template-rooms">
  <% if (m.get('id')) { %>
  <tr class="table-header"><th>Название</th><th>Кол-во</th><th>Цена</th><th>Ц. за 7 дней</th><th>Действия</th></tr>
  <tr class="room-new">
    <td><input type="text" id="room_name" /></td>
    <td><input type="number" id="room_count" /></td>
    <td><input type="number" id="room_price" /></td>
    <td><input type="number" id="room_price_full" /></td>
    <td><button class="create">Добавить</button></td>
  </tr>
  <% } else { %>
  <tr><td>Для добавления номеров сохраните базу.</td></tr>
  <% } %>
</script>

<!-- BookingItem template -->
<script type="text/html" id="template-booking-item">
  <div class="col-50">
    <table class='booking-item'>
      <tr><td>Название: </td><td><input type="text" id="name" value="<%= m.get('name') %>"/></td></tr>
      <tr><td>Описание: </td><td><input type="text" id="description" value="<%= m.get('description') %>"/></td></tr>
      <tr><td>Вершины:  </td><td><input type="text" id="points" value="<%= m.get('points') %>" readonly="readonly"/></td></tr>
    </table>
    <h3>Номера:</h3>
    <table id="rooms" class="kwmmb-admin-table"></table>
  </div>
  <div class="col-50">
    <div class='kwmmb-admin-map' id='ya-map'></div>
  </div>
  <button class="save">Сохранить</button> <a href="#booking_items">Назад</a>
</script>

<!-- BookingItems template -->
<script type="text/html" id="template-booking-items">
  <ul class="kwmmb-admin-menu">
    <li><a href="#bookings">Заказы</a></li>
    <li class="active"><a href="#booking_items">Базы</a></li>
    <li><a href="#settings">Настройки</a></li>
  </ul>
  <table class='kwmmb-admin-table'>
    <tr class='table-header'><th>Название</th><th>Описание</th><th>Действия</th></tr>
    <% items.each(function (i) { %>
    <tr>
      <td><%= i.get('name') %></td>
      <td><%= i.get('description') %></td>
      <td><a href='<%= i.editUrl() %>'>изменить</a> | <a data-id="<%= i.get('id') %>" class="remove" '>удалить</a></td>
    </tr>
    <% }) %>
  </table>
  <a href="#booking_items/new">Добавить</a>
</script>

<!-- Bookings template -->
<script type="text/html" id="template-bookings">
  <ul class="kwmmb-admin-menu">
    <li class="active"><a href="#bookings">Заказы</a></li>
    <li><a href="#booking_items">Базы</a></li>
    <li><a href="#settings">Настройки</a></li>
  </ul>
  <table class='kwmmb-admin-table'>
    <tr class='table-header'><th>Ид</th><th>Email</th><th>Имя</th><th>Телефон</th><th>Дата с</th><th>Дата по</th><th>Действия</th></tr>
  <% bookings.each(function (e) { %>
      <tr class="<%= e.get('verified') === "0" ? "" : "verified" %>">
        <td><%= e.get('str_id') %></td>
        <td><%= e.get('email') %></td>
        <td><%= e.get('name') %></td>
        <td><%= e.get('phone') %></td>
        <td><%= e.get('date_start') %></td>
        <td><%= e.get('date_end') %></td>
        <td><a href='<%= e.showUrl() %>'>просмотр</a> | <a class="delete" data-id="<%= e.get('id') %>" style="cursor: pointer">удалить</a></td>
      </tr>
  <% }) %>
  </table>
</script>

<!-- Booking show template -->
<script type="text/html" id="template-booking">
  <ul class="kwmmb-admin-menu">
    <li><a href="#bookings">Заказы</a></li>
    <li><a href="#booking_items">Базы</a></li>
    <li><a href="#settings">Настройки</a></li>
  </ul>
  <ul>
      <li>Ид: <strong><%= booking.get('str_id') %></strong></li>
      <li>E-Mail: <strong><%= booking.get('email') %></strong></li>
      <li>Имя: <strong><%= booking.get('name') %></strong></li>
      <li>Телефон: <strong><%= booking.get('phone') %></strong></li>
      <li>Дата с: <strong><%= booking.get('date_start') %></strong></li>
      <li>Дата по: <strong><%= booking.get('date_end') %></strong></li>
      <li>Взрослых: <strong><%= booking.get('adults') %></strong></li>
      <li>Детей: <strong><%= booking.get('child_0_5') %></strong></li>
      <li>Подростков: <strong><%= booking.get('child_6_12') %></strong></li>
      <li>Подтвержден: <strong><%= booking.get('verified') === "0" ? "Нет" : "Да" %></strong></li>
      <li>Место: <strong><%= "" %></strong></li>
  </ul>
</script>

<!-- Settings template -->
<script type="text/html" id="template-settings">
  <ul class="kwmmb-admin-menu">
    <li><a href="#bookings">Заказы</a></li>
    <li><a href="#booking_items">Базы</a></li>
    <li class="active"><a href="#settings">Настройки</a></li>
  </ul>
  <form method="post" action="options.php">
    <?php settings_fields( 'kwmmb' ); ?>
    <?php do_settings_sections( 'kwmmb' ); ?>
    <table class="form-table">
        <tr><th colspan="2">Настройки SMS-aero</th></tr>
        <tr valign="top">
            <td scope="row">Имя польз.</td>
            <td><input type="text" name="smsaero_user" value="<?= esc_attr( get_option('smsaero_user') ); ?>" /></td>
        </tr>
        <tr valign="top">
            <td scope="row">Пароль</td>
            <td><input type="text" name="smsaero_password" value="<?= esc_attr( get_option('smsaero_password') ); ?>" /></td>
        </tr>
        <tr valign="top">
            <td scope="row">Отправитель</td>
            <td><input type="text" name="smsaero_sender" value="<?= esc_attr( get_option('smsaero_sender') ); ?>" /></td>
        </tr>
        <tr><th colspan="2">Настройки карты</th></tr>
        <tr valign="top">
            <td scope="row">Широта центра</td>
            <td><input type="text" name="map_latitude" value="<?= esc_attr( get_option('map_latitude') ); ?>" /></td>
        </tr>
        <tr valign="top">
            <td scope="row">Долгота центра</td>
            <td><input type="text" name="map_longitude" value="<?= esc_attr( get_option('map_longitude') ); ?>" /></td>
        </tr>
        <tr><th colspan="2">Цены</th></tr>
        <tr valign="top">
            <td scope="row">Орг. взнос</td>
            <td><input type="text" name="price_org" value="<?= esc_attr( get_option('price_org') ); ?>" /></td>
        </tr>
    </table>
    <?php submit_button(); ?>
  </form>
</script>