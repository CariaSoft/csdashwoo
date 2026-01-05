<?php
if (!defined('ABSPATH')) exit;

class Menu_Reader {

    public static function get_current_menu() {
        global $menu, $submenu;

        $result = [];

        foreach ($menu as $item) {
            if (empty($item[0]) || $item[0] === ' ') continue; // boş satırlar

            $slug = $item[2];
            $title = strip_tags($item[0]);

            $result[$slug] = [
                'title' => $title,
                'slug'  => $slug,
                'roles' => self::get_default_roles($slug),
            ];

            // alt menüler
            if (!empty($submenu[$slug])) {
                foreach ($submenu[$slug] as $sub) {
                    $sub_slug = $sub[2];
                    $result[$sub_slug] = [
                        'title' => strip_tags($sub[0]),
                        'slug'  => $sub_slug,
                        'parent' => $slug,
                        'roles' => self::get_default_roles($sub_slug),
                    ];
                }
            }
        }

        // Kayıtlı özel roller varsa uygula
        $saved_roles = get_option('csdashwoo_menu_roles', []);
        foreach ($saved_roles as $slug => $roles) {
            if (isset($result[$slug])) {
                $result[$slug]['roles'] = $roles;
            }
        }

        return $result;
    }

    private static function get_default_roles($slug) {
        // basit varsayılanlar - gerçekte daha akıllı olabilir
        if (stripos($slug, 'edit.php?post_type=shop_order') !== false) {
            return ['administrator', 'shop_manager'];
        }
        if (stripos($slug, 'edit.php?post_type=product') !== false) {
            return ['administrator', 'shop_manager', 'editor'];
        }
        return ['administrator'];
    }
}