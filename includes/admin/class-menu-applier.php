<?php

class Menu_Applier {

    public static function init() {
        add_action('admin_menu', [self::class, 'apply_custom_menu'], 999); // Yüksek öncelik
    }

    public static function apply_custom_menu() {
        global $menu, $submenu;

        $saved = get_option('csdashwoo_menu_layout', []);
        if (empty($saved)) return;

        $new_menu = [];
        $current_user_roles = wp_get_current_user()->roles;

        foreach ($saved as $slug => $data) {
            $allowed = false;
            foreach ($data['roles'] as $role) {
                if (in_array($role, $current_user_roles)) {
                    $allowed = true;
                    break;
                }
            }
            if (!$allowed) continue;

            // Menü öğesini bul ve ekle
            foreach ($menu as $item) {
                if (isset($item[2]) && $item[2] === $slug) {
                    $new_menu[] = $item;
                    break;
                }
            }
        }

        // Yeni menü düzenini uygula
        $menu = $new_menu;
    }
}