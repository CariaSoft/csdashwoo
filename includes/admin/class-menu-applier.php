<?php

namespace CariaSoft\CSDashWoo\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Menu_Applier {

    public static function apply() {
        if ( Menu_Layout::is_locked() && ! current_user_can( 'manage_options' ) ) {
            return;
        }
        
        global $menu, $submenu;

        $user = wp_get_current_user();
        $user_roles = $user->roles;

        // Role göre izinleri belirle
        $can_access_settings = in_array( 'administrator', $user_roles );
        $can_access_menu_manager = in_array( 'administrator', $user_roles );
        $can_access_orders = in_array( 'administrator', $user_roles ) || in_array( 'shop_manager', $user_roles );
        $can_access_notices = in_array( 'administrator', $user_roles ) || in_array( 'shop_manager', $user_roles );
        $can_access_open_tasks = in_array( 'administrator', $user_roles ) || in_array( 'shop_manager', $user_roles );
        $can_access_products = in_array( 'administrator', $user_roles ) || in_array( 'shop_manager', $user_roles ) || in_array( 'editor', $user_roles );
        $can_access_comments = in_array( 'administrator', $user_roles ) || in_array( 'editor', $user_roles );

        $layout = Menu_Layout::get();
        $used = [];
        $new_menu = [];

        // Kullanıcının rollerine göre menü filtrele
        $filtered_layout = [];
        foreach ( $layout as $slug ) {
            $show_item = true;
            
            // Menü Yöneticisi (sadece administrator)
            if ( strpos( $slug, 'csdashwoo-menu-manager' ) !== false && ! $can_access_menu_manager ) {
                $show_item = false;
            }
            
            // Ayarlar (sadece administrator)
            if ( ( strpos( $slug, 'csdashwoo-settings' ) !== false || strpos( $slug, 'options-general.php' ) !== false ) && ! $can_access_settings ) {
                $show_item = false;
            }
            
            // Siparişler
            if ( strpos( $slug, 'shop_order' ) !== false && ! $can_access_orders ) {
                $show_item = false;
            }
            
            // Ürünler
            if ( strpos( $slug, 'product' ) !== false && ! $can_access_products ) {
                $show_item = false;
            }
            
            // Yorumlar
            if ( strpos( $slug, 'edit-comments.php' ) !== false && ! $can_access_comments ) {
                $show_item = false;
            }
            
            if ( $show_item ) {
                $filtered_layout[] = $slug;
            }
        }

        // 1️⃣ Ana menüler (rol bazlı filtrelenmiş)
        foreach ( $filtered_layout as $slug ) {
            foreach ( $menu as $item ) {
                if ( isset( $item[2] ) && $item[2] === $slug ) {
                    $new_menu[] = $item;
                    $used[] = $slug;
                    break;
                }
            }
        }

        // 2️⃣ Diğer menüler → Rol bazlı filtreleme
        foreach ( $menu as $item ) {
            if ( empty( $item[2] ) || in_array( $item[2], $used, true ) ) {
                continue;
            }
            
            // Menü yöneticisi ve ayarlar hariç diğer menüleri filtrele
            $show_item = true;
            
            // Menü Yöneticisi (sadece administrator)
            if ( strpos( $item[2], 'csdashwoo-menu-manager' ) !== false && ! $can_access_menu_manager ) {
                $show_item = false;
            }
            
            // Ayarlar (sadece administrator)
            if ( ( strpos( $item[2], 'csdashwoo-settings' ) !== false || strpos( $item[2], 'options-general.php' ) !== false ) && ! $can_access_settings ) {
                $show_item = false;
            }
            
            // Siparişler
            if ( strpos( $item[2], 'shop_order' ) !== false && ! $can_access_orders ) {
                $show_item = false;
            }
            
            // Ürünler
            if ( strpos( $item[2], 'product' ) !== false && ! $can_access_products ) {
                $show_item = false;
            }
            
            // Yorumlar
            if ( strpos( $item[2], 'edit-comments.php' ) !== false && ! $can_access_comments ) {
                $show_item = false;
            }
            
            if ( $show_item ) {
                add_submenu_page(
                    Menu_Others::PARENT_SLUG,
                    $item[0],
                    $item[0],
                    $item[1],
                    $item[2]
                );
            }
        }

        // 3️⃣ Sadece filtrelenmiş ana menüleri göster
        $menu = $new_menu;

        // 4️⃣ Etiketleri uygula
        if ( class_exists( '\CariaSoft\CSDashWoo\Admin\Menu_Labels' ) ) {
            Menu_Labels::apply();
        }
    }
}