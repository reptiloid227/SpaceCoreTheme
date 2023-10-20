<?php


/**
 * Включение отрывка у страниц
 * @return void
 */
function page_excerpt():void
{
    add_post_type_support('page', array('excerpt'));
}

add_action('init', 'page_excerpt');

add_filter(/**
 * @return int
 */ 'excerpt_length', function () {
    return 20;
});

add_filter(/**
 * @param $more
 * @return string
 */ 'excerpt_more', function ($more) {
    return '...';
});


