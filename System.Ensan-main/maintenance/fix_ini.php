<?php
$file = 'C:\\MAMP\\bin\\php\\php8.3.1\\php.ini';
$content = file_get_contents($file);
$content = str_replace("9e x t e n s i o n = p h p _ f i l e i n f o . d l l  \r\n \r\n", "9\r\nextension=fileinfo\r\n", $content);
$content = preg_replace('/e x t e n s i o n = p h p _ f i l e i n f o . d l l.*$/s', "extension=fileinfo", $content);
file_put_contents($file, $content);
echo "Done";
