<?php

require_once __DIR__ . '/../php-yaoi/modules/Yaoi.php';
Yaoi::init();

$key = explode('/', substr($_SERVER['REQUEST_URI'], 1), 2);

//echo '<pre>';
//print_r($_SERVER);
//print_r($key);


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

<form action="/" method="post">
    Key <input name="url" onchange="this.form.action = this.value" />
    <textarea name="value" style="width:100%;height: 100px"></textarea>
    <button type="submit">save</button>
</form>
<?php
    exit();
}

try {
    $storage = Storage::create('mongo://localhost/' . $key[0] . '/cache?compression=1');

    if (!isset($_POST['cmd'])) {
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
        if ('set' === $_POST['cmd']) {
            $storage->set($key[1], $_POST['value']);
        }
        elseif ('deleteAll' === $_POST['cmd']) {
            $storage->deleteAll();
        }
        elseif ('delete' === $_POST['cmd']) {
            $storage->delete($key[1]);
        }
    }
}
catch (Storage_Exception $e) {
    echo $e->getMessage();
}
