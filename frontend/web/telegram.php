<?php
    $data = file_get_contents("php://input");
    file_put_contents("test.dump", $data, FILE_APPEND);
?>