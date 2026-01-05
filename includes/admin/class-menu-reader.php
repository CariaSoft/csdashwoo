<?php

class Menu_Reader {

    public static function get_admin_menu() {
        global $menu, $submenu;

        $structured_menu = [];

        foreach ($menu as $priority => $item) {
            if (empty($item[0])) continue; // Boş satırlar

            $slug = $item[2];
            $title = strip_tags($item[0]);
            $roles = self::get_default_roles_for_menu($slug);

            $structured_menu[$slug] = [
                'title'  => $title,
                'slug'   => $slug,
                'roles'  => $roles,
                'icon'   => $item[6] ?? 'dashicons-admin-generic',
                'submenu' => []
            ];

            // Alt menüler
            if (isset($submenu[$slug])) {
                foreach ($submenu[$slug] as $sub) {
                    $sub_slug = $sub[2];
                    $structured_menu[$slug]['submenu'][$sub_slug] = [
                        'title' => strip_tags($sub[0]),
                        'slug'  => $sub_slug,
                        'roles' => self::get_default_roles_for_menu($sub_slug)
                    ];
                }
            }
        }

        // Kayıtlı özel sıralama varsa uygula
        $saved_layout = get_option('csdashwoo_menu_layout', []);
        if (!empty($saved_layout)) {
            // Sıralamayı uygula (basitçe)
            $ordered = [];
            foreach ($saved_layout as $key => $data) {
                if (isset($structured_menu[$key])) {
                    $ordered[$key] = $structured_menu[$key];
                }
            }
            $structured_menu = $ordered + $structured_menu;
        }

        return $structured_menu;
    }

    private static function get_default_roles_for_menu($slug) {
        // Basit varsayılanlar (gerçekte daha detaylı olabilir)
        if (strpos($slug, 'edit.php?post_type=shop_order') !== false) {
            return ['administrator', 'shop_manager'];
        }
        return ['administrator'];
    }
}