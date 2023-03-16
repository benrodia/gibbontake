<?php
    include('utils.php');
    include('navigation.php');

    $root = isLocalhost() ? '/gibbontake' : '';
    $data = get_data();
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

<!-- Site Header (navigation.js) -->
<?php echo siteNav($root,$data) ?>

