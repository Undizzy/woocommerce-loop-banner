<?php

/*
Plugin Name: Woo Loop Banner
Plugin URI: https://github.com/Undizzy/woocommerce-loop-banner
Description: Добаляет возможность отображения рекламного баннера в категориях и метках товаров в случайной позиции.
Version: 1.0
Author: Undizzy
Author URI: https://metronic.ml
License: GPL2
*/

require_once 'wlb-options.php';

require_once 'wlb-post-type.php';

register_activation_hook( __FILE__, 'wlb_install' );

/**
 * Проверяет наличие и активность плагина Woocommerce перед активацией
 */
function wlb_install(){
    // Проверяем, активен ли плагин woocommerce
    if (is_plugin_active('woocommerce/woocommerce.php') ) {

        // Запускаем функцию регистрации типа записи
        wlb_setup_post_type();

        // Сбрасываем настройки ЧПУ, чтобы они пересоздались с новыми данными
        flush_rewrite_rules();
    } else {

        wp_die('Установите и активируйте плагин Woocommerce, перед активацией этого плагина! Без Woocommerce этот плагин бесполезен.');
    }
}

/**
 * Делает запрос в БД, что бы получить имеющиеся баннеры для текущей страницы.
 *
 * @return bool|array FALSE, если баннеров нет | Массив с параметрами случайного баннера
 */
function wlb_banner(){
    $args = array(
        'post_type' => 'wlb_banner',
        'showposts' => -1,
        'tax_query' => [
            'relation' => 'OR',
            [
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => get_query_var('product_cat'),
            ],
            [
                'taxonomy' => 'product_tag',
                'field'    => 'slug',
                'terms'    => get_query_var('product_tag'),
            ]
        ]
    );

    $my_posts = new WP_Query;
    // делаем запрос
    $banners = $my_posts->query( $args );
    // Возвращаем данные
    if ( !empty($banners) ) {
        $index = rand(0, count($banners)-1);
        return $banner = array(
            'img'     => get_the_post_thumbnail($banners[$index], 'full'),
            'url'     => get_post_meta($banners[$index]->ID, 'wlb_url', true ),
            'new_tab' => get_post_meta($banners[$index]->ID, 'wlb_new_tab', true )
        );
    } else {
        return false;
    }
}

/**
 * Удаляет символ CSS идентификатора (ID или Class)
 *
 * @param $identifier string CSS Идентификатор
 * @return string|string[] Очищенная строка
 */
function trim_identifier($identifier){
    $identifier = str_replace('.', '', $identifier);
    $identifier = str_replace('#', '', $identifier);

    return $identifier;
}

/**
 * Выводит JS скрипт для вставки баннера между товарами
 */
function wlb_print_script(){
    // Получаем настройки плагина
    $wlb_options = get_option('wlb_options');

    if (isset($wlb_options['enable']) and $wlb_options['enable'] and is_woocommerce()):

        // Делаемм запрос и получаем изображение баннера или FALSE
        $banner = wlb_banner();

        if ($banner !== false): ?>

            <script>
                function getRandomInt(min, max) {
                    min = Math.ceil(min);
                    max = Math.floor(max);
                    return Math.floor(Math.random() * (max - min)) + min; //Максимум не включается, минимум включается
                }

                /* Получить родительский ul с товарами */
                let parentUl = document.querySelector('<?php echo !empty($wlb_options['ul_container']) ? $wlb_options['ul_container'] : 'ul#products' ?>');

                /* Получить список товаров на странице */
                let productList = document.querySelectorAll('<?php echo !empty($wlb_options['li_container']) ? $wlb_options['li_container'] : 'li'; echo !empty($wlb_options['li_container_class']) ? $wlb_options['li_container_class'] : '.product-item'?> ');

                /* Создать блок для баннера */
                let bannerBox = document.createElement('<?php echo !empty($wlb_options['li_container']) ? $wlb_options['li_container'] : 'li' ?>');
                bannerBox.classList.add('<?php echo !empty($wlb_options['li_container_class']) ? trim_identifier($wlb_options['li_container_class']) : 'product-item'?>', 'loop-banner');

                /* Заполнить созданный div баннером */
                bannerBox.innerHTML = '<figure class="product-inner"><a href="<?php echo $banner['url'] ?>" target="<?php echo $banner['new_tab'] ? '_blank' : '_self' ?>"><?php echo $banner['img'] ?></a></figure>';

                /* Получить рандомный индекс товара */
                let index = getRandomInt(<?php echo $wlb_options['min_product_random'] ?>, productList.length);

                /* Сделать вставку баннера */
                if (productList.length >= <?php echo $wlb_options['min_product_on_page'] ?>) {
                    parentUl.insertBefore(bannerBox, productList[index]);
                }
            </script>

        <?php endif;

    endif;
}

add_action('wp_footer', 'wlb_print_script');