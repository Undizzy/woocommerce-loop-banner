<?php

/**
 * Add admin submenu page
 */
function wlb_options_menu(){
    add_submenu_page(
        "edit.php?post_type=wlb_banner",
        "Настройки отображения баннеров",
        "Настройки",
        "manage_options",
        "manage_wlb_banner_options",
        "wlb_options_output"
    );
}
add_action("admin_menu", "wlb_options_menu");

/**
 * Выводит страницу с настройками плагина
 */
function wlb_options_output(){
    ?>
    <div class="wrap">
        <h2><?php echo get_admin_page_title(); ?></h2>
        <?php settings_errors(); ?>

        <form action="options.php" method="POST">
            <?php
            settings_fields( 'wlb_option_group' );     // скрытые защитные поля
            do_settings_sections( 'wlb_options' ); // секции с настройками (опциями). У нас она всего одна 'section_id'
            submit_button();
            ?>
        </form>

    </div>
    <?php
}

/**
 * Регистрируем настройки.
 * Настройки будут храниться в массиве, а не одна настройка = одна опция.
 */
function wlb_options(){
    // параметры: $option_group, $option_name, $sanitize_callback
    register_setting( 'wlb_option_group', 'wlb_options', 'wlb_sanitize_callback' );

    // параметры: $id, $title, $callback, $page
    add_settings_section( 'section_id', 'Основные настройки', '', 'wlb_options' );

    // параметры: $id, $title, $callback, $page, $section, $args
    add_settings_field('wlb_options_enable', 'Включить отображение баннеров', 'wlb_options_enable', 'wlb_options', 'section_id' );
    add_settings_field('wlb_options_ul_container', 'Родительский контейнер с товарами', 'wlb_options_ul_container', 'wlb_options', 'section_id' );
    add_settings_field('wlb_options_li_container', 'Контейнер с товаром', 'wlb_options_li_container', 'wlb_options', 'section_id' );
    add_settings_field('wlb_options_li_container_class', 'ID или Класс контейнера с товаром', 'wlb_options_li_container_class', 'wlb_options', 'section_id' );
    add_settings_field('wlb_options_min_product_random', 'После какого товара вставлять баннер уже можно', 'wlb_options_min_product_random', 'wlb_options', 'section_id' );
    add_settings_field('wlb_options_min_product_on_page', 'Минимальное количество товаров на странице для вывода баннера', 'wlb_options_min_product_on_page', 'wlb_options', 'section_id' );
}
add_action('admin_init', 'wlb_options');

## Заполняем опцию wlb_options_enable
function wlb_options_enable(){
    $val = get_option('wlb_options');
    $val = isset($val['enable']) ? $val['enable'] : null;
    ?>
    <label><input type="checkbox" name="wlb_options[enable]" value="1" <?php checked( 1, $val ) ?> /> Включить?</label>
    <?php
}
## Заполняем опцию wlb_options_ul_container
function wlb_options_ul_container(){
    $val = get_option('wlb_options');
    $val = $val ? $val['ul_container'] : null;
    ?>
    <label><input type="text" name="wlb_options[ul_container]" value="<?php echo esc_attr( $val ) ?>" /></label>
    <p class="wlb-description">
        <i>HTML Тег и ID(#) или Class (.) в котором содержатся все товары на странице | Например: ul#products | Оставьте пустым, что бы оставить значение по умолчанию.</i>
    </p>
    <?php
}
## Заполняем опцию wlb_options_li_container
function wlb_options_li_container(){
    $val = get_option('wlb_options');
    $val = $val ? $val['li_container'] : null;
    ?>
    <label><input type="text" name="wlb_options[li_container]" value="<?php echo esc_attr( $val ) ?>" /></label>
    <p class="wlb-description">
        <i>HTML Тег c отдельным товаром | Например: li | Оставьте пустым, что бы оставить значение по умолчанию.</i>
    </p>
    <?php
}
## Заполняем опцию wlb_options_li_container_class
function wlb_options_li_container_class(){
    $val = get_option('wlb_options');
    $val = $val ? $val['li_container_class'] : null;
    ?>
    <label><input type="text" name="wlb_options[li_container_class]" value="<?php echo esc_attr( $val ) ?>" /></label>
    <p class="wlb-description">
        <i>ID(#) или Class (.) HTML Тега c отдельным товаром | Например: .product-item | Оставьте пустым, что бы оставить значение по умолчанию.</i>
    </p>
    <?php
}
## Заполняем опцию wlb_options_min_product_random
function wlb_options_min_product_random(){
    $val = get_option('wlb_options');
    $val = $val ? $val['min_product_random'] : 4;
    ?>
    <label><input type="number" name="wlb_options[min_product_random]" value="<?php echo esc_attr( $val ) ?>" /></label>
    <p class="wlb-description">
        <i>До какого по счёту товара баннер не будет выводиться | По умолчанию: до 4-го товара, то есть начиная с 5-го </i>
    </p>
    <?php
}
## Заполняем опцию wlb_options_min_product_on_page
function wlb_options_min_product_on_page(){
    $val = get_option('wlb_options');
    $val = $val ? $val['min_product_on_page'] : 4;
    ?>
    <label><input type="number" name="wlb_options[min_product_on_page]" value="<?php echo esc_attr( $val ) ?>" /></label>
    <p class="wlb-description">
        <i>Если товаров меньше, чем указанно, то баннер выводиться не будет. | По умолчанию: 4 товара </i>
    </p>
    <?php
}
## Очистка данных
function wlb_sanitize_callback( $options ){
    // очищаем
    foreach( $options as $name => & $val ){
        if( $name == 'enable' )
            $val = intval( $val );

        if( $name == 'ul_container' )
            $val = strip_tags( $val );

        if( $name == 'li_container' )
            $val = strip_tags( $val );

        if( $name == 'li_container_class' )
            $val = strip_tags( $val );
    }

    return $options;
}