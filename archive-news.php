<?php

/**
 *  Template name: Новости
 * 
 *  Template post type: page
 *
 * @package Them

 */

get_header(); ?>
<main>
    <?php
    $args = array(
        'post_type'   => 'news',
        'paged' => get_query_var('paged'),
        'posts_per_page' => 2
    );

    $query = new WP_Query($args);
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post(); ?>

    <article class="post" href="<?php the_permalink(); ?>">
        <div class="entry-header">
            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

            <p class="post-meta">
                <time class="date" datatime="2014-01-14T11:24"><?php the_time('F jS, Y') ?></time> /
                <!-- Вывод категорий -->
                <span class="categories">
                    <?php the_category('/ '); ?>
                </span>
                <!-- Вывод тегов -->
                <span class="tags">
                    <?php the_tags("", " / ") ?>
                </span>
            </p>
        </div>
        <div class="post-thumb">
            <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('post_thumb'); ?></a>
        </div>
        <p class="font"><?php echo get_post_meta($post->ID, 'title', true); ?></з>
        <div class="post-content">

            <?php the_excerpt(); ?>
        </div>
        <?php }  // Конец while
        // Пагинация
        $current = absint(max(1, get_query_var('paged') ? get_query_var('paged') : get_query_var('page')));
        echo paginate_links(array(
            'total' => $query->max_num_pages,  // максимальное количество страниц
            'current' => $current,  // текущая страница
        ));
    }; //Конец if
        ?>
    </article>
</main>
<?php get_footer(); ?>