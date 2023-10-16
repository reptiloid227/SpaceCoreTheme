<?php

/**
 * Передача параметров в AJAX
 * @return void
 */
function addAJAX():void
{
    wp_localize_script('ajax-form', 'ajax_form_object', [
        'url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('ajax-form-nonce'),
    ]);
}

add_action('wp_enqueue_scripts', 'addAJAX');

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