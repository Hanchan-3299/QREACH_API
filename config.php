<?php

define("DATABASE_HOST", "localhost");
define("DATABASE_USER", "root");
define("DATABASE_PASSWORD", "hanchanp155");
define("DATABASE_NAME", "androidapp");

$connection = mysqli_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);

if(mysqli_connect_error()){
    sendJsonResponse("Error", "Cannot Connect to Database MYSQL = " . mysqli_connect_error());
}