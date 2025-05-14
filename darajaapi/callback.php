<?php
$data = file_get_contents('php://input');
file_put_contents('callback_log.txt', $data, FILE_APPEND);
echo "OK";
?>
