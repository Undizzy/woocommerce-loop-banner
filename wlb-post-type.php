<?php
/**
 * Регистрирует произвольный тип записи
 */
function wlb_setup_post_type(){
    /**
     * Post Type: Баннеры.
     */

    $labels = [
        "name" => __( "Баннеры", "wlb_banner" ),
        "singular_name" => __( "Баннер", "wlb_banner" ),
        "menu_name" => __( "Баннеры", "wlb_banner" ),
        "all_items" => __( "Все Баннеры", "wlb_banner" ),
        "add_new" => __( "Добавить новый", "wlb_banner" ),
        "add_new_item" => __( "Добавить новый Баннер", "wlb_banner" ),
        "edit_item" => __( "Редактировать Баннер", "wlb_banner" ),
        "new_item" => __( "Новый Баннер", "wlb_banner" ),
        "view_item" => __( "Просмотр Баннера", "wlb_banner" ),
        "view_items" => __( "Посмотреть Баннеры", "wlb_banner" ),
        "search_items" => __( "Поиск Баннеров", "wlb_banner" ),
        "not_found" => __( "Баннеры не найдены", "wlb_banner" ),
        "not_found_in_trash" => __( "No Баннеры found in trash", "wlb_banner" ),
        "parent" => __( "Parent Баннер:", "wlb_banner" ),
        "featured_image" => __( "Изображение Баннера", "wlb_banner" ),
        "set_featured_image" => __( "Выбрать изображение для Баннера", "wlb_banner" ),
        "remove_featured_image" => __( "Удалить изображение для Баннера", "wlb_banner" ),
        "use_featured_image" => __( "Испоьлзовать как изображение для Баннера", "wlb_banner" ),
        "archives" => __( "Баннер archives", "wlb_banner" ),
        "insert_into_item" => __( "Insert into_Баннер", "wlb_banner" ),
        "uploaded_to_this_item" => __( "Upload to this Баннер", "wlb_banner" ),
        "filter_items_list" => __( "Filter Баннеры list", "wlb_banner" ),
        "items_list_navigation" => __( "Баннеры list navigation", "wlb_banner" ),
        "items_list" => __( "Баннеры list", "wlb_banner" ),
        "attributes" => __( "Баннеры attributes", "wlb_banner" ),
        "name_admin_bar" => __( "Баннер", "wlb_banner" ),
        "item_published" => __( "Баннер published", "wlb_banner" ),
        "item_published_privately" => __( "Баннер published privately.", "wlb_banner" ),
        "item_reverted_to_draft" => __( "Баннер reverted to draft.", "wlb_banner" ),
        "item_scheduled" => __( "Баннер scheduled", "wlb_banner" ),
        "item_updated" => __( "Баннер updated.", "wlb_banner" ),
        "parent_item_colon" => __( "Parent Баннер:", "wlb_banner" ),
    ];

    $args = [
        "label" => __( "Баннеры", "wlb_banner" ),
        "labels" => $labels,
        "description" => "",
        "public" => true,
        "publicly_queryable" => false,
        "show_ui" => true,
        "show_in_rest" => false,
        "rest_base" => "",
        "rest_controller_class" => "WP_REST_Posts_Controller",
        "has_archive" => false,
        "show_in_menu" => true,
        "show_in_nav_menus" => false,
        "delete_with_user" => false,
        "exclude_from_search" => true,
        "capability_type" => "post",
        "map_meta_cap" => true,
        "hierarchical" => false,
        "rewrite" => [ "slug" => "wlb_banner", "with_front" => false ],
        "query_var" => true,
        "menu_icon" => "dashicons-format-gallery",
        "supports" => [ "title", "thumbnail", "custom-fields" ],
        "taxonomies" => [ "product_cat", "product_tag" ],
    ];

    register_post_type( "wlb_banner", $args );
}
add_action( 'init', 'wlb_setup_post_type' );

require_once 'classes/class-Kama-Post-Meta-Box.php';

/**
 * Добавляет метабокс для указания ссылки в тип поста "Баннер"
 */
class_exists('Kama_Post_Meta_Box') && new Kama_Post_Meta_Box(
    array(
        'id'         => 'wlb',
        'title'      => 'Ссылка для баннера',
        'post_type'  => 'wlb_banner', // показывать только на страницах типа: wlb_banner
        'fields'     => array(
            'url' => array( 'title' => 'URL' ),
            'new_tab' => array(
                'type'=>'checkbox', 'desc'=>'Открывать ссылку в новой вкладке?'
            )
        ),
    )
);