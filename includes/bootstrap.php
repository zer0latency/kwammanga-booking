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

/**
 * Get plugin file
 * @param type $type (script|image|stylesheet|php)
 * @param type $name e.g. hooks/shortcodes
 * @return string Absolute path to file
 */
function kwmmb_asset($type, $name) {
    switch ($type) {
        case 'script':
            $path = plugin_dir_url( __FILE__ )."../assets/js/$name.js";
            break;
        case 'image':
            $path = plugin_dir_url( __FILE__ )."../assets/images/$name.png";
            break;
        case 'stylesheet':
            $path = plugin_dir_url( __FILE__ )."../assets/css/$name.css";
            break;
        case 'php':
            $path = dirname(__file__)."/../$name.php";
            break;
        default:
            $path = '';
            break;
    }
    
    return $path;
}

function kwmmb_render($template, $params = array()) {
    ob_start();
    extract($params);
    include kwmmb_asset('php', $template);
    $rendered_template = ob_get_contents();
    ob_end_clean();
    
    return $rendered_template;
}

include kwmmb_asset('php', 'includes/db');
include kwmmb_asset('php', 'hooks/shortcodes');
include kwmmb_asset('php', 'admin/admin');