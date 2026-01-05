<?php
/**
 * Plugin Name:       CSDashWoo
 * Plugin URI:        https://github.com/CariaSoft/csdashwoo
 * Description:       WooCommerce için özel yönetim paneli, dashboard widget'ları, menü düzenlemeleri ve bildirim merkezi sağlar.
 * Version:           1.1.0
 * Author:            CariaSoft
 * Author URI:        https://github.com/CariaSoft
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       csdashwoo
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) {
    exit; // Direkt erişimi engelle
}

// Sadece bir kere çalışsın
if (!class_exists('CSDashWoo')) :

    class CSDashWoo
    {
        /** @var CSDashWoo tekil instance */
        private static $instance = null;

        public static function instance()
        {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        private function __construct()
        {
            // Erken kontrol YAPMA! Sadece hook ekle
            add_action('plugins_loaded', [$this, 'init_plugin']);
        }

        public function init_plugin()
        {
            if (!$this->is_woocommerce_active()) {
                add_action('admin_notices', [$this, 'woocommerce_required_notice']);
                return; // Eklentiyi başlatma
            }

            // Woo aktif → normal başlatma
            $this->define_constants();
            $this->includes();
            $this->init_hooks();
        }

        private function is_woocommerce_active()
        {
            // En güvenilir yöntem: WooCommerce'in ana dosya yolu + active_plugins listesi
            $woo_plugin = 'woocommerce/woocommerce.php';

            if (in_array($woo_plugin, (array) get_option('active_plugins', []))) {
                return true;
            }

            // Multisite desteği (eğer ağda aktifse)
            if (is_multisite()) {
                $plugins = get_site_option('active_sitewide_plugins');
                if (isset($plugins[$woo_plugin])) {
                    return true;
                }
            }

            return false;
        }

        public function woocommerce_required_notice()
        {
            ?>
            <div class="notice notice-error">
                <p><strong>CSDashWoo:</strong> Bu eklenti çalışması için <strong>WooCommerce</strong> eklentisinin aktif olması gerekmektedir.</p>
            </div>
            <?php
        }

        private function define_constants()
        {
            define('CSDASHWOO_VERSION', '1.1.0');
            define('CSDASHWOO_PATH', plugin_dir_path(__FILE__));
            define('CSDASHWOO_URL', plugin_dir_url(__FILE__));
            define('CSDASHWOO_BASENAME', plugin_basename(__FILE__));
        }

        private function includes()
        {
            $includes = [
                'includes/admin/class-menu-labels.php',
                'includes/admin/class-menu-applier.php',
                'includes/admin/class-menu-others.php',
                'includes/admin/class-menu-layout.php',
                'includes/admin/class-menu-reader.php',
                'includes/admin/class-menu-ui.php',
                'includes/notifications/class-notification-center.php',
                'includes/dashboard/class-dashboard-widgets.php',
                'includes/settings/class-settings.php',
            ];

            foreach ($includes as $file) {
                $path = CSDASHWOO_PATH . $file;
                if (file_exists($path)) {
                    require_once $path;
                } else {
                    // Hata ayıklama için log (debug.log'da görünür)
                    error_log("CSDashWoo: Dosya bulunamadı → $path");
                }
            }
        }

        private function init_hooks()
        {
            // Admin menü ve ayar sayfası
            if (class_exists('Settings')) {
                add_action('admin_menu', ['Settings', 'add_admin_menu']);
                add_action('admin_init', ['Settings', 'register_settings']);
            }

            // Dashboard widget'ları
            if (class_exists('Dashboard_Widgets')) {
                add_action('wp_dashboard_setup', ['Dashboard_Widgets', 'register_widgets']);
            }

            // Diğer sınıflar için init (menü düzenlemeleri vs.)
            if (class_exists('Menu_UI')) {
                Menu_UI::init();
            }
            if (class_exists('Notification_Center')) {
                Notification_Center::init();
            }
        }

        // Önleme: clone ve unserialize
        private function __clone() {}
        public function __wakeup() { throw new Exception("Unserialize yapılamaz."); }
    }

    // Başlat
    CSDashWoo::instance();

endif;

// Eklenti action linkleri - güvenli versiyon
add_filter('plugin_action_links_csdashwoo/csdashwoo.php', 'csdashwoo_add_settings_link');

function csdashwoo_add_settings_link($actions) {
    $settings_link = '<a href="' . admin_url('options-general.php?page=csdashwoo-settings') . '" style="font-weight: 600; color: #2271b1;">Ayarlar</a>';
    array_unshift($actions, $settings_link);
    return $actions;
}