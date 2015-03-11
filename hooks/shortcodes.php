<?php

function kwmmb_booking_form_shortcode() {
    $params = array(
        'place_types' => array(
            'tent'     => 'Палатка',
            'standard' => 'Стандарт',
            'comfort'  => 'Комфорт',
            'ecolux'   => 'Эко-люкс',
        )
    );

    return KwmmbAssetic::render('assets/views/user-form.html', $params);
}

function kwmmb_register_form_shortcode() {
    add_shortcode('kwmmb-user-form', 'kwmmb_booking_form_shortcode');
}