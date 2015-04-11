<?php wp_enqueue_style('daterange-picker-css',     KwmmbAssetic::get('stylesheet', 'daterangepicker')) ?>
<?php wp_enqueue_style('kwmmb-css',                KwmmbAssetic::get('stylesheet', 'admin')) ?>
<?php wp_enqueue_script('moment',                  KwmmbAssetic::get('script', 'moment.min')) ?>
<?php wp_enqueue_script('jquery-daterange-picker', KwmmbAssetic::get('script', 'jquery.daterangepicker')) ?>
<?php wp_enqueue_script('ymaps',                   'http://api-maps.yandex.ru/2.1/?lang=ru_RU'); ?>
<?php wp_enqueue_script('app',                     KwmmbAssetic::get('script', 'app'), array('backbone'), '', true) ?>
<div class="wrap" id="wrap">

</div>

<script>
    // Bootstrap some models
    var rooms_prefill = <?= json_encode(KwmmbDb::select("kwmmb_rooms")) ?>;
    var items_prefill = <?= json_encode(KwmmbDb::select("kwmmb_booking_items")) ?>;
    // Inject options
    var price_org    = <?= get_option('price_org', 2500); ?>;
</script>

<script type="text/html" id="template-map-object">
    <h3><%= m.get('name') %></h3>
    <p><%= m.get('description') %></p>
    <ul>
      <% KwmmbApp.rooms.byItemId(m.get('id')).each(function (room) { %>
      <li><%= room.get('name') %>: <%= room.get('count') %> (<strong><%= room.get('price') %> руб. за человека</strong>)</li>
      <% }); %>
    </ul>
    <button data-id="<%= m.get('id') %>" class="select-item">Выбрать</button>
</script>

<script type="text/html" id="template-booking">
    <form class='kwmmb-item-form' name='kwmmb-item-form'>
        <input id="kwmmb_ajax_nonce" type='hidden' name="_ajax_nonce" value='<?= wp_create_nonce('kwmmb_user_nonce') ?>'>
        <p><b>Период участия:</b></p>
        <div class='kwmmb-field'>
          <label for="date_range">Когда:</label>
          <input value="<%= m.getRange() %>" class="daterange-picker" type='text' name='date_range' id="date_range" placeholder="Щелкните здесь..." readonly="readonly">
          <span class='kwmmb-field-value'></span>
        </div>
        <hr>
        <p><b>Доступные базы:</b></p>
        <div id="ya-map"></div>
        <div class="kwmmb-field">
          <label for="place">Выбранное место:</label>
          <select id="place" name="place">
            <% items.each(function (el) { %>
            <option data-id="<%= el.get('id') %>" <% if (el.get('id')===currentItem.get('id')) { %>selected="selected"<% } %>><%= el.get('name') %></option>
            <% }); %>
          </select>
        </div>
        <p><b>Тип размещения:</b></p>
        <div class="kwmmb-radio-group">
        <% KwmmbApp.rooms.byItemId(currentItem.get('id')).each(function (room) { %>
          <input <% if (room.get('id') === m.get('item').get('id')) { %>checked<% } %> type="radio" name="item" id="item<%= room.get('id') %>" value="<%= room.get('id') %>">
          <label for="item<%= room.get('id') %>"><%= room.get('name') %></label>
          <small><%= room.get('description') %></small>
        <% }); %>
        </div>
        <hr>
        <p><b>Нас:</b></p>
        <div class='kwmmb-field'>
          <label for="adults">Взрослых:</label>
          <input type='number' name='adults' id="adults" value="<%= m.get('adults') %>">
          <span class='kwmmb-field-value'></span>
        </div>
        <div class='kwmmb-field'>
          <label for="child_0_5">Детей 0-5:</label>
          <input type='number' name='child_0_5' id="child_0_5" value="<%= m.get('child_0_5') %>">
          <span class='kwmmb-field-value'></span>
        </div>
        <div class='kwmmb-field'>
          <label for="child_6_12">Детей 6-12:</label>
          <input type='number' name='child_6_12' id="child_6_12" value="<%= m.get('child_6_12') %>">
          <span class='kwmmb-field-value'></span>
        </div>
        <div class='kwmmb-field'>
          <label for="food">Питание:</label>
          <input type='number' name='food' id="food" value="<%= m.get('food') %>">
          <span class='kwmmb-field-value'></span>
        </div>
        <hr>
        <p>
          <b style="display: block">Организаторские сборы: <span style="color: #303030; float: right;" id="cost-of-org"><%= costs.org %></span></b>
          <b style="display: block">Стоимость проживания:  <span style="color: #303030; float: right;" id="cost-of-living"><%= costs.live %></span></b>
          <b style="display: block">Стоимость питания:     <span style="color: #303030; float: right;" id="cost-of-food"><%= costs.food %></span></b>
          <b style="display: block; text-decoration: underline">Полная стоимость:      <span style="color: #303030; float: right;" id="cost-of-all"><%= costs.food + costs.live + costs.org %></span></b>
        </p>
        <hr>
        <p><b>Контакты:</b></p>
        <div class="kwmmb-field">
          <label for="name">Ваше имя:</label>
          <input value="<%= m.get('name') %>" type="text" name="name" id="name" placeholder="Иванов Иван Иванович" required="required">
        </div>
        <div class="kwmmb-field">
          <label for="email">Email-адрес:</label>
          <input value="<%= m.get('email') %>" type="text" name="email" id="email" placeholder="myemail@example.com" required="required">
        </div>
        <div class="kwmmb-field">
          <label for="phone">Телефон:</label>
          <input value="<%= m.get('phone') %>" type="text" name="phone" id="phone" placeholder="79231231235" required="required" <% if (m.isVerified()) { %>readonly="readonly"<% } %>>
        </div>
    </form>
    <% if (m.isVerified()) { %>
    <button class='kwmmb-item-submit'>Изменить</button>
    <% } else { %>
    <button class='kwmmb-item-submit'>Отправить код подтверждения</button>
    <% } %>
    <div id="code"></div>
</script>

<script type="text/html" id="template-code">
  <p><i>Код подтверждения будет выслан в течение 5 минут.</i></p>
  <div class="kwmmb-field">
    <label for="code">Код:</label>
    <input value="<%= m.get('code') %>" type="text" name="code" id="code" placeholder="Код подтверждения" required="required">
  </div>
  <button class="accept">Подтвердить</button>
</script>