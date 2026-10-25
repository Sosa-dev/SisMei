<?php
if ($_SERVER['HTTP_HOST'] == 'localhost') {
    define('URL_BASE', '/SisMei/');
} else {
    define('URL_BASE', '/');
}