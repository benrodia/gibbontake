<?php
    $root = '/gibbontake';
    function get_data($dir = __DIR__) {
        $json = file_get_contents($dir.'/data.json');
        return json_decode($json, true);    
    }
    $data = get_data()
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['info']['title']?></title>
    <link rel="stylesheet" href="<?php echo $root."/styles/basic.css" ?>">
    <link rel="stylesheet" href="<?php echo $root."/styles/elements.css" ?>">
    <link rel="stylesheet" href="<?php echo $root."/styles/layout.css" ?>">
    <link rel="icon" type="image/x-icon" href="<?php echo $root."/assets/icons/gib.ico" ?>">
</head>
<body>
<div id="root_dummy" style="display: none"><?php echo $root ?></div>


<!-- NAVIGATION -->
<header id='header'>
    <section id="title">
        <a href="<?php echo $root ?>">
            <h1><?php echo $data['info']['title']?></h1>
        </a>   
    </section>
    <ul class="nav">
        <?php 
            foreach($data['info']['pages'] as $page) {
                $is_active = strpos($_SERVER['REQUEST_URI'],$root.$page['link'])!==false;
                echo "<li class='link".($is_active?" active":'')."'>
                    <a href='".$root.$page['link']."'>".$page['name']."</a>
                    </li>";
            }
        ?>

    </ul>
</header>
