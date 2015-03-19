<?php wp_enqueue_style('kwmmb-admin', KwmmbAssetic::get('stylesheet', 'admin')) ?>
<?php wp_enqueue_script('ymaps','http://api-maps.yandex.ru/2.1/?lang=ru_RU'); ?>
<?php wp_enqueue_script('kwmmb-admin', KwmmbAssetic::get('script', 'admin2'), array('jquery', 'ymaps', 'backbone')) ?>
<div class="wrap">
    <h2>Настройки формы бронирования</h2>
    <div class='col-50 tab' id='settings-other' style="display: none">

    </div>
    <div class="tab" id='settings-places'>
        <div class='col-50'>
          <a href='#settings'>Другие настройки</a>
          <form class='kwmmb-item-form' name='kwmmb-item-form'>
            <h3>Текущий элемент</h3>
              <input id="kwmmb_ajax_nonce" type='hidden' name="_ajax_nonce" value='<?= wp_create_nonce('kwmmb_admin_nonce') ?>'>
              <div class='kwmmb-field'>
                <label for="item_name">Название:</label>
                <input type='text' name='item_name' id="item_name" placeholder="База Хрустальная">
                <span class='kwmmb-field-value'></span>
              </div>
              <div class='kwmmb-field'>
                <label for="item_description">Описание:</label>
                <input type='text' name='item_description' id="item_description" placeholder="">
                <span class='kwmmb-field-value'></span>
              </div>
              <div class='kwmmb-field'>
                <label for="item_tents_count">Кол-во палаток:</label>
                <input type='number' name='item_tents_count' id="item_tents_count" placeholder="650">
                <span class='kwmmb-field-value'></span>
              </div>
              <div class='kwmmb-field'>
                <label for="item_standards_count">Кол-во стандартов:</label>
                <input type='number' name='item_standards_count' id="item_standards_count" placeholder="650">
                <span class='kwmmb-field-value'></span>
              </div>
              <div class='kwmmb-field'>
                <label for="item_comforts_count">Кол-во комфортов:</label>
                <input type='number' name='item_comforts_count' id="item_comforts_count" placeholder="650">
                <span class='kwmmb-field-value'></span>
              </div>
              <div class='kwmmb-field'>
                <label for="item_ecolux_count">Кол-во эко-люксов:</label>
                <input type='number' name='item_ecolux_count' id="item_ecolux_count" placeholder="650">
                <span class='kwmmb-field-value'></span>
              </div>
              <div class='kwmmb-field'>
                <label for="item_points">Точки:</label>
                <input type='text' name='item_points' id="item_points" placeholder="Щелкните для добавления на карту" readonly="readonly">
                <span class='kwmmb-field-value'></span>
              </div>
          </form>
          <button class='kwmmb-item-submit'>Сохранить</button>
        </div>
        <div class='col-50'>
          <div class='kwmmb-admin-map' id='ya-map'></div>
        </div>
        <table class='kwmmb-admin-table'>
          <tr class='table-header'><th>Наименование</th><th>Палатки</th><th>Стандарт</th><th>Комфорт</th><th>Эко-люкс</th><th>Действия</th></tr>

        </table>
    </div>
</div>

<script type="text/html" id="kwmmb_baloon">
    <h3>{name}</h3>
    <p>{description}</p>
    <ul>
      <li>Палатки:  <b>{tents_count}</b> мест</li>
      <li>Стандарт: <b>{standards_count}</b> мест</li>
      <li>Комфорт:  <b>{comforts_count}</b> мест</li>
      <li>Эко-люкс: <b>{ecolux_count}</b> мест</li>
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

<script type="text/html" id="kwmmb_loading">
  <div class="kwmmb-loading-backdrop">
    <img src="<?= KwmmbAssetic::get('animation', 'loading') ?>" alt="Загрузка...">
  </div>
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
        <tr>
          <td><%= e.get('str_id') %></td>
          <td><%= e.get('email') %></td>
          <td><%= e.get('name') %></td>
          <td><%= e.get('phone') %></td>
          <td><%= e.get('date_start') %></td>
          <td><%= e.get('date_end') %></td>
          <td><a href='<%= e.showUrl() %>'>просмотр</a></td>
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