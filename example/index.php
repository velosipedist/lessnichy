<?php
require_once __DIR__ . "/../lib/Lessnichy.php";

Lessnichy::add([
   '/less/foo.less'
]);

?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title></title>
    <?
    Lessnichy::head([
        'less'=>'http://www.lesscss.org/js/less.js'
    ]);
    ?>
</head>
<body>
content
</body>
</html>