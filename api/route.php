<?php
    include './controllers/LoginController.php';

    error_reporting(E_ERROR | E_PARSE);

    try {
        //code...
        $LoginController = new LoginController;

    } catch (\Throwable $th) {
        //throw $th;
    }
?>