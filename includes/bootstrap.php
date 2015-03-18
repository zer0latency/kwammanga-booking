<?php

/*
 * Copyright (C) 2015 dkey
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

function kwmmb_log($message) {
    if (getenv('KWMMB_DEBUG')) {
        error_log($message, 4);
    }
}

include dirname(__FILE__).'/KwmmbAssetic.php';

// Include bootstrap files, needed to work

include KwmmbAssetic::get('php', 'includes/KwmmbDb');
include KwmmbAssetic::get('php', 'includes/KwmmbAjax');
include KwmmbAssetic::get('php', 'includes/classes/KwmmbRest');
include KwmmbAssetic::get('php', 'includes/BookingItem');
include KwmmbAssetic::get('php', 'includes/Booking');
include KwmmbAssetic::get('php', 'includes/Code');
include KwmmbAssetic::get('php', 'hooks/shortcodes');

include KwmmbAssetic::get('php', 'admin/admin');
