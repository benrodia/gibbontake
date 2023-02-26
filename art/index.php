
<?php include('../head.php') ?>

<?php 

        $path = '../'.$data['art']['image_dir'];

        $folders = array_reverse(array_filter(glob($path.'/*'), 'is_dir'));
        header('Location: '.basename($folders[0]));

    ?>


<?php include('../foot.php') ?>
