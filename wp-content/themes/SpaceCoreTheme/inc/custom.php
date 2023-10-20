<?php

/**
 * Передача параметров в AJAX
 * @return void
 */
function addAJAX():void
{
	wp_enqueue_scripts('ajax-form', get_template_directory_uri() . '/js/inc/ajax-form.js', ['jquery'], '1.0', true);

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


