<?php

class Menu_Applier {

    public static function init() {
        add_action('admin_menu', [self::class, 'apply_custom_menu'], 999); // Yüksek öncelik
    }

    public static function apply_custom_menu() {
        global $menu, $submenu;

        $saved_roles = get_option('csdashwoo_menu_roles', []);

        $current_user_roles = wp_get_current_user()->roles;

        // Ana menü filtrele
        foreach ($menu as $key => $item) {
            $slug = $item[2];
            if (isset($saved_roles[$slug])) {
                $allowed = false;
                foreach ($saved_roles[$slug] as $role) {
                    if (in_array($role, $current_user_roles)) {
                        $allowed = true;
                        break;
                    }
                }
                if (!$allowed) {
                    unset($menu[$key]);
                }
            }
        }

        // Alt menü filtrele
        foreach ($submenu as $parent => &$sub) {
            foreach ($sub as $key => $item) {
                $slug = $item[2];
                if (isset($saved_roles[$slug])) {
                    $allowed = false;
                    foreach ($saved_roles[$slug] as $role) {
                        if (in_array($role, $current_user_roles)) {
                            $allowed = true;
                            break;
                        }
                    }
                    if (!$allowed) {
                        unset($sub[$key]);
                    }
                }
            }
        }
    }
}