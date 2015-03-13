<?php wp_enqueue_style('daterange-picker-css',     KwmmbAssetic::get('stylesheet', 'daterangepicker')) ?>
<?php wp_enqueue_style('kwmmb-css',                KwmmbAssetic::get('stylesheet', 'admin')) ?>
<?php wp_enqueue_script('moment',                  KwmmbAssetic::get('script', 'moment.min')) ?>
<?php wp_enqueue_script('jquery-daterange-picker', KwmmbAssetic::get('script', 'jquery.daterangepicker')) ?>
<?php wp_enqueue_script('ymaps',                   'http://api-maps.yandex.ru/2.1/?lang=ru_RU'); ?>
<?php wp_enqueue_script('underscore',              KwmmbAssetic::get('script', 'underscore-min')) ?>
<?php wp_enqueue_script('backbone',                KwmmbAssetic::get('script', 'backbone-min')) ?>
<?php wp_enqueue_script('app',                     KwmmbAssetic::get('script', 'app'), array('backbone')) ?>
<div class="wrap" id="wrap">

</div>

<script>
    /**
     * Bootstrap some BookingItems
     */
    var bookingItems = JSON.parse('<?= BookingItem::get_all_json() ?>');

    // Inject options
    var price_org    = <?= get_option('price_org', 2500); ?>;
</script>

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

<script type="text/html" id="template-booking">
    <form class='kwmmb-item-form' name='kwmmb-item-form'>
        <input id="kwmmb_ajax_nonce" type='hidden' name="_ajax_nonce" value='<?= wp_create_nonce('kwmmb_user_nonce') ?>'>
        <p><b>Период принятия участия:</b></p>
        <div class='kwmmb-field'>
          <label for="date_range">Когда:</label>
          <input value="<%= moment(m.date_start).format('DD.MM.YYYY') %> - <%= moment(m.date_end).format('DD.MM.YYYY') %>" class="daterange-picker" type='text' name='date_range' id="date_range" placeholder="Щелкните здесь..." readonly="readonly">
          <span class='kwmmb-field-value'></span>
        </div>
        <hr>
        <div class='kwmmb-field'>
          <label for="place_type">Тип размещения:</label>
          <table class="kwmmb-places-table">
            <tr>
              <?php foreach ($place_types as $name => $label): ?>
                <td class="<% if (m.comfort == "<?= $name ?>") { %>active<% }%>">
                  <a class="tab-toggle" data-id="<?= $name ?>"><?= $label ?></a>
                </td>
              <?php endforeach; ?>
            </tr>
          </table>
        </div>
        <p><b>Доступные базы:</b></p>
        <div id="ya-map"></div>
        <div class="kwmmb-field">
          <label for="place">Выбранное место:</label>
          <input value="<%= m.item.attributes.name %>" type="text" readonly="readonly" name="place" id="place" placeholder="Выберите место на карте">
        </div>
        <hr>
        <p><b>Нас:</b></p>
        <div class='kwmmb-field'>
          <label for="adults">Взрослых:</label>
          <input type='number' name='adults' id="adults" value="<%= m.adults %>">
          <span class='kwmmb-field-value'></span>
        </div>
        <div class='kwmmb-field'>
          <label for="child_0_5">Детей 0-5:</label>
          <input type='number' name='child_0_5' id="child_0_5" value="<%= m.child_0_5 %>">
          <span class='kwmmb-field-value'></span>
        </div>
        <div class='kwmmb-field'>
          <label for="child_6_12">Детей 6-12:</label>
          <input type='number' name='child_6_12' id="child_6_12" value="<%= m.child_6_12 %>">
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
          <input value="<%= m.name %>" type="text" name="name" id="name" placeholder="Иванов Иван Иванович" required="required">
        </div>
        <div class="kwmmb-field">
          <label for="email">Email-адрес:</label>
          <input value="<%= m.email %>" type="text" name="email" id="email" placeholder="myemail@example.com" required="required">
        </div>
        <div class="kwmmb-field">
          <label for="phone">Телефон:</label>
          <input value="<%= m.phone %>" type="text" name="phone" id="phone" placeholder="79231231235" required="required">
        </div>
    </form>
    <button class='kwmmb-item-submit'>Заказать</button>
    <script>
      jQuery(function () {
        Application.bindRangePicker();
        ymaps.ready(function () {
            if (jQuery('#ya-map *').length > 0) {
                return;
            }
            var myMap = new ymaps.Map("ya-map", {
              center: [44.808763, 37.370311],
              zoom: 9
            });
            Application.bookingView.items.each(function (el) {
                var poly = new ymaps.Polygon([JSON.parse(el.attributes.points)]);
                myMap.geoObjects.add(poly);
                poly.events.add("click", function () {
                    Application.bookingView.model.set("item", el);
                });
            });
        });
      });

    </script>
</script>