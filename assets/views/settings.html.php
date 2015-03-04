<?php wp_enqueue_style('kwmmb-admin', KwmmbAssetic::get('stylesheet', 'admin')) ?>
<?php wp_enqueue_script('ymaps','http://api-maps.yandex.ru/2.1/?lang=ru_RU'); ?>
<?php wp_enqueue_script('kwmmb-admin', KwmmbAssetic::get('script', 'admin'), array('jquery', 'ymaps')) ?>
<div class="wrap">
    <h2>Настройки формы бронирования</h2>
    <div>
        <div class='col-50'>
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
    </div>
    <table class='kwmmb-admin-table'>
      <tr class='table-header'><th>Наименование</th><th>Палатки</th><th>Стандарт</th><th>Комфорт</th><th>Эко-люкс</th><th>Действия</th></tr>

    </table>
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