<?php

namespace CariaSoft\CSDashWoo\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Menu_Reader {

    public static function get_menu_tree() {
        global $menu, $submenu;
        
        $menu_tree = [];
        
        foreach ( $menu as $item ) {
            if ( ! empty( $item[2] ) ) {
                $menu_tree[ $item[2] ] = [
                    'title' => $item[0],
                    'capability' => $item[1] ?? 'read',
                    'slug' => $item[2],
                    'has_submenu' => isset( $submenu[ $item[2] ] ) && ! empty( $submenu[ $item[2] ] )
                ];
            }
        }
        
        // Add submenu items if they're not already in the main menu
        foreach ( $submenu as $parent_slug => $sub_items ) {
            foreach ( $sub_items as $sub_item ) {
                $sub_slug = $sub_item[2];
                
                // Skip if this slug already exists in the main menu
                if ( ! isset( $menu_tree[ $sub_slug ] ) ) {
                    $menu_tree[ $sub_slug ] = [
                        'title' => $sub_item[0],
                        'capability' => $sub_item[1] ?? 'read',
                        'slug' => $sub_slug,
                        'parent' => $parent_slug
                    ];
                }
            }
        }
        
        return $menu_tree;
    }
}