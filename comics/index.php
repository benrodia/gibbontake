<?php include('../head.php') ?>

<main id="comics">
    <?php
        foreach($data['comics'] as $comic) {
            echo "<section class='comic'>
            <h3>" . $comic['name'] . " - <b>" . $comic['year'] . "</b></h3>";
            foreach($comic['description'] as $p) {
                echo "<p class='desc'>" . $p . "</p>";
            }
            echo "<div class='chapters'>";

            foreach($comic['chapters'] as $chapter) {
                echo "<a href=" . $chapter['link'] . "><button>" . $chapter['name'] . "</button></a>";
            }
            echo "</div></section>";
        }
    ?>
</main>

<?php include('../foot.php') ?>