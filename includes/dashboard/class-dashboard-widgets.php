<?php

namespace CariaSoft\CSDashWoo\Dashboard;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Dashboard_Widgets {

    public static function init() {
        add_action( 'wp_dashboard_setup', [ __CLASS__, 'add_widgets' ] );
        add_action( 'admin_head', [ __CLASS__, 'dashboard_styles' ] );
    }

    public static function add_widgets() {
        // Only add widgets if they are enabled in settings
        $show_dashboard = get_option( 'csdashwoo_dashboard_enabled', 1 );
        
        if ( $show_dashboard ) {
            wp_add_dashboard_widget(
                'csdashwoo_dashboard_summary',
                'CSDashWoo Özet',
                [ __CLASS__, 'render_summary_widget' ]
            );
        }
    }

    public static function render_summary_widget() {
        // Get WooCommerce sales data
        $total_sales = self::get_total_sales();
        $pending_orders = self::get_pending_orders();
        $open_tasks = self::get_open_tasks();
        $recent_notifications = self::get_recent_notifications();
        ?>
        <div class="csdashwoo-dashboard-container">
            <div class="csdashwoo-stats-grid">
                <div class="csdashwoo-stat-card">
                    <h4>Toplam Satış</h4>
                    <p class="csdashwoo-stat-value"><?php echo $total_sales; ?></p>
                </div>
                <div class="csdashwoo-stat-card">
                    <h4>Bekleyen Sipariş</h4>
                    <p class="csdashwoo-stat-value"><?php echo $pending_orders; ?></p>
                </div>
                <div class="csdashwoo-stat-card">
                    <h4>Açık İşler</h4>
                    <p class="csdashwoo-stat-value"><?php echo $open_tasks; ?></p>
                </div>
            </div>
            
            <div class="csdashwoo-notifications-section">
                <h4>Son Bildirimler</h4>
                <ul class="csdashwoo-notifications-list">
                    <?php if ( ! empty( $recent_notifications ) ): ?>
                        <?php foreach ( $recent_notifications as $notification ): ?>
                            <li class="csdashwoo-notification-item">
                                <strong><?php echo esc_html( $notification['title'] ); ?></strong>
                                <span class="csdashwoo-notification-date"><?php echo esc_html( $notification['date'] ); ?></span>
                                <p><?php echo esc_html( $notification['message'] ); ?></p>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="no-notifications">Yeni bildirim yok</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <?php
    }

    public static function get_total_sales() {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return 'WooCommerce aktif değil';
        }
        
        // Get total sales from WooCommerce
        $total = 0;
        
        // Query orders with completed status
        $completed_orders = wc_get_orders( [
            'status' => [ 'wc-completed', 'wc-processing', 'wc-on-hold' ],
            'return' => 'ids',
            'limit' => -1
        ] );
        
        foreach ( $completed_orders as $order_id ) {
            $order = wc_get_order( $order_id );
            if ( $order ) {
                $total += $order->get_total();
            }
        }
        
        return wc_price( $total );
    }

    public static function get_pending_orders() {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return 0;
        }
        
        // Count orders with pending statuses
        $pending_orders = wc_get_orders( [
            'status' => [ 'wc-pending', 'wc-on-hold', 'wc-processing' ],
            'return' => 'ids',
            'limit' => -1
        ] );
        
        return count( $pending_orders );
    }

    public static function get_open_tasks() {
        // In a real implementation, this would check for actual open tasks
        // For now, we'll return a placeholder value
        return rand( 0, 5 );
    }

    public static function get_recent_notifications() {
        // Get recent notifications from the notification system
        if ( class_exists( '\\CariaSoft\\CSDashWoo\\Notifications\\Notification_Center' ) ) {
            $notifications = \CariaSoft\CSDashWoo\Notifications\Notification_Center::get_all_notifications();
            // Return only the 3 most recent
            return array_slice( $notifications, 0, 3 );
        }
        return [];
    }

    public static function dashboard_styles() {
        echo '<style>
        .csdashwoo-dashboard-container {
            padding: 10px 0;
        }
        
        .csdashwoo-stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .csdashwoo-stat-card {
            text-align: center;
            padding: 15px;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            background: #f9f9f9;
        }
        
        .csdashwoo-stat-card h4 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #666;
        }
        
        .csdashwoo-stat-value {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            color: #0073aa;
        }
        
        .csdashwoo-notifications-section h4 {
            margin: 0 0 15px 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .csdashwoo-notifications-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .csdashwoo-notification-item {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .csdashwoo-notification-item:last-child {
            border-bottom: none;
        }
        
        .csdashwoo-notification-item strong {
            display: block;
            margin-bottom: 5px;
        }
        
        .csdashwoo-notification-date {
            font-size: 11px;
            color: #999;
            float: right;
        }
        
        .no-notifications {
            color: #999;
            font-style: italic;
            text-align: center;
            padding: 10px;
        }
        
        @media (max-width: 768px) {
            .csdashwoo-stats-grid {
                grid-template-columns: 1fr;
            }
        }
        </style>';
    }
}