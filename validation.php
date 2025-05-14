<?php
$data = file_get_contents('php://input');
file_put_contents('validation_log.txt', $data, FILE_APPEND);
echo json_encode(["ResultCode" => 0, "ResultDesc" => "Accepted"]);
?>
