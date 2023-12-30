<?php

function getDbArr($db) {
    $arr = [];

    $DBC = mysqli_connect(DBHOST, DBUSER, DBPW, DBNAME);
    
    if (mysqli_connect_errno()) {
        echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
        exit; //stop processing the page further
    };

    $query = "SELECT * FROM " . $db . ";";
    $result = mysqli_query($DBC,$query);

    mysqli_close($DBC);

    while ($row = mysqli_fetch_assoc($result)) {
        array_push($arr, $row);
    }

    return $arr;
}

function assemble_query($DBC, $columns, $source, $conditions="") {
    $query = "SELECT " . $columns . " " .$source . " " . $conditions;
    return mysqli_query($DBC, $query);
}

function cleanStr($str) {
    $str = trim($str);
    $str = stripslashes($str);
    $str = htmlspecialchars($str);

    return $str;
}

function bb_ify($str) {

    $str = trim($str);

    $str = nl2br($str);
    $str = str_replace("<b>", "[b]", $str);
    $str = str_replace("</b>", "[/b]", $str);
    $str = str_replace("<strong>", "[b]", $str);
    $str = str_replace("</strong>", "[/b]", $str);
    $str = str_replace("<i>", "[i]", $str);
    $str = str_replace("</i>", "[/i]", $str);
    $str = str_replace("<em>", "[i]", $str);
    $str = str_replace("</em>", "[/i]", $str);
    $str = str_replace("<s>", "[s]", $str);
    $str = str_replace("</s>", "[/s]", $str);
    $str = str_replace("<u>", "[u]", $str);
    $str = str_replace("</u>", "[/u]", $str);
    $str = str_replace('<br />', '[nl]', $str);

    while (strpos($str, '<img') !== false) {
        $img_pos = strpos($str, '<img');
        $src_pos = strpos($str, 'src="', $img_pos);
        $src_pos_end = strpos($str, '"', $src_pos + 5);
        if ($src_pos && $src_pos_end) {
            $src = substr($str, $src_pos+5, $src_pos_end - ($src_pos + 5));
        } else {
            $src = '';
        }
        $end_pos = strpos($str, ">", $img_pos);
        if ($end_pos !== false) {
            $bb_code = '[img=' . $src . ']';
            $str = substr_replace($str, $bb_code, $img_pos, ($end_pos + 1 - $img_pos));
        } else {
            $str = substr_replace($str, '[There was supposed to be an image here but the code was incorrect]', $img_pos, 4);
        }
        
    }

    while (strpos($str, '<a href=') !== false) {
        $link_pos = strpos($str, '<a href=');
        $href_pos = strpos($str, 'href="', $link_pos);
        
        if ($href_pos !== false) {
            $href_pos_end = strpos($str, '"', $href_pos + 6);
            $opening_end_pos = strpos($str, '>', $href_pos);
            $end_pos = strpos($str, '</a>', $link_pos);
            if ($href_pos_end !== false && $opening_end_pos !== false && $end_pos !== false) {
                $link = substr($str, $href_pos + 6, $href_pos_end - ($href_pos + 6));
                $bb_code = '[url=' . $link . ']';
                $str = substr_replace($str, $bb_code, $link_pos, ($opening_end_pos + 1 - $link_pos));
                $str = substr_replace($str, '[/url]', $end_pos-1, 4);
            } else {
                substr_replace($str, '--Link error--', $link_pos, 8);
            }
        } else {
            substr_replace($str, '--Link error--', $link_pos, 8);
        }
    }
    
    $str = str_replace('[[nl]', '][nl]', $str);
    $str = strip_tags($str);
    
    return $str;
}

function de_bb_ify($str) {

    $str = str_replace("[b]", "<strong>", $str);
    $str = str_replace("[/b]", "</strong>", $str);
    $str = str_replace("[i]", "<em>", $str);
    $str = str_replace("[/i]", "</em>", $str);
    $str = str_replace("[s]","<s>", $str);
    $str = str_replace("[/s]", "</s>", $str);
    $str = str_replace("[u]", "<u>", $str);
    $str = str_replace("[/u]", "</u>", $str);
    $str = str_replace('[nl]', '<br>', $str);
    
    while (strpos($str, '[img=') !== false) {
        $img_pos = strpos($str, '[img=');
        $end_pos = strpos($str, ']', $img_pos+1);
        if ($end_pos !== false) {
            $src = substr($str, $img_pos+5, ($end_pos - ($img_pos + 5)));
            $str = substr_replace($str, '<img src="' . $src . '">', $img_pos, ($end_pos + 1 - $img_pos));
        }
        
    }

    while (strpos($str, '[url=') !== false) {
        $link_pos = strpos($str, '[url=');
        $end_pos = strpos($str, ']', $link_pos);
        if ($end_pos !== false) {
            $closing_pos = strpos($str, '[/url]', $end_pos);
            if ($closing_pos !== false) {
                $link = substr($str, $link_pos+5, ($end_pos - ($link_pos + 5)));
                $link_text = substr($str, $end_pos+1, $closing_pos-($end_pos+1));
                // echo "<script>alert('$link_text');</script>";
                $str = substr_replace($str, '<a href="' . $link . '">' . $link_text . '</a>', $link_pos, ($closing_pos + 6 - $link_pos));
                // $str = substr_replace($str, '</a>', $closing_pos, 6);
            } else {
                $str = substr_replace($str, '--Link error--', $link_pos, 5);
            }
        } else {
            $str = substr_replace($str, '--Link error--', $link_pos, 5);
        }
    }

    return $str;

}

// Get categories

include "php-functionality/categories.php";
$DBC = mysqli_connect(DBHOST, DBUSER, DBPW, DBNAME);

$category_arr = [];

if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
    exit; //stop processing the page further
};

$query = "SELECT * FROM  categories;";
$result = mysqli_query($DBC,$query);

mysqli_close($DBC);

while ($row = mysqli_fetch_assoc($result)) {
    $category = new Category($row['category_is'], $row['category_name'], $row['parent_category']);
    $category_arr[$category->id] = $category;
}

foreach ($category_arr as $cat) {
    if ($cat->parent <> null) {
        array_push($category_arr[$cat->children], $cat);
    }
}
?>