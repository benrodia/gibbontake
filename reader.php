
<?php 

function getGame($dir) {
    $content = "<div id='gameContainer' style='width: 800px; height: 600px; margin: auto'></div>";
    $content .= "<script src='".$dir."/UnityLoader.js'></script>";
    $content .= "<script>const gameInstance = UnityLoader.instantiate('gameContainer', '".$dir."/WebTest20.json')</script>";
    return $content;
}

function getIframe($url) {
    return "<iframe src='".$url."' frameborder='0' allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share' allowfullscreen></iframe>";
}

function reader_simple($dir,$comic,$page_index) {
    $chapter = $comic['chapters'][$page_index];

    $imgs = "<div class='imgs'>";
    for ($i=0; $i < $chapter['length']; $i++) { 
        $ind = $chapter['start'] + $i;
        $fn = find_image($dir.$comic['image_dir'],$ind);
        $imgs .= "<img src='".$dir.$comic['image_dir']."/".$fn."' alt='Page ".$ind."' />"; 
    }
    $imgs .= "</div>";
        
    return "<main id='reader' class='simple'>" . 
        chapterNav($comic,$page_index) .
        $imgs .
        chapterNav($comic,$page_index) .
        merch_links($dir,$comic,$page_index).
        "</main>"
    ;  
}

function reader_cyoa($dir,$comic,$page_index) {
    $content = ''; $imgs = ''; $prompt = ''; $has_prompt = false;
    $page = $comic['pages'][$page_index];
    $content .= "<main id='reader' class='cyoa'><div class='inner'>
        <h3 class='title'>".$page['title']."</h3>";
    
    if(isset($page['content'])) {
        $content .= "<div class='text'>";
        foreach($page['content'] as $media) {
            if(isset($media['url'])) $content .= "<a href='".$media['url']."' target='_'>";
            if(isset($media['link'])) $content .= "<a href='".$dir.$comic['page_dir'].$media['link']."'>";
            if(isset($media['iframe'])) $content .= getIframe($media['iframe']);
            if(isset($media['game'])) {
                $content .= getGame($dir.$media['game']['dir']);
            }
            if(isset($media['image'])) {
                $fn = find_image($dir.$comic['image_dir'],$media['image']);
                $content .= "<img src='".$dir.$comic['image_dir']."/".$fn."' alt='".($fn||$media['image'])."'/>";
            }
            if(isset($media['text'])) $content .= "<p>".$media['text']."</p>";
            if(isset($media['prompt'])) {
                $content .= "<span class='prompt'>".$media['prompt']."</span>";
                $has_prompt = true;
            }
 
            
            if(isset($media['link'])||isset($media['url'])) $content .= "</a>";
        }
        $content .= "</div>";
    }


    if(isset($page['prompts'])) foreach($page['prompts'] as $pr) {
        $prompt .= "<a href='".$dir.$comic['page_dir'].$pr['link']."' class='prompt'>" . 
            $pr['text'] .
        "</a>";
        $has_prompt = true;
    }
    if(!$has_prompt) {
        $last_index = $page_index - 1;
        $has_last = $last_index >= 0;
        $last_link = $has_last ? $dir.$comic['page_dir'].$comic['pages'][$last_index]['link'] : null;

        $next_index = $page_index + 1;
        $has_next = count($comic['pages']) > $next_index;
        $next_link = $has_next ? $dir.$comic['page_dir'].$comic['pages'][$next_index]['link'] : null;

        if($has_next) {
            $next_page =$comic['pages'][$next_index];
            $prompt = "<a href='".$dir.$comic['page_dir'].$next_page['link']."' class='prompt'>" . 
                $next_page['title'] .
            "</a>";
        }
        if(!isset($page['no_arrow_nav'])) $content .= arrowkey_nav($last_link,$next_link);
    }
    
    if(isset($page['iframe'])) $content .= getIframe($page['iframe']);

    if(isset($page['images'])) foreach($page['images'] as $img_num) {
        $fn = find_image($dir.$comic['image_dir'],$img_num);

        $imgs .= "<img src='".$dir.$comic['image_dir']."/".$fn."' alt='".$fn."' />"; 
    }
    $text = '<div class="text">';
    if(isset($page['text'])) {
        foreach($page['text'] as $p) $text .= "<p>".$p."</p>"; 
    }
    if(isset($page['code'])) {
        $text .= '<div class="code"><code>';
        foreach($page['code'] as $p) {
            if(is_string($p)) $text .= "<b>".$p."</b><br>";
            elseif(isset($p['text'])) {
                $color = isset($p['color']) ? $p['color'] : "";
                $text .= "<b style='color:".$color."'>".$p['text']."</b><br>";
            }
        } 
        $text .= '</code></div>';
    }

    $text .= $prompt."</div>";
    $content .= $imgs .$text;   
    
    if(isset($page['game'])) $content .= getGame($dir.$page['game']['dir']);

    $content .= "<div class='links'>".pageNav($comic,$page_index).merch_links($dir,$comic,$page_index)."</div>";
    $content .= "</div></main>";
    
    return $content;
}

function reader_lazy($dir,$comic,$page_index) {
    $folder = glob(__DIR__.$comic['image_dir'].'/*');
    $prompt = '';
    $title = basename($folder[$page_index]);
    $img = "<img src='".$dir.$comic['image_dir'].'/'.$title."' alt='".$folder[$page_index]."' />";
    
    $last_index = $page_index - 1;
    $has_last = $last_index>=0;
    $last_link = $has_last ? $dir.$comic['page_dir']."/".clean(pathinfo($folder[$last_index])['filename']):null;            
    $prompt .= "<a href='".$last_link."' class='".($has_last?'':'disabled')."'>Last</a>";

    $prompt .= pageNavLazy($comic,$page_index);

    $next_index = $page_index + 1;
    $has_next = count($folder) > $next_index;
    $next_link = $has_next ? $dir.$comic['page_dir']."/".clean(pathinfo($folder[$next_index])['filename']):null;
    $prompt .= "<a href='".$next_link."' class='".($has_next?'':'disabled')."'>Next</a>";

    return "<main id='reader' class='cyoa'>
        <div class='inner'>" 
            . $img .
            "<div class='lazy-nav'>".$prompt."</div>". 
        "</div></main>"
        .arrowkey_nav($last_link,$next_link)
    ;
}


function reader($dir, $comic_name, $page_index) {
    $data = json_decode(file_get_contents($dir.'/data.json'), true);

    $comic = find_object_by_key($data['comics'], 'name', $comic_name);

    if(!$comic) return "<main id='reader'>We're having trouble finding that comic</main>";

    $content = '';

    if($comic['format'] == 'simple') $content .= reader_simple($dir,$comic,$page_index);
    if($comic['format'] == 'cyoa') $content .= reader_cyoa($dir,$comic,$page_index);
    if($comic['format'] == 'lazy') $content .= reader_lazy($dir,$comic,$page_index);

    return $content;
    
}
# BATCH RENAME FOLDER IN POWERSHELL VVV
# $i=1; Get-ChildItem . | %{Rename-Item $_ -NewName ('clarissa_{0:D4}{1}' -f $i++, $_.extension)}

?>
