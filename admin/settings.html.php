<?php wp_enqueue_style('kwmmb-admin', kwmmb_asset('stylesheet', 'admin')) ?>
<?php wp_enqueue_script('ymaps','http://api-maps.yandex.ru/2.1/?lang=ru_RU'); ?>
<?php wp_enqueue_script('kwmmb-admin', kwmmb_asset('script', 'admin'), array('jquery', 'ymaps')) ?>
<div class="wrap">
    <h2>Настройки формы бронирования</h2>
    <div>
        <div class='col-50'>
          <form class='kwmmb-item-form' name='kwmmb-item-form'>
            <h3>Текущий элемент</h3>
              <input type='hidden' name="_ajax_nonce" value='<?= wp_create_nonce('kwmmb_admin_nonce') ?>'>
              <div class='kwmmb-field'>
                <label for="item_name">Название:</label>
                <input type='text' name='item_name' id="item_name" placeholder="Палатка">
                <span class='kwmmb-field-value'></span>
              </div>
              <div class='kwmmb-field'>
                <label for="item_description">Описание:</label>
                <input type='text' name='item_description' id="item_description" placeholder="Кондиционер, бла бла бла">
                <span class='kwmmb-field-value'></span>
              </div>
              <div class='kwmmb-field'>
                <label for="item_price">Цена:</label>
                <input type='text' name='item_price' id="item_price" placeholder="650">
                <span class='kwmmb-field-value'></span>
              </div>
              <div class='kwmmb-field'>
                <label for="item_price_full">Цена (весь период):</label>
                <input type='text' name='item_price_full' id="item_price_full" placeholder="5000">
                <span class='kwmmb-field-value'></span>
              </div>
              <div class='kwmmb-field'>
                <label for="item_roominess">Кол-во номеров:</label>
                <input type='text' name='item_roominess' id="item_roominess" placeholder="20">
                <span class='kwmmb-field-value'></span>
              </div>
              <div class='kwmmb-field'>
                <label for="item_latitude">Широта:</label>
                <input type='text' name='item_latitude' id="item_latitude" placeholder="44.808763">
                <span class='kwmmb-field-value'></span>
              </div>
              <div class='kwmmb-field'>
                <label for="item_longitude">Долгота:</label>
                <input type='text' name='item_longitude' id="item_longitude" placeholder="37.370311">
                <span class='kwmmb-field-value'></span>
              </div>
              <button class='kwmmb-item-submit'>Сохранить</button>
          </form>
        </div>
        <div class='col-50'>
          <div class='kwmmb-admin-map' id='ya-map'></div>
        </div>
    </div>
    <script>
        kwmmbItems = {
          <?php $i=1; foreach ($items as $item): ?>
          <?php $i++; ?>
          "<?= $item->id ?>": {
            "id":          "<?= $item->id ?>",
            "name":        "<?= $item->name ?>",
            "description": "<?= $item->description ?>",
            "price":       "<?= $item->price ?>",
            "price_full":  "<?= $item->price_full ?>",
            "roominess":   "<?= $item->roominess ?>",
            "latitude":    "<?= $item->latitude ?>",
            "longitude":   "<?= $item->longitude ?>",
          } <?= $i<count($items) ? ',' : '' ?>
          <?php endforeach; ?>
        };
    </script>
    <table class='kwmmb-admin-table'>
      <tr class='table-header'><th>Наименование</th><th>Цена</th><th>Цена за весь период</th><th>Вместимость</th><th>Действия</th></tr>

    </table>
</div>