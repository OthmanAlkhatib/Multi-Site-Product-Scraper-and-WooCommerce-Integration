<?php
    require_once __DIR__."./../../../../wp-config.php";
    require_once(ABSPATH . 'wp-includes/category.php');
    header('Access-Control-Allow-Origin: *');

    $args = array(
        'taxonomy' => 'product_cat',
        'orderby'  => 'name',
        "hide_empty" => 0,
    );
    
    $categories_arr = array();
    
    $all_categories = get_categories($args);
    foreach ($all_categories as $item) {
        $categories_arr[] = $item->name;
    }
    
    echo json_encode($categories_arr);
?>