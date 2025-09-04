<?php
$input = '123456';
$hash = '$2y$10$N9qo8uLOickgx2ZMRZo5i.5SThNGLnCuv3j0j1vKOY2LS9Ywzj1eK';

if (password_verify($input, $hash)) {
    echo 'MATCH';
} else {
    echo 'NO MATCH';
}