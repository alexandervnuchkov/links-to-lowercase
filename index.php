<?php
    include('simple_html_dom.php');
    ini_set('max_execution_time', 0);
    error_reporting(E_ERROR);
    ini_set("display_errors", 1);
    set_time_limit(0);

?>
<html>
<head>
    <title>Convert URLs to lowercase</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:900,800,700,600,500,400,300&amp;subset=latin,cyrillic-ext,cyrillic,latin-ext" rel="stylesheet" type="text/css" />
    <link href="css/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <p id="back-top" style="display: none">
        <a title="Scroll up" href="#top"></a>
    </p>
    <h1>Convert URLs to lowercase</h1>
<?php

    $src_file_list1 = [];
    $src_file_list_xml = [];
    $xxcounter = 0;

    $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('files/src'), RecursiveIteratorIterator::SELF_FIRST);
    foreach($objects as $name => $object){
        if(is_dir($name)==1){
            $dest_name = str_replace('src', 'out', $name);
            mkdir($dest_name);
        }
        if(pathinfo($name, PATHINFO_EXTENSION) == 'aspx' || pathinfo($name, PATHINFO_EXTENSION) == 'ascx' || pathinfo($name, PATHINFO_EXTENSION) == 'master' || pathinfo($name, PATHINFO_EXTENSION) == 'js' || pathinfo($name, PATHINFO_EXTENSION) == 'htm' || pathinfo($name, PATHINFO_EXTENSION) == 'html'){
            $name = str_replace('\\', '/', $name);
            array_push($src_file_list1, $name);
        } else if(pathinfo($name, PATHINFO_EXTENSION) == 'xml'){
            $name = str_replace('\\', '/', $name);
            array_push($src_file_list_xml, $name);
        }
    }

    foreach($src_file_list1 as $file_path){
        $html = file_get_html($file_path);
        $source = file_get_contents($file_path);
        foreach($html->find('img') as $element){
            $image_src = $element->src;
            $source = str_replace($image_src, strtolower($image_src), $source);
        }
        foreach($html->find('a') as $element){
            $URL_href = $element->href;
            if (strpos($URL_href, '.exe') !== false || $URL_href == '') {
            } else {
                $source = str_replace($URL_href, strtolower($URL_href), $source);            
                $xxcounter += 1;
            }
        }
        foreach($html->find('a') as $element){
            $URL_id = $element->id;
            if ($URL_id !== '') {
                $source = str_replace($URL_id, strtolower($URL_id), $source);
                $xxcounter += 1;
            }
        }
        file_put_contents(str_replace('src', 'out', $file_path), $source);
    }
    function prettyPrint($array) {
	    echo '<pre>'.print_r($array, true).'</pre>';
	}

    $array_list = [];
    foreach($src_file_list_xml as $file_path){
        $source = file_get_contents($file_path);
        $p = xml_parser_create();
        xml_parse_into_struct($p, $source, $vals, $index);
        foreach($vals as $tag=>$value){
            if(is_array($value)){
                for($i = 0; $i <= count($value); ++$i){
                    if($value['tag'] == 'PATH'){
                        $array_list[] = $value['value'];
                    }
                }
            }
        }
        foreach($array_list as $link){
            $source = str_replace($link, strtolower($link), $source);
            $xxcounter += 1;
        }
        
        file_put_contents(str_replace('src', 'out', $file_path), $source);
    }
    echo $xxcounter . ' replacements have been made';
?>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="js/arrowup.min.js"></script>
</body>
</html>