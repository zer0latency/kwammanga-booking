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
                <label for="name">Представьтесь:</label>
                <input type='text' name='name' id="name" placeholder="Иванов И.И.">
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
                <input class="daterange-picker" type='text' name='date_range' id="date_range" placeholder="Щелкните здесь...">
                <span class='kwmmb-field-value'></span>
              </div>
              <hr>
              <p><b>Места:</b></p>
              <div class="kwmmb-field">
                <div id="ya-map"></div>
              </div>
          </form>
          <button class='kwmmb-item-submit'>Сохранить</button>
        </div>
        <div class='col-50'>
          <div class='kwmmb-admin-map' id='ya-map'></div>
        </div>
    </div>
</div>

<script type="text/html" id="kwmmb_baloon">
    <h3>{name}</h3>
    <p>{description}</p>
    <ul>
      <li>За сутки: <b>{price}</b> рублей</li>
      <li>За весь период: <b>{price_full}</b> рублей</li>
    </ul>
</script>