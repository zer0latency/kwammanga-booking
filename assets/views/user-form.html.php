<?php wp_enqueue_style('daterange-picker-css',     kwmmb_asset('stylesheet', 'daterangepicker')) ?>
<?php wp_enqueue_style('kwmmb-css',                kwmmb_asset('stylesheet', 'admin')) ?>
<?php wp_enqueue_script('moment',                  kwmmb_asset('script', 'moment.min')) ?>
<?php wp_enqueue_script('jquery-daterange-picker', kwmmb_asset('script', 'jquery.daterangepicker')) ?>
<?php wp_enqueue_script('ymaps',                   'http://api-maps.yandex.ru/2.1/?lang=ru_RU'); ?>
<?php wp_enqueue_script('kwmmb_client',            kwmmb_asset('script', 'client')) ?>
<div class="wrap">
    <div>
        <div>
          <form class='kwmmb-item-form' name='kwmmb-item-form'>
              <input id="kwmmb_ajax_nonce" type='hidden' name="_ajax_nonce" value='<?= wp_create_nonce('kwmmb_user_nonce') ?>'>
              <div class='kwmmb-field'>
                <label for="place_type">Тип размещения:</label>
                <table>
                  <tr>
                  <?php foreach ($place_types as $name => $label): ?>
                    <td><a href="#<?= $name ?>"><?= $label ?></a></td>
                  <?php endforeach; ?>
                  </tr>
                </table>
              </div>
              <p><b>Доступные базы:</b></p>
              <div id="ya-map"></div>
              <div class="kwmmb-field">
                <label for="place">Выбранное место:</label>
                <input type="text" readonly="readonly" name="place" id="place" placeholder="Выберите место на карте">
              </div>
              <hr>
              <p><b>Нас:</b></p>
              <div class='kwmmb-field'>
                <label for="adults">Взрослых:</label>
                <input type='number' name='adults' id="adults" value="1">
                <span class='kwmmb-field-value'></span>
              </div>
              <div class='kwmmb-field'>
                <label for="childs_0_5">Детей 0-5:</label>
                <input type='number' name='childs_0_5' id="childs_0_5" value="0">
                <span class='kwmmb-field-value'></span>
              </div>
              <div class='kwmmb-field'>
                <label for="childs_6_12">Детей 6-12:</label>
                <input type='number' name='childs_6_12' id="childs_6_12" value="0">
                <span class='kwmmb-field-value'></span>
              </div>
              <hr>
              <p><b>Период принятия участия:</b></p>
              <div class='kwmmb-field'>
                <label for="date_range">Когда:</label>
                <input class="daterange-picker" type='text' name='date_range' id="date_range" placeholder="Щелкните здесь..." readonly="readonly">
                <span class='kwmmb-field-value'></span>
              </div>
              <p>
                <b style="display: block">Организаторские сборы: <span style="color: #303030; float: right;" id="cost-of-org"></span></b>
                <b style="display: block">Стоимость проживания:  <span style="color: #303030; float: right;" id="cost-of-living"></span></b>
                <b style="display: block">Стоимость питания:     <span style="color: #303030; float: right;" id="cost-of-food"></span></b>
                <b style="display: block">Полная стоимость:      <span style="color: #303030; float: right;" id="cost-of-all"></span></b>
              </p>
              <hr>
              <p><b>Контакты:</b></p>
              <div class="kwmmb-field">
                <label for="name">Ваше имя:</label>
                <input type="text" name="name" id="name" placeholder="Иванов Иван Иванович" required="required">
              </div>
              <div class="kwmmb-field">
                <label for="email">Email-адрес:</label>
                <input type="text" name="email" id="email" placeholder="myemail@example.com" required="required">
              </div>
              <div class="kwmmb-field">
                <label for="phone">Телефон:</label>
                <input type="text" name="phone" id="phone" placeholder="79231231235" required="required">
              </div>
          </form>
          <button class='kwmmb-item-submit'>Заказать</button>
        </div>
        <div class='col-50'>
          <div class='kwmmb-admin-map' id='ya-map'></div>
        </div>
    </div>
</div>

<script type="text/html" id="kwmmb_baloon">
    <p><b>{name}</b></p>
    <p>{description}</p>
    <ul>
      <li>За сутки: <b>{price}</b> рублей</li>
      <li>За весь период: <b>{price_full}</b> рублей</li>
    </ul>
</script>