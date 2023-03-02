<?php include("head.php") ?>

<main id="home">
    
    <h2 class="choose">choose your adventure</h2>

    <section id="hero">
        <?php 
            foreach($data['comics'] as $comic) {
                if(!$comic['homepage']) continue;
                $first_issue = isset($comic['chapters']) ? $comic['chapters'][0] : $comic['pages'][0];
                $start = $first_issue ? $first_issue['year'] : $comic['year'];
                $end = $comic['completed'] ? end($comic['chapters'])['year'] : 'Ongoing';
                echo "<a 
                    class='comic-head'
                    style='background-image: url(".$root.$comic['cover'].")'
                    href=" . $root . $comic['page_dir'] . $first_issue['link'] . "  
                >   
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