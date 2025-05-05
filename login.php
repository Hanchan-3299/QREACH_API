<?php
//1. dipindahkan, awalnya didalam sendJsonResponse diatas echo json encode
header("Content-Type: application/json");
session_start();
require_once("config.php");

//2. kita ambil input an dari frontend berupa json, dan kita decode jadi array assoc
$rawData = file_get_contents("php://input");
$dt = json_decode($rawData, true);

function sendJsonResponse($status, $message, $data = []){
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}

function loginUser($connection, $email, $password){
    if(empty($email) || empty($password)){
        sendJsonResponse("Error", "Please fill email and password");
    }

    $query = "SELECT id, password from user WHERE email = ?";
    if($statement = $connection->prepare($query)){
        $statement->bind_param('s', $email);
        $statement->execute();
        $statement->store_result();

        $id = null;
        $db_password =  null;
        if($statement->num_rows > 0){
            $statement->bind_result($id, $db_password);
            $statement->fetch();

            if($password === $db_password){
                session_regenerate_id();
                $_SESSION['account_logged_in'] = true;
                $_SESSION['account_email'] = $email;
                $_SESSION['account_id'] = $id;
                $statement->close();
                sendJsonResponse("success", "Logged in Succesfully", [
                    'id' => $id,
                    'email' => $email
                ]);
            }
            $statement->close();
            sendJsonResponse("error", "invalid password");
        }
        $statement->close();
        sendJsonResponse("error", "invalid email");
    }
    sendJsonResponse("error", "incorrect query");
}


function checkLogin(){
    if(isset($_SESSION['account_logged_in']) && $_SESSION['account_logged_in'] === true){
        sendJsonResponse("success", "User has successfully logged in", [
            "status" => true,
            "email" => $_SESSION['account_email'],
            "id" => $_SESSION['account_id']
        ]);
    }
    sendJsonResponse("error", "user failed to login", [
        "status" => false
    ]);
}

if($_SERVER['REQUEST_METHOD'] === "POST"){
    //3. buat baru dengan dt, yang lama komentarin aja
    if(!isset($dt['email'], $dt['password']) || $dt['email'] === "" || $dt['password'] === ""){
        //4. ini aslinya dari userLogin bagian atas, skrng dipindah agar di cek dan tampilkan disini
        sendJsonResponse("Error", "Username or Password is empty");
    }
    //5. kita ambil dari yang lama dibawah, lalu ubah agar menggunakan dt
    loginUser($connection, $dt['email'], $dt['password']);
} else if(($_SERVER['REQUEST_METHOD'] === "GET")){
    checkLogin();
}else {
    sendJsonResponse("error", "unsupported request method");
}