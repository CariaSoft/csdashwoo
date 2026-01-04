<?php

namespace CariaSoft\CSDashWoo\Admin;

if ( ! defined( 'ABSPATH' ) ) exit;

class Menu_UI {

    public static function init() {
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'assets' ] );
        add_action( 'admin_menu', [ __CLASS__, 'register_page' ] );
        add_action( 'wp_ajax_csdashwoo_save_menu', [ __CLASS__, 'save' ] );
    }

    public static function register_page() {
        add_submenu_page(
            'csdashwoo-settings',
            'Menü Yöneticisi',
            'Menü Yöneticisi',
            'manage_options',
            'csdashwoo-menu-manager',
            [ __CLASS__, 'render' ]
        );
    }

    public static function assets( $hook ) {
        if ( strpos( $hook, 'csdashwoo-menu-manager' ) === false ) return;

        wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_script(
            'csdashwoo-menu-ui',
            CSDASHWOO_URL . 'assets/menu-ui.js',
            [ 'jquery', 'jquery-ui-sortable' ],
            '1.0',
            true
        );

        wp_localize_script( 'csdashwoo-menu-ui', 'csdashwoo', [
            'ajax' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'csdashwoo_menu' )
        ]);
    }

    public static function render() {
        $menus = Menu_Reader::get_menu_tree();
        $layout = Menu_Layout::get();
        ?>
        <div class="wrap">
            <h1>Menü Yöneticisi</h1>

            <div style="display:flex; gap:30px">

                <ul id="active-menu" class="menu-box">
                    <h3>Menü Görünümü</h3>
                    <?php foreach ( $layout as $slug ): ?>
                        <li data-slug="<?= esc_attr($slug) ?>">
                            <?= esc_html( $menus[$slug]['title'] ?? $slug ) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <ul id="all-menu" class="menu-box">
                    <h3>WP Menüler</h3>
                    <?php foreach ( $menus as $slug => $menu ): ?>
                        <li data-slug="<?= esc_attr($slug) ?>">
                            <?= esc_html( $menu['title'] ) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>

            </div>

            <button class="button button-primary" id="save-menu">Kaydet</button>
        </div>
        <?php
    }

    public static function save() {
        check_ajax_referer( 'csdashwoo_menu', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error();
        }

        $layout = isset($_POST['layout']) ? array_map('sanitize_text_field', $_POST['layout']) : [];
        Menu_Layout::save( $layout );

        wp_send_json_success();
    }
}