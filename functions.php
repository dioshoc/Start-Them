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

function them_scripts()
{
    // Тож чистка от ненужных отображений в коде
    wp_dequeue_style('wp-block-library');
    wp_deregister_script('wp-embed');
    wp_deregister_script('bootstrap');

    // Подключение шрифтов
    //wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap', [], _S_VERSION);

    // Подключение стилей
    wp_enqueue_style('them-style', get_stylesheet_uri(), array(), _S_VERSION);

    // Подключение Скриптов
    wp_enqueue_script('them-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true);
    wp_enqueue_script('them-main', get_template_directory_uri() . '/js/main.js', array(), _S_VERSION, true);
    wp_enqueue_script('them-start-script', get_template_directory_uri() . '/js/start-script.js', array(), _S_VERSION, true);

    // jQuery
    wp_register_script('jQuery', get_template_directory_uri() . '/node_modules/jquery/dist/jquery.min.js');
    wp_enqueue_script('jQuery');

    //Bootstrap
    wp_enqueue_style('bootstrap-style', get_template_directory_uri() . '/node_modules/bootstrap/dist/css/bootstrap.min.css');
    wp_register_script('bootstrap', get_template_directory_uri() . '/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js');
    wp_enqueue_script('bootstrap');
}
add_action('wp_enqueue_scripts', 'them_scripts');



function more_post_capabilities()
{
    // Автоматический заголовок после фавикона
    add_theme_support('title-tag');
    // Добавление изображения для сраниц и постов
    add_theme_support('post-thumbnails');
    // Добавление собственной миниатюры
    add_image_size('post_thumb', 1300, 300, true);
}
add_action('after_setup_theme', 'more_post_capabilities');

// фильттры для краткого описания постов
add_filter('excerpt_length', function () {
    return 20;
});
add_filter('excerpt_more', 'new_excerpt_more');
function new_excerpt_more($more)
{
    global $post;
    return '<a href="' . get_permalink($post) . '"> Читать дальше...</a>';
}

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
                'add_new' => 'Добавить новость',
                'not_found' => 'Ничего не найдено',
                'not_found_in_trash' => ''
            ),
            'hierarchical' => 'true',
            'menu_position' => 5, // позиция в меню сразу за запясями
            'rewrite' => 'news', //сслыка на сайте
            'public' => true,
            'has_archive' => true,
            'supports' => array( //добавляем элементы в редактор
                'title',
                'editor',
                'author',
                'trackbacks',
                'page-attributes',
                'post-formats',
                //'custom-fields',
                'thumbnail',
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

// Чистка миниатюр от всех кроме собственных
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

// Дополнительные поля в разел новости
add_action('add_meta_boxes', 'my_extra_fields', 1);

function my_extra_fields()
{
    add_meta_box('extra_fields', 'Дополнительные поля', 'extra_fields_box_func', 'news', 'normal', 'high');
}

function extra_fields_box_func($post)
{
?>
<p><label>Ваше имя <input type="text" name="extra[title]" value="<?php echo get_post_meta($post->ID, 'title', 1); ?>"
            style="width:50%" /></label></p>

<input type="hidden" name="extra_fields_nonce" value="<?php echo wp_create_nonce(__FILE__); ?>" />
<?php
}

// включаем обновление полей при сохранении
add_action('save_post', 'my_extra_fields_update', 0);

## Сохраняем данные, при сохранении поста
function my_extra_fields_update($post_id)
{
    // базовая проверка
    if (
        empty($_POST['extra'])
        || !wp_verify_nonce($_POST['extra_fields_nonce'], __FILE__)
        || wp_is_post_autosave($post_id)
        || wp_is_post_revision($post_id)
    )
        return false;

    // Все ОК! Теперь, нужно сохранить/удалить данные
    $_POST['extra'] = array_map('sanitize_text_field', $_POST['extra']); // чистим все данные от пробелов по краям
    foreach ($_POST['extra'] as $key => $value) {
        if (empty($value)) {
            delete_post_meta($post_id, $key); // удаляем поле если значение пустое
            continue;
        }

        update_post_meta($post_id, $key, $value); // add_post_meta() работает автоматически
    }

    return $post_id;
}