<?php

function sendJsonResponse($status, $message, $data = []){
    header("Content-Type: application/json");
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}

if(isset($_SESSION['account_logged_in']) && $_SESSION['account_logged_in'] === true){
    session_unset();
    session_destroy();
    sendJsonResponse("success", "Logout Success");
}else {
    sendJsonResponse("error", "User not logged in");
}