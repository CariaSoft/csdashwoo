<?php
if (!defined('ABSPATH')) {
    exit;
}

class Settings {

    public static function add_admin_menu() {
        // Ana ayarlar sayfası
        add_options_page(
            'CSDashWoo Ayarlari',
            'CSDashWoo',
            'manage_options',
            'csdashwoo-settings',
            [self::class, 'settings_page']
        );

        // Yeni: Menü Yönetimi sayfası
        // Menü Yönetimi sayfası
        add_submenu_page(
            'csdashwoo-settings',                    // Ana menü slug'ı
            'Menü Yönetimi - CSDashWoo',
            'Menü Yönetimi',
            'manage_options',
            'csdashwoo-menu-manager',
            [self::class, 'menu_manager_page']
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

    public static function menu_manager_page() {
        $menu_items = Menu_Reader::get_current_menu();

        ?>
        <div class="wrap">
            <h1>Menü Yönetimi</h1>
            <p>Menü öğelerini sürükle bırak ile sıralayın ve hangi rollerin göreceğini seçin.</p>

            <form method="post" id="csdashwoo-menu-form">
                <?php wp_nonce_field('csdashwoo_save_menu', 'csdashwoo_nonce'); ?>

                <ul id="csdashwoo-menu-sortable" class="sortable-menu">
                    <?php foreach ($menu_items as $slug => $item): ?>
                        <li class="menu-item" data-slug="<?php echo esc_attr($slug); ?>">
                            <span class="dashicons dashicons-menu"></span>
                            <strong><?php echo esc_html($item['title']); ?></strong>

                            <select name="roles[<?php echo esc_attr($slug); ?>][]" multiple class="menu-roles">
                                <option value="administrator" <?php selected(in_array('administrator', $item['roles'] ?? [])); ?>>Yönetici</option>
                                <option value="shop_manager"   <?php selected(in_array('shop_manager',   $item['roles'] ?? [])); ?>>Mağaza Yöneticisi</option>
                                <option value="editor"         <?php selected(in_array('editor',         $item['roles'] ?? [])); ?>>Editör</option>
                                <option value="author"         <?php selected(in_array('author',         $item['roles'] ?? [])); ?>>Yazar</option>
                            </select>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <p class="submit">
                    <input type="submit" name="csdashwoo_save_menu" class="button button-primary" value="Menüyü Kaydet">
                </p>
            </form>
        </div>

        <!-- jQuery UI Sortable için -->
        <?php wp_enqueue_script('jquery-ui-sortable'); ?>
        <script>
        jQuery(function($) {
            $("#csdashwoo-menu-sortable").sortable({
                placeholder: "menu-placeholder",
                handle: ".dashicons-menu"
            });

            $("#csdashwoo-menu-form").on("submit", function(e) {
                e.preventDefault();

                var formData = $(this).serialize();

                $.post(ajaxurl, {
                    action: "csdashwoo_save_menu",
                    _ajax_nonce: "<?php echo wp_create_nonce('csdashwoo_save_menu'); ?>",
                    data: formData
                }, function(response) {
                    if (response.success) {
                        alert("Menü başarıyla kaydedildi!");
                    } else {
                        alert("Hata: " + (response.data || "Bilinmeyen hata"));
                    }
                });
            });
        });
        </script>

        <style>
        .sortable-menu { list-style: none; padding: 0; }
        .menu-item {
            padding: 12px;
            background: #f9f9f9;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: move;
        }
        .menu-item .dashicons { margin-right: 10px; color: #666; }
        .menu-roles { margin-left: 20px; min-width: 220px; }
        .menu-placeholder { background: #e0e0e0; height: 50px; border: 2px dashed #aaa; }
        </style>
        <?php
    }

    public static function save_menu() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'csdashwoo_save_menu')) {
            return;
        }

        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_POST['roles']) && is_array($_POST['roles'])) {
            $menu_layout = array();
            foreach ($_POST['roles'] as $slug => $roles) {
                $menu_layout[$slug] = array(
                    'roles' => is_array($roles) ? $roles : array()
                );
            }
            update_option('csdashwoo_menu_layout', $menu_layout);
            
            // Add success message
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success is-dismissible"><p>Menü ayarları başarıyla kaydedildi.</p></div>';
            });
        }
    }
}