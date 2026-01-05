<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Menu_Labels {

    /**
     * Slug => Yeni Başlık
     */
    public static function get_labels() {
        return apply_filters( 'csdashwoo_menu_labels', [
            'woocommerce'                     => __( 'Mağaza', 'csdashwoo' ),
            'edit.php?post_type=shop_order'   => __( 'Siparişler', 'csdashwoo' ),
            'edit.php?post_type=product'      => __( 'Ürünler', 'csdashwoo' ),
            'users.php'                       => __( 'Müşteriler', 'csdashwoo' ),
        ]);
    }

    /**
     * Admin menüye uygula
     */
    public static function apply() {
        global $menu;

        $labels = self::get_labels();

        foreach ( $menu as &$item ) {
            if ( isset( $item[2] ) && isset( $labels[ $item[2] ] ) ) {
                $item[0] = $labels[ $item[2] ];
            }
        }
    }
}