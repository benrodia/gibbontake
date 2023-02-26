

<?php 
    function art_nav($folders,$cur = null) {
        $nav = "<div class='art-nav'>";
        foreach($folders as $ind => $folder) {
            $header = basename($folder);
            $disable = $cur === $header ? "disabled" : "";
            $nav .= "<a class='nav-item ".$disable."' href='../".$header."'>
                <button>".$header."</button>
            </a>";
        }
        $nav .= "</div>";
        return $nav;
    }

    function gallery($dir, $page) {
        $data = json_decode(file_get_contents($dir.'/data.json'), true);
        $path = $dir.$data['art']['image_dir'];

        $folders = array_reverse(array_filter(glob($path.'/*'), 'is_dir'));
        
        $content = "<main id='art'>";

        $content .= art_nav($folders,$page);
        foreach($folders as $folder) {
            $header = basename($folder);
            if($header != $page) continue;
            $content .= "<section id=".$header."><div class='year'>".$header."</div><div class='cont'>";
            $images = glob($path.'/'.$header.'/*');
            natsort($images);
            foreach ($images as $image) {
                $nsfw = strpos(strtolower(basename($image)),'nsfw') !== false;

                $content .= $nsfw 
                    ?  "<span class='img-cont'>
                            <span class='nsfw'>NSFW</span>
                            <img src='".$image."' class='gallery-img'/>
                        </span>"
                    : "<img src='".$image."' class='gallery-img'/>";
            }
            $content .= "</div></section>";
        }
        $content .= art_nav($folders,$page);
        $content .= "<div id='lightbox-cont' class='modal hide'>
        <div id='lightbox-bg' class='bg'></div>
        <img id='lightbox-img' />
        </div>";
        $content .= "</main>";
        return $content;
    }
        

    ?>

</main>

<!-- // echo "<img src=$picture['filename'] alt=$picture['name'] />"; -->
