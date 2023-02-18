<?php


function get_data($dir = __DIR__) {
    $json = file_get_contents($dir.'/data.json');
    return json_decode($json, true);    
}


function create_page($path, $name, $ind) {
    $dir =  __DIR__ . $path;

    echo file_exists($dir) ? 'true' : 'false';
    
    if (!file_exists($dir)) mkdir($dir, 0777, true);
    
    chmod($dir, 0777);

    $newfile = fopen( $dir . '/index.php', 'w');
    
    $contents = "<?php \$path = \"../../../\" ?>
    <?php include(\$path.'head.php') ?>
    <?php include(\$path.'reader.php') ?>
    
    <?php echo reader(\$path,'".$name."',".$ind.") ?>
    
    <?php include(\$path.'foot.php') ?>
    ";

    fwrite($newfile, $contents);
    fclose($newfile);
    return $newfile;
}


$data = get_data();

foreach ($data['comics'] as $comic) {
    if(isset($comic['chapters'])) {
        foreach($comic['chapters'] as $ind=>$page) {
            print_r(create_page($comic['page_dir'].$page['link'],$comic['name'],$ind));
        }
    } elseif(isset($comic['pages'])) {
        foreach($comic['pages'] as $ind=>$page) {
            print_r(create_page($comic['page_dir'].$page['link'],$comic['name'],$ind));
        }   
    }
}

?>