<?php include('../head.php') ?>

<main id="about">
    <div id="about-text">
        <img class='headshot' src="<?php echo $root.$data['info']['headshot'] ?>" />
        <div>
        <?php 
            foreach($data['info']['description'] as $p) {
                echo "<p>" . $p . "</p>";
            }
        ?>  
        </div>
    </div>
    <div class="socials">
        <?php
            foreach($data['info']['socials'] as $social) {
                echo "<a class='link' target='_' href=" . $social['url'] . ">
                <img class='icon' src=" . $root . $social['icon'] . " alt=" . $social['name'] . ">
                <p>" . $social['handle'] . "</p>
                </a>";
            }
        ?>
    </div>
</main>

<?php include('../foot.php') ?>