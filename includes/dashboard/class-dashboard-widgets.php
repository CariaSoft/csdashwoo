<?php
/**
 * CSDashWoo Dashboard Widget'ları - v1.0
 * Sadece 3 ana widget: Satış Özeti, Bekleyen İşlemler, Son Bildirimler
 */
if (!defined('ABSPATH')) {
    exit;
}

class Dashboard_Widgets {

    public static function register_widgets() {
        // Varsayılan widget'ları gizle (ayar sayfasındaki checkbox ile)
        if (get_option('csdashwoo_hide_default_widgets', false)) {
            global $wp_meta_boxes;
            // Tüm core widget'ları temizle
            unset($wp_meta_boxes['dashboard']['normal']['core']);
            unset($wp_meta_boxes['dashboard']['side']['core']);
            unset($wp_meta_boxes['dashboard']['normal']['high']);
            unset($wp_meta_boxes['dashboard']['side']['high']);
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
        <div style="padding: 15px; background: linear-gradient(135deg, #e8f5e9, #c8e6c9); border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 15px; color: #2e7d32; font-size: 1.4em;">📈 Satış Performansı</h3>
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                <div>
                    <strong style="display: block; color: #555;">Bugün</strong>
                    <span style="font-size: 1.8em; font-weight: bold; color: #2e7d32;"><?php echo wc_price($today_sales); ?></span>
                </div>
                <div>
                    <strong style="display: block; color: #555;">Bu Ay</strong>
                    <span style="font-size: 1.6em; font-weight: bold; color: #388e3c;"><?php echo wc_price($month_sales); ?></span>
                </div>
            </div>
            <p style="margin: 10px 0; color: #555;">
                <strong>Toplam Tamamlanan Sipariş:</strong> <?php echo number_format_i18n($total_orders); ?>
            </p>
            <a href="<?php echo admin_url('edit.php?post_type=shop_order'); ?>" 
               class="button button-primary" 
               style="width: 100%; margin-top: 15px; text-align: center; padding: 10px;">
                Tüm Siparişleri İncele
            </a>
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
        <div style="padding: 15px; background: linear-gradient(135deg, #fff3e0, #ffe0b2); border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 15px; color: #ef6c00; font-size: 1.4em;">⚠️ Acil İşlemler</h3>
            <ul style="list-style: none; padding: 0; margin: 0;">
                <li style="padding: 10px 0; border-bottom: 1px solid #ffb74d;">
                    <strong>Bekleyen Sipariş</strong>
                    <span style="float: right; color: #d32f2f; font-weight: bold; font-size: 1.4em;"><?php echo $pending; ?></span>
                    <div style="clear:both;"></div>
                </li>
                <li style="padding: 10px 0; border-bottom: 1px solid #ffb74d;">
                    <strong>Hazırlanan Sipariş</strong>
                    <span style="float: right; color: #f57c00; font-weight: bold;"><?php echo $processing; ?></span>
                    <div style="clear:both;"></div>
                </li>
                <li style="padding: 10px 0; border-bottom: 1px solid #ffb74d;">
                    <strong>Onay Bekleyen Yorum</strong>
                    <span style="float: right; color: #ff8f00;"><?php echo $new_reviews->total_comments; ?></span>
                    <div style="clear:both;"></div>
                </li>
                <li style="padding: 10px 0;">
                    <strong>Düşük Stok Ürün</strong>
                    <span style="float: right; color: #d32f2f; font-weight: bold;"><?php echo $low_stock_count; ?></span>
                    <div style="clear:both;"></div>
                </li>
            </ul>
            <a href="<?php echo admin_url('edit.php?post_type=shop_order'); ?>" 
               class="button" 
               style="width: 100%; margin-top: 15px; text-align: center;">
                Hepsini Yönet
            </a>
        </div>
        <?php
    }

    /**
     * Son Bildirimler Widget'ı
     */
    public static function render_recent_notifications() {
        ?>
        <div style="padding: 15px; background: #f3e5f5; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center;">
            <h3 style="margin: 0 0 15px; color: #7b1fa2; font-size: 1.4em;">🔔 Bildirim Merkezi</h3>
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
        <?php
    }
}