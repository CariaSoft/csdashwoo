<?php
/**
 * Plugin Name: CSDashWoo
 * Description: WordPress WooCommerce yönetim paneli özelleştirme eklentisi
 * Version: 1.0.1
 * Author: CariaSoft
 */

// Erişim engeli
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Ana sınıf dosyası
if ( ! class_exists( 'CSDashWoo' ) ) {
    
    class CSDashWoo {
        
        public function __construct() {
            // WooCommerce aktif değilse otomatik olarak disable et
            if ( ! $this->is_woocommerce_active() ) {
                add_action( 'admin_notices', [ $this, 'woocommerce_required_notice' ] );
                return;
            }
            
            $this->define_constants();
            $this->includes();
            $this->init_classes();
        }
        
        private function is_woocommerce_active() {
            return class_exists( 'WooCommerce' );
        }
        
        public function woocommerce_required_notice() {
            echo '<div class="notice notice-error"><p><strong>CSDashWoo:</strong> Bu eklenti WooCommerce\'un aktif olmasına ihtiyaç duyar.</p></div>';
        }
        
        private function define_constants() {
            define( 'CSDASHWOO_PATH', plugin_dir_path( __FILE__ ) );
            define( 'CSDASHWOO_URL', plugin_dir_url( __FILE__ ) );
            define( 'CSDASHWOO_VERSION', '1.0.0' );
        }
        
        private function includes() {
            // Buraya diğer require satırları eklenecek
            require_once CSDASHWOO_PATH . 'includes/admin/class-menu-labels.php';
            require_once CSDASHWOO_PATH . 'includes/admin/class-menu-applier.php';
            require_once CSDASHWOO_PATH . 'includes/admin/class-menu-others.php';
            require_once CSDASHWOO_PATH . 'includes/admin/class-menu-layout.php';
            require_once CSDASHWOO_PATH . 'includes/admin/class-menu-reader.php';
            require_once CSDASHWOO_PATH . 'includes/admin/class-menu-ui.php';
            require_once CSDASHWOO_PATH . 'includes/notifications/class-notification-center.php';
            require_once CSDASHWOO_PATH . 'includes/dashboard/class-dashboard-widgets.php';
            require_once CSDASHWOO_PATH . 'includes/settings/class-settings.php';
        }
        
        private function init_classes() {
            // Initialize the settings class
            \CariaSoft\CSDashWoo\Settings\Settings::init();
            \CariaSoft\CSDashWoo\Admin\Menu_UI::init();
            \CariaSoft\CSDashWoo\Notifications\Notification_Center::init();
            \CariaSoft\CSDashWoo\Dashboard\Dashboard_Widgets::init();
        }
    }
    
    new CSDashWoo();
}