
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

    function reader($dir, $comic_name, $page_index) {
        $data = json_decode(file_get_contents($dir.'/data.json'), true);

        $comic = find_object_by_key($data['comics'], 'name', $comic_name);

        if(!$comic) return "<main id='reader'>We're having trouble finding that comic</main>";

        $content = '';


        if($comic['format'] == 'simple') {
            $chapter = $comic['chapters'][$page_index];

            $imgs = '';
            for ($i=0; $i < $chapter['length']; $i++) { 
                $ind = $chapter['start'] + $i;
                $fn = find_image($dir.$comic['image_dir'],$ind);
                $imgs .= "<img src='".$dir.$comic['image_dir']."/".$fn."' alt='Page ".$ind."' />"; 
            }
             
             $content .= "<main id='reader' class='simple'>" . 
                chapterNav($comic, $page_index) .
                $imgs .
                chapterNav($comic, $page_index) .
                "</main>"
                ;    
        }
        if($comic['format'] == 'cyoa') {
            $page = $comic['pages'][$page_index];
            $imgs = ''; $prompt = '';

            if(isset($page['prompts'])) foreach($page['prompts'] as $pr) {
                $prompt .= "<a href='".$dir.$comic['page_dir'].$pr['link']."' class='prompt'>" . 
                    $pr['text'] .
                "</a>";
            }
            else {
                $next_index = $page_index + 1;
                $has_next = count($comic['pages']) > $next_index;
                if($has_next) {
                    $next_page = $comic['pages'][$next_index];
                    $prompt = "<a href='".$dir.$comic['page_dir'].$next_page['link']."' class='prompt'>" . 
                        $next_page['title'] .
                    "</a>";
                }
            }

            foreach($page['images'] as $img_num) {
                $fn = find_image($dir.$comic['image_dir'],$img_num);

                $imgs .= "<img src='".$dir.$comic['image_dir']."/".$fn."' alt='".$fn."' />"; 
            }
            $text = '<div class="text">';
            if(isset($page['text'])) foreach($page['text'] as $p) $text .= "<p>".$p."</p>"; 
            
            $text .= $prompt."</div>";

            $content .= 
            "<main id='reader' class='cyoa'>
            <div class='inner'>
                <h3 class='title'>".$page['title']."</h3>" .
                $imgs .
                $text . 
            "</div></main>"
            ;   
            //<h2 class='nav'>".$comic['name']." (".explode('/',$page['link'])[1].")".pageNav($comic,$page_index)."</h2>
        }
        if($comic['format'] == 'lazy') {
            $folder = glob(__DIR__.$comic['image_dir'].'/*');
            $prompt = '';
            $title = basename($folder[$page_index]);
            $img = "<img src='".$dir.$comic['image_dir'].'/'.$title."' alt='".$folder[$page_index]."' />";
            
            $last_index = $page_index - 1;
            $has_last = $last_index>=0;
            $last_link = $has_last ? clean(pathinfo($folder[$last_index])['filename']):'';            
            $prompt .= "<a href='".$dir.$comic['page_dir']."/".$last_link."' class='".($has_last?'':'disabled')."'>Last</a>";

            $next_index = $page_index + 1;
            $has_next = count($folder) > $next_index;
            $next_link = $has_next ? clean(pathinfo($folder[$next_index])['filename']):'';
            $prompt .= "<a href='".$dir.$comic['page_dir']."/".$next_link."' class='".($has_next?'':'disabled')."'>Next</a>";


            $content .= 
            "<main id='reader' class='cyoa'>
            <div class='inner'>
                <h3 class='title'>".$title."</h3>" .
                $img .
                "<div class='lazy-nav'>".$prompt."</div>". 
            "</div></main>"
            ;  
        }

        return $content;
        
    }
    # BATCH RENAME FOLDER IN POWERSHELL VVV
    # $i=1; Get-ChildItem . | %{Rename-Item $_ -NewName ('clarissa_{0:D4}{1}' -f $i++, $_.extension)}

?>