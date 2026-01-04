<?php

namespace CariaSoft\CSDashWoo\Notifications;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Notification_Center {

    const NOTIFICATION_OPTION = 'csdashwoo_notifications';
    
    public static function init() {
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_assets' ] );
        add_action( 'admin_bar_menu', [ __CLASS__, 'add_notification_icon' ], 999 );
        add_action( 'wp_ajax_csdashwoo_dismiss_notification', [ __CLASS__, 'dismiss_notification' ] );
        add_action( 'wp_ajax_csdashwoo_get_notifications', [ __CLASS__, 'get_notifications_ajax' ] );
    }

    public static function enqueue_assets( $hook ) {
        // Enqueue notification-related scripts and styles
        wp_enqueue_style(
            'csdashwoo-notifications',
            CSDASHWOO_URL . 'assets/notifications.css',
            [],
            CSDASHWOO_VERSION
        );
        
        wp_enqueue_script(
            'csdashwoo-notifications',
            CSDASHWOO_URL . 'assets/notifications.js',
            [ 'jquery' ],
            CSDASHWOO_VERSION,
            true
        );
        
        wp_localize_script( 'csdashwoo-notifications', 'csdashwoo_notif', [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'csdashwoo_notif_nonce' )
        ]);
    }

    public static function add_notification_icon( $wp_admin_bar ) {
        $notifications = self::get_unread_notifications();
        $count = count( $notifications );
        
        $title = $count > 0 ? sprintf( 'Bildirimler (%d)', $count ) : 'Bildirimler';
        $icon = $count > 0 ? '<span class="ab-icon csdashwoo-notif-icon">🔔 <span class="notif-count">' . $count . '</span></span>' : '<span class="ab-icon csdashwoo-notif-icon">🔔</span>';
        
        $wp_admin_bar->add_node( [
            'id'    => 'csdashwoo-notifications',
            'title' => $title,
            'href'  => '#csdashwoo-notifications-panel',
            'meta'  => [
                'class' => 'csdashwoo-notifications-menu',
            ]
        ] );
    }

    public static function get_unread_notifications() {
        $all_notifications = self::get_all_notifications();
        return array_filter( $all_notifications, function( $notification ) {
            return ! isset( $notification['read'] ) || ! $notification['read'];
        });
    }

    public static function get_all_notifications() {
        $notifications = get_option( self::NOTIFICATION_OPTION, [] );
        // Sort by date, newest first
        usort( $notifications, function( $a, $b ) {
            return strtotime( $b['date'] ) - strtotime( $a['date'] );
        });
        return $notifications;
    }

    public static function add_notification( $type, $title, $message, $link = '', $date = null ) {
        if ( ! $date ) {
            $date = current_time( 'mysql' );
        }
        
        $notification = [
            'id' => uniqid(),
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'date' => $date,
            'read' => false
        ];
        
        $notifications = self::get_all_notifications();
        array_unshift( $notifications, $notification ); // Add to beginning
        
        // Keep only last 50 notifications
        $notifications = array_slice( $notifications, 0, 50 );
        
        update_option( self::NOTIFICATION_OPTION, $notifications );
    }

    public static function dismiss_notification() {
        check_ajax_referer( 'csdashwoo_notif_nonce', 'nonce' );
        
        $notification_id = sanitize_text_field( $_POST['id'] );
        
        $notifications = self::get_all_notifications();
        foreach ( $notifications as &$notification ) {
            if ( $notification['id'] === $notification_id ) {
                $notification['read'] = true;
                break;
            }
        }
        
        update_option( self::NOTIFICATION_OPTION, $notifications );
        
        wp_send_json_success();
    }

    public static function get_notifications_ajax() {
        check_ajax_referer( 'csdashwoo_notif_nonce', 'nonce' );
        
        $notifications = self::get_unread_notifications();
        
        wp_send_json_success( [ 'notifications' => $notifications ] );
    }
}