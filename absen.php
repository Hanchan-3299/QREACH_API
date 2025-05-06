<?php

header("Content-Type: application/json");
session_start();
require_once("config.php");

$dataRaw = file_get_contents("php://input");
$dt = json_decode($dataRaw, true);

function sendJsonResponse($status, $message, $data = []) {
    echo json_encode([
        "status" => $status,
        "message" => $message,
        "data" => $data
    ]);
    exit();
}

if(!$connection) {
    sendJsonResponse("error", "Database connection failed");
}

//debug
// file_put_contents("dbg_absen.txt", print_r($dt, true));

function absen ($conn, $id, $email, $qrData, $status){
    $statusInt = $status ? 1 : 0;

    $query = "INSERT INTO absen (id_user, email_user, qr_data, absensi)
                VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $id, $email, $qrData, $statusInt);

    if($stmt->execute()){
        sendJsonResponse("success", "Absen Berhasil", $statusInt);
    }else {
        sendJsonResponse("error", "Absen Gagal");
    }
}

if($_SERVER['REQUEST_METHOD'] === "POST") {
    $required = ['id', 'email', 'qrdata', 'status'];

    foreach($required as $key) {
        if(!array_key_exists($key, $dt)){
            sendJsonResponse("error", "missing array key " . $key);
        }
    }
    absen($connection, $dt['id'], $dt['email'], $dt['qrdata'], $dt['status']);
}else {
    sendJsonResponse("error", "Invalid request method");
}
