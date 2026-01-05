<?php
/**
 * CSDashWoo Dashboard Widget'ları - v1.0
 * Sadece 3 ana widget: Satış Özeti, Bekleyen İşlemler, Son Bildirimler
 */
if (!defined('ABSPATH')) {
    exit;
}

class Dashboard_Widgets {

    public static function enqueue_dashboard_styles($hook) {
        if (strpos($hook, 'index.php') !== false || strpos($hook, 'dashboard') !== false) {
            wp_enqueue_style(
                'csdashwoo-dashboard-widgets',
                CSDASHWOO_URL . 'assets/css/dashboard-widgets.css',
                [],
                '1.2.0'
            );
        }
    }

    public static function add_dashboard_body_class() {
        $screen = get_current_screen();
        if ($screen && $screen->id === 'dashboard') {
            $hide_defaults = get_option('csdashwoo_hide_default_widgets', false);
            if ($hide_defaults) {
                echo '<script>jQuery(document).ready(function($) { $("body").addClass("csdashwoo-hide-defaults"); });</script>';
            }
        }
    }

    public static function register_widgets() {
        // Varsayılan widget'ları gizle (ayar sayfasındaki checkbox ile)
        if (get_option('csdashwoo_hide_default_widgets', false)) {
            global $wp_meta_boxes;
            // Tüm core widget'ları temizle
            unset($wp_meta_boxes['dashboard']['normal']['core']);
            unset($wp_meta_boxes['dashboard']['side']['core']);
            unset($wp_meta_boxes['dashboard']['normal']['high']);
            unset($wp_meta_boxes['dashboard']['side']['high']);
            
            // WooCommerce ve diğer yaygın dashboard widget'larını kaldır
            remove_meta_box('woocommerce_dashboard_status', 'dashboard', 'normal');
            remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
            remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
            remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
            remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
            remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
            remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');
            remove_meta_box('dashboard_primary', 'dashboard', 'side');
            remove_meta_box('dashboard_secondary', 'dashboard', 'side');
            remove_meta_box('dashboard_activity', 'dashboard', 'normal');
            remove_meta_box('dashboard_site_health', 'dashboard', 'normal');
        }

        // Widget 1: Satış Özeti
        wp_add_dashboard_widget(
            'csdashwoo_sales_summary',
            'Satış Özeti',
            [self::class, 'render_sales_summary']
        );

        // Widget 2: Bekleyen İşlemler
        wp_add_dashboard_widget(
            'csdashwoo_pending_tasks',
            'Bekleyen İşlemler',
            [self::class, 'render_pending_tasks']
        );

        // Widget 3: Son Bildirimler (v1 için placeholder, v2'de gerçek bildirimler)
        wp_add_dashboard_widget(
            'csdashwoo_recent_notifications',
            'Son Bildirimler',
            [self::class, 'render_recent_notifications']
        );
    }

    /**
     * Satış Özeti Widget'ı
     */
    public static function render_sales_summary() {
        if (!class_exists('WooCommerce')) {
            echo '<p><em>WooCommerce aktif değil.</em></p>';
            return;
        }

        $today_sales   = 0;
        $month_sales   = 0;
        $total_orders  = wc_orders_count('completed') + wc_orders_count('processing');

        // Bugünkü satışlar
        $today_orders = wc_get_orders([
            'date_created' => 'today',
            'status'       => ['completed', 'processing'],
            'limit'        => -1,
        ]);
        foreach ($today_orders as $order) {
            $today_sales += $order->get_total();
        }

        // Bu ayki satışlar
        $this_month_orders = wc_get_orders([
            'date_created' => 'this_month',
            'status'       => ['completed', 'processing'],
            'limit'        => -1,
        ]);
        foreach ($this_month_orders as $order) {
            $month_sales += $order->get_total();
        }

        ?>
        <div class="csdashwoo-widget csdashwoo-sales">
            <div class="csdashwoo-widget-header">
                <span>📈</span> Satış Performansı
            </div>
            <div class="csdashwoo-widget-content">
                <div class="csdashwoo-list-item">
                    <span>Bugün</span>
                    <span class="csdashwoo-big-number"><?php echo wc_price($today_sales); ?></span>
                </div>
                <div class="csdashwoo-list-item">
                    <span>Bu Ay</span>
                    <span class="csdashwoo-big-number"><?php echo wc_price($month_sales); ?></span>
                </div>
                <div class="csdashwoo-list-item">
                    <span>Toplam Tamamlanan Sipariş</span>
                    <span><?php echo number_format_i18n($total_orders); ?></span>
                </div>
                <a href="<?php echo admin_url('edit.php?post_type=shop_order'); ?>" 
                   class="button button-primary csdashwoo-button">
                    Tüm Siparişleri İncele
                </a>
            </div>
        </div>
        <?php
    }

    /**
     * Bekleyen İşlemler Widget'ı
     */
    public static function render_pending_tasks() {
        if (!class_exists('WooCommerce')) return;

        $pending    = wc_orders_count('pending');
        $processing = wc_orders_count('processing');
        $on_hold    = wc_orders_count('on-hold');
        $refunded   = wc_orders_count('refunded');

        // Düşük stok (stok <= 5)
        $low_stock = wc_get_products([
            'limit'          => -1,
            'stock_quantity' => 5,
            'stock_status'   => 'instock',
        ]);
        $low_stock_count = count($low_stock);

        $new_reviews = wp_count_comments(['post_type' => 'product', 'status' => 'hold']);

        ?>
        <div class="csdashwoo-widget csdashwoo-pending">
            <div class="csdashwoo-widget-header">
                <span>⚠️</span> Acil İşlemler
            </div>
            <div class="csdashwoo-widget-content">
                <div class="csdashwoo-list-item">
                    <span>Bekleyen Sipariş</span>
                    <span class="csdashwoo-big-number"><?php echo $pending; ?></span>
                </div>
                <div class="csdashwoo-list-item">
                    <span>Hazırlanan Sipariş</span>
                    <span class="csdashwoo-big-number"><?php echo $processing; ?></span>
                </div>
                <div class="csdashwoo-list-item">
                    <span>Onay Bekleyen Yorum</span>
                    <span><?php echo $new_reviews->total_comments; ?></span>
                </div>
                <div class="csdashwoo-list-item">
                    <span>Düşük Stok Ürün</span>
                    <span class="csdashwoo-big-number"><?php echo $low_stock_count; ?></span>
                </div>
                <a href="<?php echo admin_url('edit.php?post_type=shop_order'); ?>" 
                   class="button csdashwoo-button">
                    Hepsini Yönet
                </a>
            </div>
        </div>
        <?php
    }

    /**
     * Son Bildirimler Widget'ı
     */
    public static function render_recent_notifications() {
        ?>
        <div class="csdashwoo-widget csdashwoo-notifications">
            <div class="csdashwoo-widget-header">
                <span>🔔</span> Bildirim Merkezi
            </div>
            <div class="csdashwoo-widget-content" style="text-align: center;">
                <p style="font-style: italic; color: #666;">
                    v2.0 ile tam aktif olacak
                </p>
                <ul style="text-align: left; margin: 15px 0; padding-left: 20px; color: #555;">
                    <li>Yeni sipariş anında bildirim</li>
                    <li>Sipariş durumu değişiklikleri</li>
                    <li>Onay bekleyen ürün yorumları</li>
                    <li>Contact Form 7 mesajları</li>
                    <li>Seçmeli bildirim kapatma</li>
                </ul>
                <p><strong>Yakında burada!</strong></p>
            </div>
        </div>
        <?php
    }
}