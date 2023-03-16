<?php
    function clean($string) {
        return strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $string)));
    }

    function find_object_by_key($array, $key, $val){
        foreach ( $array as $element ) {
            if ( $val === $element[$key] ) {
                return $element;
            }
        }
        return false;
    }
    function find_image($dir,$search) {
        $files = glob($dir."/*.*");
        foreach($files as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);
            if(strpos(strtolower($name), strtolower($search)) !== false) {
                return basename($file);
            } 
        }
        return null;
    }

    function get_data($dir = __DIR__) {
        $json = file_get_contents($dir.'/data.json');
        return json_decode($json, true);    
    }
    function isLocalhost($whitelist = ['127.0.0.1', '::1']) {
        return in_array($_SERVER['REMOTE_ADDR'], $whitelist);
    }

    function arrowkey_nav($last_url,$next_url) {
        return "
            <script>
            window.onkeyup = e => {
                if(e.keyCode===37 && '".$last_url."') window.location = '".$last_url."'
                if(e.keyCode===39 && '".$next_url."') window.location = '".$next_url."'	
            }
            </script>
        ";
    }
?>