<?php
    $root = "/gibbontake";
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
    <link rel="stylesheet" href="<?php echo $root."/style.css" ?>">
    <link rel="icon" type="image/x-icon" href="<?php echo $root."/assets/icons/gib.ico" ?>">
</head>
<body>
<div id="root_dummy" style="display: none"><?php echo $root ?></div>


<!-- NAVIGATION -->
<header>
<section id="title">
    <a href="<?php echo $root ?>">
        <h1><?php echo $data['info']['title']?></h1>
    </a>   
    <p><?php echo $data['info']['subtitle']?></p>
</section>
<ul id="nav">
    <li class="link"><a href="<?php echo $root."/comics"?>">comics</a></li>
    <li class="link"><a href="<?php echo $root."/art"?>">art</a></li>
    <li class="link"><a href="<?php echo $root."/merch"?>">merch</a></li>
</ul>
</header>
