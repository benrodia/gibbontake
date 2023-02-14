<?php include("head.php") ?>

<main id="home">
    <div id="about">
        <?php 
            foreach($data['info']['description'] as $p) {
                echo "<p>" . $p . "</p>";
            }
        ?>  
    </div>
    <h2 class="choose">choose your adventure</h2>

    <section id="hero">
        <?php 
            foreach($data['comics'] as $comic) {
                $cover = $root."/assets/".$comic['cover'];
                echo "<a 
                    class='comic-head'
                    href=" . $root . "/comics/" . $comic['chapters'][0]['link'] . "  
                >   
                    <img src=" . $cover . " />
                    <div class='pane'>
                        <h3>" . $comic['name'] . "</h3>
                        <b>" . $comic['year'] . "</b>";
                    foreach($comic['description'] as $p) {
                        echo "<p class='desc'>" . $p . "</p>";
                    }
                echo "</div></a>";
            }
        ?>

    </section>
</main>

<?php include('foot.php') ?>