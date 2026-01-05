<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Menu_Layout {

    public static function get() {
        // Default layout - can be filtered for customization
        return apply_filters( 'csdashwoo_menu_layout', [
            'index.php',                    // Dashboard
            'edit.php?post_type=product',   // Products
            'edit.php?post_type=shop_order', // Orders
            'edit.php?post_type=shop_coupon', // Coupons
            'woocommerce',                  // Analytics
            'wc-admin&path=/customers',     // Customers
            'edit.php?post_type=wp_block',  // Reusable Blocks
            'edit.php',                     // Posts
            'edit.php?post_type=page',      // Pages
            'upload.php',                   // Media
            'users.php',                    // Users
            'edit-comments.php',            // Comments
            'themes.php',                   // Appearance
            'plugins.php',                  // Plugins
            'options-general.php',          // Settings
        ]);
    }
    
    public static function is_locked() {
        return (bool) get_option( 'csdashwoo_menu_locked', 0 );
    }
    
    public static function save( $layout ) {
        update_option( 'csdashwoo_menu_layout', $layout );
    }
}