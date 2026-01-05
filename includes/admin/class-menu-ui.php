<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Menu_UI {

    public static function init() {
        add_action('admin_enqueue_scripts', [self::class, 'enqueue_assets']);
        add_action('wp_ajax_csdashwoo_save_menu', [self::class, 'save_menu']);
    }

    public static function enqueue_assets($hook) {
        if ($hook !== 'settings_page_csdashwoo-menu-manager') return;

        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_style('csdashwoo-menu-ui', CSDASHWOO_URL . 'assets/css/menu-ui.css', [], '1.0');
        wp_enqueue_script('csdashwoo-menu-ui', CSDASHWOO_URL . 'assets/menu-ui.js', ['jquery', 'jquery-ui-sortable'], '1.0', true);

        wp_localize_script('csdashwoo-menu-ui', 'csdashwooMenu', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('csdashwoo_menu_save'),
            'menu'    => Menu_Reader::get_admin_menu() // JSON olarak menüyü JS'e geçir
        ]);
    }

    public static function save_menu() {
        check_ajax_referer('csdashwoo_menu_save', 'nonce');

        $layout = isset($_POST['menu_layout']) ? wp_unslash($_POST['menu_layout']) : [];
        update_option('csdashwoo_menu_layout', $layout);

        wp_send_json_success('Menü kaydedildi');
    }
}