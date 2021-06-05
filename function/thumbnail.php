<?php
// Чистка миниатюр от всех кроме собственных
add_filter('woocommerce_get_image_size_thumbnail', 'add_thumbnail_size', 1, 10);
function add_thumbnail_size($size)
{
    $size['width'] = 300;
    $size['height'] = 300;
    $size['crop'] = 0; //0 - не обрезаем, 1 - обрезка
    return $size;
}
add_filter('woocommerce_get_image_size_single', 'add_single_size', 1, 10);
function add_single_size($size)
{
    $size['width'] = 600;
    $size['height'] = 600;
    $size['crop'] = 0;
    return $size;
}
add_filter('woocommerce_get_image_size_gallery_thumbnail', 'add_gallery_thumbnail_size', 1, 10);
function add_gallery_thumbnail_size($size)
{
    $size['width'] = 100;
    $size['height'] = 100;
    $size['crop'] = 0;
    return $size;
}
add_filter('woocommerce_default_address_fields', 'customise_postcode_fields');
function customise_postcode_fields($address_fields)
{
    $address_fields['postcode']['required'] = false;
    $address_fields['address_1']['required'] = false;
    return $address_fields;
}