<?php

namespace CariaSoft\CSDashWoo\Settings;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Settings {

    public static function init() {
        add_action( 'admin_menu', [ __CLASS__, 'register_page' ] );
    }

    public static function register_page() {
        add_options_page(
            'csdashwoo',
            'csdashwoo',
            'manage_options',
            'csdashwoo-settings',
            [ __CLASS__, 'render' ]
        );
    }

    public static function render() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( isset( $_POST['csdashwoo_save'] ) && check_admin_referer( 'csdashwoo_save_settings' ) ) {
            update_option(
                'csdashwoo_menu_locked',
                isset( $_POST['menu_locked'] ) ? 1 : 0
            );
            
            update_option(
                'csdashwoo_dashboard_enabled',
                isset( $_POST['dashboard_enabled'] ) ? 1 : 0
            );

            echo '<div class="updated"><p>Ayarlar kaydedildi.</p></div>';
        }

        $locked = get_option( 'csdashwoo_menu_locked', 0 );
        $dashboard_enabled = get_option( 'csdashwoo_dashboard_enabled', 1 );
        ?>
        <div class="wrap">
            <h1>csdashwoo – Ayarlar</h1>

            <form method="post">
                <?php wp_nonce_field( 'csdashwoo_save_settings' ); ?>

                <table class="form-table">
                    <tr>
                        <th scope="row">Menü Düzeni Kilidi</th>
                        <td>
                            <label>
                                <input type="checkbox" name="menu_locked" value="1" <?php checked( $locked, 1 ); ?>>
                                Menü düzenini kilitle (kullanıcılar değiştiremez)
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Dashboard Özeti</th>
                        <td>
                            <label>
                                <input type="checkbox" name="dashboard_enabled" value="1" <?php checked( $dashboard_enabled, 1 ); ?>>
                                Dashboard'da özet bilgileri göster
                            </label>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button class="button button-primary" name="csdashwoo_save">
                        Kaydet
                    </button>
                </p>
            </form>
        </div>
        <?php
    }
}