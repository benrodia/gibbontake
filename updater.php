<?php
include('utils.php');
include('reader.php');

$data = get_data();

function create_page($path, $contents) {
    $dir =  __DIR__ . $path;
    $filename = $dir . '/index.php';
    $exists = file_exists($filename);
    if(!$exists) mkdir($dir, 0777, true);
    $equal = $exists && $contents === file_get_contents($filename);
    
    chmod($dir, 0777);

    $newfile = fopen($filename, 'w');

    fwrite($newfile, $contents);
    fclose($newfile);
    echo "<div style='color:".($exists?$equal?'gray':'orange':'green').";'>".
    ($exists?$equal?'Unchanged':'Updated':'Created')." file: \"".$filename."\"</div>";
    return $newfile;
}



function ass_reader($comic,$page,$ind) {
    $contents = "<?php 
        \$path = \"../../../\";
        include(\$path.'head.php');
        include(\$path.'reader.php'); 
        echo reader(\$path,'".$comic['name']."',".$ind.");
        include(\$path.'foot.php');
    ?>";
    create_page($comic['page_dir'].$page['link'],$contents);
}


echo "<a href='index.php'><button>Back home</button></a>";

foreach ($data['comics'] as $comic) {
    if(isset($comic['chapters'])) foreach($comic['chapters'] as $ind=>$page) ass_reader($comic,$page,$ind);
    elseif(isset($comic['pages'])) foreach($comic['pages'] as $ind=>$page) ass_reader($comic,$page,$ind);    
    elseif($comic['format']=='lazy') {
        $pages = glob(__DIR__.$comic['image_dir'].'/*');
        natsort($pages);
        foreach($pages as $ind => $img_path) {
            $contents = "<?php 
                \$path = \"../../../\";
                include(\$path.'head.php');
                include(\$path.'reader.php'); 
                echo reader(\$path,'".$comic['name']."',".$ind.");
                include(\$path.'foot.php');
            ?>";
            create_page($comic['page_dir'].'/'.clean(pathinfo($img_path)['filename']),$contents);
        }
    }
}

$art_folders = array_reverse(array_filter(glob(__DIR__.$data['art']['image_dir'].'/*'), 'is_dir'));



foreach($art_folders as $ind => $folder) {
    $header = basename($folder);
    $contents = "
    <?php 
    \$path = \"../../\";
    include(\$path.'head.php');
    include(\$path.'gallery.php');
    echo gallery(\$path,'".$header."');
    include(\$path.'foot.php');
    ?>";
    create_page($data['art']['page_dir'].'/'.$header, $contents);
}
echo "<a href='index.php'><button>Back home</button></a>";
?>