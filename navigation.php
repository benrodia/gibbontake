<?php

function siteNav($root,$data) {
    $has_slash = strpos($root,'/') !== false;
    $header_link = $has_slash ? $root : '/';
    $content = "
        <header id='header'>
        <section id='title'>
            <a href='".$header_link."'>
                <h1>".$data['info']['title']."</h1>
            </a>   
        </section>
        <ul class='nav'>
    ";

    foreach($data['info']['pages'] as $page) {
        $is_active = strpos($_SERVER['REQUEST_URI'],$root.$page['link'])!==false;
        $content .= "
            <li class='link".($is_active?" active":'')."'>
                <a href='".$root.$page['link']."'>".$page['name']."</a>
            </li>
        ";
    }
    $content .= "</ul></header>";
    
    return $content;
}


function chapterNav($comic, $cur) {
    $nav = "<section class='chapter-nav'>
        <h3>" . $comic['name'] . "</h3>
        <div class='links'>";
        
    for ($i=0; $i < count($comic['chapters']); $i++) {
        $chapter = $comic['chapters'][$i];
        $classes = $cur === $i ? 'disabled' : '';
        $nav .= "<a href='../../..".$comic['page_dir'].$chapter['link']."' class=" . $classes ."> 
        <button>" . $chapter['name'] . "</button>
        </a>";
    }
    $nav .= "</div></section>";
    return $nav;
}


function pageNav($comic, $cur, $path='../../..') {
    $inner = '';
    foreach($comic['pages'] as $ind => $page) {
        $classes = $cur===$ind ? 'disabled' : '';
        $inner .= "<div class='row'><h3>".($ind+1)."</h3>
        <a href='".$path.$comic['page_dir'].$page['link']."' class='".$classes."'>
        <button>".$page['title']."</button></a></div>";
    }
    return makeModal($comic,$inner);
}

function pageNavLazy($comic, $cur, $path='../../..') {
    $images = glob($path.$comic['image_dir'].'/*');
    $inner = '';
    foreach($images as $ind => $image) {
        $classes = $cur===$ind ? 'disabled' : '';
        $inner .= "<div class='row'><h3>".($ind+1)."</h3>
        <a href='".$path.$comic['page_dir'].'/'.clean(pathinfo($image)['filename'])."' class='".$classes."'>
        <button>".basename($image)."</button></a></div>";
    }
    return makeModal($comic,$inner);
}

function makeModal($comic, $inner) {
    $id = clean($comic['name']);
    $btn = "<button onclick='show(\"".$id."\");'>All Pages</button>";
    $nav = "<div id='".$id."' class='modal hide'>
    <div class='bg' onclick='hide(\"".$id."\");'></div>
    <div class='inner'>
    <div class='header'><button id='exit' onclick='hide(\"".$id."\");'>✖️</button>
        <h2>".$comic['name']."</h2>
    </div>
    <div class='list'>";
    $nav .= $inner;
    $nav .= "</div></div></div>";
    return $btn.$nav;
}


function gen_link($dir,$merch) {
    return "<a href='".$merch['url']."' target='_'>
        <img src='".$dir.$merch['icon']."' class='icon'/>"
        .$merch['text'].
    "</a>";
}

function merch_links($dir,$comic,$page) {
    $content = "<div class='merch links'>";
    if(isset($comic['merch'])) foreach($comic['merch'] as $merch) $content .= gen_link($dir,$merch);
    if(isset($page) && isset($page['merch'])) foreach($page['merch'] as $merch) $content .= gen_link($dir,$merch);
        
    $content .= "</div>";
    return $content;
}


?>