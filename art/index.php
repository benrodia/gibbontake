
<?php include('../head.php') ?>

<main id="art">
    <section>
        <div class="year">2023</div>
        <div class="cont">
        <?php
            foreach ($data['art'] as $pic) {
                echo "<img 
                    src=".$root.'/assets/'.$pic['filename']." 
                    alt=".$pic['name']." 
                />";
            }
        ?>

        </div>
    </section>
</main>

<?php include('../foot.php') ?>
<!-- // echo "<img src=$picture['filename'] alt=$picture['name'] />"; -->
