<?php
$file = $_FILES["file"];
if(!is_dir("files/"))
    mkdir("files/", 0777);
if($file && move_uploaded_file($_FILES["file"], "files/".$file))
{
    echo "files/".$file;
}