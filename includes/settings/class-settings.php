<?php
if (!defined('ABSPATH')) {
    exit;
}

class Settings {

    public static function add_admin_menu() {
        add_options_page(
            'CSDashWoo Ayarlari',
            'CSDashWoo',
            'manage_options',
            'csdashwoo-settings',
            [self::class, 'settings_page']
        );
    }

    public static function settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <form method="post" action="options.php">
                <?php
                settings_fields('csdashwoo_settings_group');
                do_settings_sections('csdashwoo-settings');
                submit_button('Degisiklikleri Kaydet');
                ?>
            </form>
        </div>
        <?php
    }

    public static function register_settings() {
        register_setting(
            'csdashwoo_settings_group',
            'csdashwoo_test_checkbox',
            ['type' => 'boolean']
        );

        register_setting(
            'csdashwoo_settings_group',
            'csdashwoo_custom_title',
            ['type' => 'string', 'sanitize_callback' => 'sanitize_text_field']
        );

        register_setting(
            'csdashwoo_settings_group',
            'csdashwoo_hide_default_widgets',
            ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean']
        );

        register_setting(
            'csdashwoo_settings_group',
            'csdashwoo_widget_layout',
            ['type' => 'string', 'sanitize_callback' => 'sanitize_text_field']
        );

        add_settings_section(
            'csdashwoo_main_section',
            'Temel Ayarlar',
            null,
            'csdashwoo-settings'  // ← KRİTİK: tam bu string
        );

        add_settings_field(
            'csdashwoo_test_checkbox',
            'Test Ozelligi Etkinlestir',
            [self::class, 'checkbox_callback'],
            'csdashwoo-settings',  // ← aynı string
            'csdashwoo_main_section'
        );

        // Text alanı örneği
        add_settings_field(
            'csdashwoo_custom_title',
            'Dashboard Baslik Metni',
            [self::class, 'text_callback'],
            'csdashwoo-settings',
            'csdashwoo_main_section'
        );

        // Boolean (checkbox) - başka bir özellik
        add_settings_field(
            'csdashwoo_hide_default_widgets',
            'Varsayilan Widgetlari Gizle',
            [self::class, 'checkbox_callback_extra'],
            'csdashwoo-settings',
            'csdashwoo_main_section'
        );

        // Select dropdown örneği
        add_settings_field(
            'csdashwoo_widget_layout',
            'Widget Düzeni',
            [self::class, 'select_callback'],
            'csdashwoo-settings',
            'csdashwoo_main_section'
        );
    }

    public static function checkbox_callback() {
        $value = get_option('csdashwoo_test_checkbox', false);
        ?>
        <label>
            <input type="checkbox" name="csdashwoo_test_checkbox" value="1" <?php checked(1, $value); ?> />
            Bu ozelligi etkinlestir (otomatik)
        </label>
        <p class="description">Otomatik Settings API alanı</p>
        <?php
    }

    public static function text_callback() {
        $value = get_option('csdashwoo_custom_title', 'CSDashWoo Panel');
        echo '<input type="text" name="csdashwoo_custom_title" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">Dashboard widgetlarinda gorunecek baslik.</p>';
    }

    public static function checkbox_callback_extra() {
        $value = get_option('csdashwoo_hide_default_widgets', false);
        ?>
        <label>
            <input type="checkbox" name="csdashwoo_hide_default_widgets" value="1" <?php checked(1, $value); ?> />
            Varsayilan dashboard widgetlarini gizle
        </label>
        <p class="description">Eger isaretlenirse, standart WordPress ve WooCommerce widgetlari gizlenecek.</p>
        <?php
    }

    public static function select_callback() {
        $value = get_option('csdashwoo_widget_layout', 'grid');
        ?>
        <select name="csdashwoo_widget_layout">
            <option value="grid" <?php selected('grid', $value); ?>>Grid (Klasik)</option>
            <option value="columns" <?php selected('columns', $value); ?>>Sütunlar</option>
            <option value="compact" <?php selected('compact', $value); ?>>Kompakt</option>
        </select>
        <?php
    }
}