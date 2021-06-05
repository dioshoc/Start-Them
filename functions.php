<?php
require "function/clean.php";

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
    wp_register_script('jQuery', get_template_directory_uri() . '/js/jquery-3.6.0.min.js');
    wp_enqueue_script('jQuery');

    //Bootstrap
    wp_enqueue_style('bootstrap-style', get_template_directory_uri() . '/bootstrap/css/bootstrap-grid.min.css');
    wp_register_script('bootstrap', get_template_directory_uri() . '/bootstrap/js/bootstrap.min.js');
    wp_enqueue_script('bootstrap');
}
add_action('wp_enqueue_scripts', 'them_scripts');

function more_post_capabilities()
{
    add_theme_support('custom-logo', [
        'height'      => 190,
        'width'       => 190,
        'flex-width'    => true,
        'flex-height'    => true,
        'header-text' => '',
        'unlink-homepage-logo' => true, // WP 5.5
    ]);
    // Автоматический заголовок после фавикона
    add_theme_support('title-tag');
    // Добавление изображения для сраниц и постов
    add_theme_support('post-thumbnails');
    // Добавление собственной миниатюры
    add_image_size('post_thumb', 1300, 300, true);
}
add_action('after_setup_theme', 'more_post_capabilities');

require "function/svg.php";

require "function/post.php";

require "function/thumbnail.php";