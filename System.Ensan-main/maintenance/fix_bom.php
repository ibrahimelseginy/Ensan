<?php
$f = 'f:/Projects/Ensan/System.Ensan-main/app/Http/Controllers/ProjectWebController.php';
$c = file_get_contents($f);
if (substr($c, 0, 3) == "\xef\xbb\xbf") {
    file_put_contents($f, substr($c, 3));
    echo "BOM removed";
}
else {
    echo "No BOM found";
}
