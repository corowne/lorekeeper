<?php

function set_active($path, $class = 'active') {
    
    return call_user_func_array('Request::is', (array)$path) ? $class : '';

}

function add_help($text) {
    return '<i class="fas fa-question-circle help-icon" data-toggle="tooltip" title="'.$text.'"></i>';
}

function breadcrumbs($links) {
    $ret = '<nav><ol class="breadcrumb">';
    $count = 0;
    $ret .= '<li class="breadcrumb-item"><a href="'.url('/').'">'.config('app.name', 'Lorekeeper').'</a></li>';
    foreach($links as $key => $link) {
        $isLast = ($count == count($links) - 1);

        $ret .= '<li class="breadcrumb-item ';
        if($isLast) $ret .= 'active';
        $ret .= '">';

        if(!$isLast) $ret .= '<a href="'.url($link).'">';
        $ret .=  $key;
        if(!$isLast) $ret .= '</a>';

        $ret .= '</li>';

        $count++;
    }
    $ret .= '</ol></nav>';

    return $ret;
}

function format_date($timestamp) {
    return $timestamp->format('j F Y, H:i:s e');
}

function parse($text) {

    require_once(base_path().'/vendor/ezyang/htmlpurifier/library/HTMLPurifier.auto.php');
    
    $config = HTMLPurifier_Config::createDefault();
    $purifier = new HTMLPurifier($config);
    $text = $purifier->purify($text);

    return $text;
}