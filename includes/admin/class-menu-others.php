<?php

namespace CariaSoft\CSDashWoo\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Menu_Others {

    const PARENT_SLUG = 'csdashwoo-others';

    public static function apply() {
        global $menu;

        add_menu_page(
            __( 'Diğer Ayarlar', 'csdashwoo' ),
            __( 'Diğer Ayarlar', 'csdashwoo' ),
            'manage_options',
            self::PARENT_SLUG,
            null,
            'dashicons-admin-generic',
            99
        );
    }
}