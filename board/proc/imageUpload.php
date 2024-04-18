<?php
if ($_FILES['file']['name']) {
    if (!$_FILES['file']['error']) {
        $name = md5(rand(100, 200));
        $ext = explode('.', $_FILES['file']['name']);
        $filename = $name . '.' . $ext[1];
        $destination = '../../upload/' . $filename; 

        $location = $_FILES["file"]["tmp_name"];
        move_uploaded_file($location, $destination);
        echo '/'.$destination;
    } else {
        echo $message = '오류 발생:  '.$_FILES['file']['error'];
    }
}
?>
