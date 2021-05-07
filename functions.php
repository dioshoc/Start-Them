<?php

/*Чистка от кода от хуков*/
add_filter('show_admin_bar', '__return_false');

remove_action('wp_head',             'print_emoji_detection_script', 7);
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('wp_print_styles',     'print_emoji_styles');
remove_action('admin_print_styles',  'print_emoji_styles');

remove_action('wp_head', 'wp_resource_hints', 2); //remove dns-prefetch
remove_action('wp_head', 'wp_generator'); //remove meta name="generator"
remove_action('wp_head', 'wlwmanifest_link'); //remove wlwmanifest
remove_action('wp_head', 'rsd_link'); // remove EditURI
remove_action('wp_head', 'rest_output_link_wp_head'); // remove 'https://api.w.org/
remove_action('wp_head', 'rel_canonical'); //remove canonical
remove_action('wp_head', 'wp_shortlink_wp_head', 10); //remove shortlink
remove_action('wp_head', 'wp_oembed_add_discovery_links'); //remove alternate


if (!defined('_S_VERSION')) {
    // Replace the version number of the theme on each release.
    define('_S_VERSION', '1.0.0.0');
}

if (!function_exists('them_setup')) :
    function them_setup()
    {
        load_theme_textdomain('them', get_template_directory() . '/languages');

        add_theme_support('automatic-feed-links');

        add_theme_support('title-tag');

        add_theme_support('post-thumbnails');

        // This theme uses wp_nav_menu() in one location.
        register_nav_menus(
            array(
                'menu-1' => esc_html__('Primary', 'them'),
            )
        );

        add_theme_support(
            'html5',
            array(
                'search-form',
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
                'style',
                'script',
            )
        );

        // Set up the WordPress core custom background feature.
        add_theme_support(
            'custom-background',
            apply_filters(
                'them_custom_background_args',
                array(
                    'default-color' => 'ffffff',
                    'default-image' => '',
                )
            )
        );

        // Add theme support for selective refresh for widgets.
        add_theme_support('customize-selective-refresh-widgets');

        add_theme_support(
            'custom-logo',
            array(
                'height'      => 250,
                'width'       => 250,
                'flex-width'  => true,
                'flex-height' => true,
            )
        );
    }
endif;
add_action('after_setup_theme', 'them_setup');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function them_content_width()
{
    $GLOBALS['content_width'] = apply_filters('them_content_width', 640);
}
add_action('after_setup_theme', 'them_content_width', 0);

function them_scripts()
{
    // Тож чистка от ненужных отображени1й в коде
    wp_dequeue_style('wp-block-library');
    wp_deregister_script('wp-embed');

    // Подключение стилей
    wp_enqueue_style('them-style', get_stylesheet_uri(), array(), _S_VERSION);
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap', [], _S_VERSION);
    wp_style_add_data('them-style', 'rtl', 'replace');

    // Подключение Скриптов
    wp_enqueue_script('them-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true);
    wp_enqueue_script('them-main', get_template_directory_uri() . '/js/main.js', array(), _S_VERSION, true);
    wp_enqueue_script('them-start-script', get_template_directory_uri() . '/js/start-script.js', array(), _S_VERSION, true);

    //bootstrap
    wp_enqueue_style('bootstrap-style', get_template_directory_uri() . '/node_modules/bootstrap/dist/css/bootstrap.min.css');
    wp_deregister_script('bootstrap');
    wp_register_script('bootstrap', get_template_directory_uri() . '/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js');
    wp_enqueue_script('bootstrap');

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'them_scripts');

// Собственный тип записи

add_action('init', 'create_post_type');
function create_post_type()
{
    register_post_type(
        'news',
        array(
            'labels' => array( // добавляем новые элементы в административную частьку
                'name' => __('Новости'),
                'singular_name' => __('Новости'),
                'has_archive' => true,
                'add_new' => 'Добавить новую Новость',
                'not_found' => 'Ничего не найдено',
                'not_found_in_trash' => ''
            ),
            'hierarchical' => 'true',
            'menu_position' => 5, // позиция в меню сразу за запясями
            'rewrite' => 'news', //сслыка на сайт
            'public' => true,
            'has_archive' => true,
            'supports' => array( //добавляем элементы в редактор
                'title',
                'editor',
                'author',
                'trackbacks',
                'thumbnail',
                'page-attributes',
                'post-formats',
                'custom-fields'
            ),
            'taxonomies' => array('category', 'post_tag') //добавляем к записям необходимый набор таксономий
        )
    );
}

// Поддержка SCG

add_filter('upload_mimes', 'svg_upload_allow');

# Добавляет SVG в список разрешенных для загрузки файлов.
function svg_upload_allow($mimes)
{
    $mimes['svg']  = 'image/svg+xml';

    return $mimes;
}

add_filter('wp_check_filetype_and_ext', 'fix_svg_mime_type', 10, 5);

# Исправление MIME типа для SVG файлов.
function fix_svg_mime_type($data, $file, $filename, $mimes, $real_mime = '')
{

    // WP 5.1 +
    if (version_compare($GLOBALS['wp_version'], '5.1.0', '>='))
        $dosvg = in_array($real_mime, ['image/svg', 'image/svg+xml']);
    else
        $dosvg = ('.svg' === strtolower(substr($filename, -4)));

    // mime тип был обнулен, поправим его
    // а также проверим право пользователя
    if ($dosvg) {

        // разрешим
        if (current_user_can('manage_options')) {

            $data['ext']  = 'svg';
            $data['type'] = 'image/svg+xml';
        }
        // запретим
        else {
            $data['ext'] = $type_and_ext['type'] = false;
        }
    }

    return $data;
}
add_filter('wp_prepare_attachment_for_js', 'show_svg_in_media_library');

# Формирует данные для отображения SVG как изображения в медиабиблиотеке.
function show_svg_in_media_library($response)
{

    if ($response['mime'] === 'image/svg+xml') {

        // С выводом названия файла
        $response['image'] = [
            'src' => $response['url'],
        ];
    }

    return $response;
}

// Чистка миниатюр

add_filter('woocommerce_get_image_size_thumbnail', 'add_thumbnail_size', 1, 10);
function add_thumbnail_size($size)
{

    $size['width'] = 300;
    $size['height'] = 300;
    $size['crop']   = 1; //0 - не обрезаем, 1 - обрезка
    return $size;
}
add_filter('woocommerce_get_image_size_single', 'add_single_size', 1, 10);
function add_single_size($size)
{

    $size['width'] = 600;
    $size['height'] = 600;
    $size['crop']   = 0;
    return $size;
}
add_filter('woocommerce_get_image_size_gallery_thumbnail', 'add_gallery_thumbnail_size', 1, 10);
function add_gallery_thumbnail_size($size)
{

    $size['width'] = 100;
    $size['height'] = 100;
    $size['crop']   = 1;
    return $size;
}

add_filter('woocommerce_default_address_fields', 'customise_postcode_fields');
function customise_postcode_fields($address_fields)
{
    $address_fields['postcode']['required'] = false;
    $address_fields['address_1']['required'] = false;
    return $address_fields;
}