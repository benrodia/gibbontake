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
            <h3>" . $comic['name'] . "</h3>
            <div class='inner'>
            <img src='../".$comic['cover']."' alt=".$comic['cover']."/>
            <div class='info'>
            <h4>" . $published . "</h4>";
            foreach($comic['description'] as $p) {
                echo "<p class='desc'>" . $p . "</p>";
            }
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
            echo "</div>";
            
            if(isset($comic['merch'])) {
                echo "<div class='merch links'>";
                foreach($comic['merch'] as $merch) {
                    echo "<a href='".$merch['url']."' target='_'>
                        <img src='".$root.$merch['icon']."' class='icon'/>"
                        .$merch['text'].
                    "
                    </a>";
                }
                echo "</div>";
            }
            echo "</div></div></section>";
        }
    ?>
</main>

<?php include('../foot.php') ?>