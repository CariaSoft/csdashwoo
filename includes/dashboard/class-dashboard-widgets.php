<?php
if (!defined('ABSPATH')) {
    exit;
}

class Dashboard_Widgets {

    public static function register_widgets() {
        wp_add_dashboard_widget(
            'csdashwoo_test_widget',                // Widget ID
            'CSDashWoo Hoş Geldin',                 // Başlık
            [__CLASS__, 'render_test_widget']       // İçerik fonksiyonu
        );
    }

    public static function render_test_widget() {
        echo '<div style="padding: 10px;">';
        echo '<h3>Hoş geldiniz!</h3>';
        echo '<p>Bu, CSDashWoo eklentisinin test widget\'ıdır. Yakında gerçek widget\'lar burada olacak.</p>';
        echo '<p>Şu anki tarih: ' . date('d.m.Y') . '</p>';
        echo '</div>';
    }
}