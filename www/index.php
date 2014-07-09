<?php

require_once __DIR__ . '/../php-yaoi/modules/Yaoi.php';
Yaoi::init();

$key = explode('/', substr($_SERVER['REQUEST_URI'], 1), 2);

echo '<pre>';
//print_r($_SERVER);
print_r($key);


if (empty($key[0]) || empty($key[1])) {
    ?>
<pre>
Usage:
    GET /[db]/[collection]/[key]    to fetch string
    POST /[db]/[collection]/[key]   to store string
    form data:
        <i>value</i>,
        <i>ttl</i>, expiration time in seconds from now, default unlimited,
        <i>content-type</i>, default 'text/html',
</pre>
<?php
    exit();
}

try {
    $storage = Storage::create('mongo://localhost/' . $key[0] . '/cache');

    if (empty($_POST['value'])) {
        $value = $storage->get($key[1]);
        if ($value === null) {
            header("Status: 404 Not Found");
            //header("HTTP/1.0 404 Not Found");
        }
        else {
            echo $value;
        }
    }
    else {
        $storage->set($key, $_POST['value'], empty($_POST['ttl']) ? null : $_POST['ttl']);
    }
}
catch (Storage_Exception $e) {
    echo $e->getMessage();
}
