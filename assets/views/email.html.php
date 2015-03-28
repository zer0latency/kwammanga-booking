<h1>Kwammanga.ru</h1>
<?= $content ?>
<h2>Ваш заказ:</h2>
<ul>
  <li>Организаторские сборы: <?= $costs['org'] ?></li>
  <li>Питание:  <?= $costs['food'] ?></li>
  <li>Проживание:  <?= $costs['living'] ?></li>
  <li>Полная стоимость:  <?= $costs['food'] + $costs['org'] + $costs['living'] ?> руб.</li>
</ul>
<?php $edit_url = site_url().'/index.php/kwmmb-booking/#bookings/'.$model->str_id; ?>
<p>
  Изменить свой заказ Вы можете перейдя по ссылке: <a href="<?= $edit_url ?>"><?= $edit_url ?></a>.
</p>