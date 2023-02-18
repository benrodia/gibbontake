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
                if(!$comic['homepage']) continue;
                $first_issue = isset($comic['chapters']) ? $comic['chapters'][0] : $comic['pages'][0];
                $cover = $root."/assets/".$comic['cover'];
                $start = $first_issue ? $first_issue['year'] : $comic['year'];
                $end = $comic['completed'] ? end($comic['chapters'])['year'] : 'Ongoing';
                echo "<a 
                    class='comic-head'
                    href=" . $root . $comic['page_dir'] . $first_issue['link'] . "  
                >   
                    <img src=" . $cover . " />
                    <div class='pane'>
                        <h3>" . $comic['name'] . "</h3>
                        <b>" . $start . ' - ' . $end . "</b>";
                    foreach($comic['description'] as $p) {
                        echo "<p class='desc'>" . $p . "</p>";
                    }
                echo "</div></a>";
            }
        ?>

    </section>
</main>

<?php include('foot.php') ?>