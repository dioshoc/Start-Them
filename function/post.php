<?php
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