<?php
$lines = file('D:\Xampp\htdocs\mtechwebsite\user.metchwebsite\.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue;
    if (strpos($line, '=') !== false) {
        list($key, $value) = explode('=', $line, 2);
        echo $key . ' = ' . trim($value) . PHP_EOL;
    }
}
