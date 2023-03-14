<?php include('../head.php') ?>
<?php include('../reader.php') ?>

<main id="comics">
<?php

foreach($data['comics'] as $comic) {
    $published = '-';
    if(isset($comic['chapters'])) {
        $start = $comic['chapters'][0]['year'];
        $end = $comic['completed'] ? end($comic['chapters'])['year'] : 'Ongoing';
        $published = $start . ' - ' . $end;
    } elseif(isset($comic['published'])) $published = $comic['published'];

    echo "<section class='comic'>
    <h3>" . $comic['name'] . " (" . $published . ")</h3>
    <div class='inner'>
    <img src='../".$comic['cover']."' alt=".$comic['cover']."/>
    <div class='info'>";
    
    echo "<div class='text'>";
    foreach($comic['description'] as $p) echo "<p class='desc'>" . $p . "</p>";
    echo "</div>";
    
    echo "<div class='chapters'>";
    
    if(isset($comic['chapters'])) {
        foreach($comic['chapters'] as $chapter) {
            echo "<a href=" . $root . $comic['page_dir'] . $chapter['link'] . ">
            <button>" . $chapter['name'] . "</button>
            </a>";
        }
    }
    elseif(isset($comic['pages'])) {
        echo "<a href='" . $root . $comic['page_dir'] . $comic['pages'][0]['link'] . "'>
        <button>Begin</button>
        </a>";
        echo pageNav($comic,false,'..');
    }
    else {
        $folder = glob('../'.$comic['page_dir'].'/*')[0];
        echo "<a href='" . $folder . "'>
        <button>Read</button>
        </a>";
        echo pageNavLazy($comic,false,'..');
    }
    echo "</div></div></div>";
    echo merch_links($root,$comic,null);
    echo "</section>";
}

?>

</main>

<?php include('../foot.php') ?>