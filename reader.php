
<?php 
    function clean($string) {
        return strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $string)));
    }

    function find_object_by_key($array, $key, $val){
        foreach ( $array as $element ) {
            if ( $val === $element[$key] ) {
                return $element;
            }
        }
        return false;
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

    function find_image($dir,$search) {
        $files = glob($dir."/*.*");
        foreach($files as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);
            if(strpos(strtolower($name), strtolower($search)) !== false) {
                return basename($file);
            } 
        }
        return null;
    }
    function jsNavigate($last,$next) {
        return "
        <script>
        window.onkeyup = e => {
            if(e.keyCode===37 && '".$last."') window.location = '".$last."'
            if(e.keyCode===39 && '".$next."') window.location = '".$next."'	
        }
        </script>
    ";
    }

    function getGame($dir) {
        $content = "<div id='gameContainer' style='width: 800px; height: 600px; margin: auto'></div>";
        $content .= "<script src='".$dir."/UnityLoader.js'></script>";
        $content .= "
        <script>
            var gameInstance = UnityLoader.instantiate('gameContainer', '".$dir."/WebTest20.json');
        </script>
        ";
        return $content;
    }

    function getIframe($url) {
        return "<iframe src='".$url."' frameborder='0' allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share' allowfullscreen></iframe>";
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
            merch_links($dir,$comic,$page_index).
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
            if(!isset($page['no_arrow_nav'])) $content .= jsNavigate($last_link,$next_link);

        }
        
        if(isset($page['iframe'])) $content .= getIframe($page['iframe']);

        if(isset($page['images'])) foreach($page['images'] as $img_num) {
            $fn = find_image($dir.$comic['image_dir'],$img_num);

            $imgs .= "<img src='".$dir.$comic['image_dir']."/".$fn."' alt='".$fn."' />"; 
        }
        $text = '<div class="text">';

        if(isset($page['text'])) foreach($page['text'] as $p) $text .= "<p>".$p."</p>"; 
        
        $text .= $prompt."</div>";
        $content .= $imgs .$text;   
        
        if(isset($page['game'])) {
            $content .= getGame($dir.$page['game']['dir']);
        }
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
            .jsNavigate($last_link,$next_link)
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
