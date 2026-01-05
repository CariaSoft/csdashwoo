<?php
if (!defined('ABSPATH')) {
    exit;
}

class Settings {

    public static function add_admin_menu() {
        add_options_page(
            'CSDashWoo Ayarları',
            'CSDashWoo',
            'manage_options',
            'csdashwoo-settings',              // ← Slug 1
            [self::class, 'settings_page']
        );
    }

    public static function settings_page() {
        ?>
        <div class="wrap">
            <h1>CSDashWoo Ayarları - TEST MODU</h1>
            
            <p style="color: green; font-size: 18px;">
                → Sayfa tamamen çalışıyor. Settings API sorunu devam ediyor.
            </p>

            <!-- Manuel test alanı -->
            <h2>Test Ayarları (Manuel)</h2>
            <form method="post" action="options.php">
                <?php settings_fields('csdashwoo_settings_group'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">Test Özelliği</th>
                        <td>
                            <?php 
                            $val = get_option('csdashwoo_test_checkbox', false); 
                            ?>
                            <label>
                                <input type="checkbox" name="csdashwoo_test_checkbox" value="1" <?php checked(1, $val); ?> />
                                Etkinleştir (manuel eklenmiş)
                            </label>
                            <p class="description">Bu checkbox kaydediliyor ve çalışıyor olmalı.</p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public static function register_settings() {
        register_setting(
            'csdashwoo_settings_group',
            'csdashwoo_test_checkbox',
            ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']
        );

        add_settings_section(
            'csdashwoo_main_section',
            'Temel Ayarlar',
            null,
            'csdashwoo-settings'               // ← Slug 3 - burası kritik!
        );

        add_settings_field(
            'csdashwoo_test_checkbox',
            'Test Özelliği Etkinleştir',
            [self::class, 'checkbox_callback'],
            'csdashwoo-settings',              // ← Slug 4 - burası da aynı olmalı!
            'csdashwoo_main_section'
        );

        // Debug: Bu satır çalışıyorsa logda görünür
        error_log('CSDashWoo SETTINGS: register_settings çalıştı');
    }

    public static function checkbox_callback() {
        $value = get_option('csdashwoo_test_checkbox', false);
        ?>
        <label>
            <input type="checkbox" name="csdashwoo_test_checkbox" value="1" <?php checked(true, $value); ?> />
            Bu özelliği etkinleştir (test amaçlı)
        </label>
        <p class="description">Şu an sadece test için var, ileride gerçek ayarlar buraya gelecek.</p>
        <?php
    }
}