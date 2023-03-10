<?php include("head.php") ?>

<main id="home">
    
    <h2 class="choose">choose your adventure</h2>

    <section id="hero">
        <?php 
            function get_publish_dates($comic,$return_first_issue=false) {
                $denom = isset($comic['chapters']) ? $comic['chapters'] : (isset($comic['pages']) ? $comic['pages'] : null);
                if($return_first_issue) return isset($denom) ? $denom[0]['link'] : null;
                $start = isset($denom) && isset($denom[0]['year']) ? $denom[0]['year'] : (isset($comic['published']) ? $comic['published'] : null);
                if(!$start) return null;

                $end = $comic['completed'] ? isset($denom) && isset(end($denom)['year']) ? ' - '.end($denom)['year'] : '' : ' - Ongoing';

                return $start . $end;
            }

            foreach($data['comics'] as $comic) {
                if(!$comic['homepage']) continue;
                $link = $root . $comic['page_dir'] . get_publish_dates($comic,true);
                echo "<div class='comic-cont'>";
                echo "<a 
                    class='comic-head'
                    style='background-image: url(".$root.$comic['cover'].")'
                    href=" . $link . "  
                >   
                    <div class='pane'>
                        <h3>" . $comic['name'] . "</h3>
                        <b>" . get_publish_dates($comic) . "</b>";
                    foreach($comic['description'] as $p) {
                        echo "<p class='desc'>" . $p . "</p>";
                    }
                echo "</div></a>";


                echo "<div class='under'><a href='".$link."' class='' ><button>Start Reading</button></a>";

                if(isset($comic['merch'])) {
                    echo "<div class='merch links'>";

                    foreach($comic['merch'] as $merch) {
                        echo "<a href='".$merch['url']."' target='_'>
                            <img src='".$root.$merch['icon']."' class='icon'/>"
                            .$merch['text'].
                        "</a>";
                    }
                    echo "</div>";
                }
                echo "</div></div>";

            }
        ?>

    </section>
</main>

<?php include('foot.php') ?>