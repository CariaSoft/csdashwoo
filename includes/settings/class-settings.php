<?php
/**
 * CSDashWoo Ayarlar Sınıfı
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Settings {

    public static function add_admin_menu() {
        add_options_page(
            'CSDashWoo Ayarları',
            'CSDashWoo',
            'manage_options',
            'csdashwoo-settings',
            [ self::class, 'settings_page' ]
        );
    }

    public static function settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

            <form method="post" action="options.php">
                <?php
                settings_fields( 'csdashwoo_settings_group' );
                do_settings_sections( 'csdashwoo-settings' );
                submit_button( 'Değişiklikleri Kaydet' );
                ?>
            </form>
        </div>
        <?php
    }

    public static function register_settings() {
        // Ayarları kaydetme izni
        register_setting(
            'csdashwoo_settings_group',
            'csdashwoo_test_checkbox',
            [ 'type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean' ]
        );

        // Bölüm ekle
        add_settings_section(
            'csdashwoo_main_section',
            'Temel Ayarlar',
            null,
            'csdashwoo-settings'
        );

        // Alan ekle (checkbox)
        add_settings_field(
            'csdashwoo_test_checkbox',
            'Test Özelliği Etkinleştir',
            [ self::class, 'checkbox_callback' ],
            'csdashwoo-settings',
            'csdashwoo_main_section'
        );
    }

    public static function checkbox_callback() {
        $value = get_option( 'csdashwoo_test_checkbox', false );
        ?>
        <label>
            <input type="checkbox" name="csdashwoo_test_checkbox" value="1" <?php checked( $value, true ); ?> />
            Bu özelliği etkinleştir (test amaçlı)
        </label>
        <p class="description">Şu an sadece test için var, ileride gerçek ayarlar buraya gelecek.</p>
        <?php
    }
}