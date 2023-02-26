
<img id="bg" src="<?php echo $root."/assets/gib-abstract-bg.jpg" ?>" />

<footer>
    <h4>© <?php echo date("Y") ?> - Gibbontake | All rights reserved </h4>
    <div id="links">
        <?php
            foreach($data['socials'] as $social) {
                echo "<a class='link' target='_' href=" . $social['url'] . ">
                <img class='icon' src=" . $root . "/assets/" . $social['icon'] . " alt=" . $social['name'] . ">
                <p>" . $social['handle'] . "</p>
                </a>";
            }
        ?>
    </div>
</footer>

<script type="text/javascript" src="<?php echo $root."/scripts/main.js" ?>"></script>
<script type="text/javascript" src="<?php echo $root."/scripts/redmanEvents.js" ?>"></script>
<script type="text/javascript" src="<?php echo $root."/scripts/redman.js" ?>"></script>
<script type="text/javascript" src="<?php echo $root."/scripts/gallery.js" ?>"></script>

</body>
</html>