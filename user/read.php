<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../class/User.php';

// Memulai sesi
session_start();

$database = new Database();
$db = $database->getConnection();

// Periksa sesi untuk memastikan pengguna telah login
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(array("message" => "Unauthorized. Harap login terlebih dahulu."));
    exit();
}

$user = new User($db);
$user->id = (isset($_GET['id']) && $_GET['id']) ? $_GET['id'] : '0';

$result = $user->read();

if ($result->num_rows > 0) {
    $userRecords = array();
    $userRecords["user"] = array();
    while ($user = $result->fetch_assoc()) {
        extract($user);
        $userDetails = array(
            "id" => $id,
            "username" => $username,
            "password" => $password,
            "nama_lengkap" => $nama_lengkap,
            "email" => $email,
            "bidang_pekerjaan" => $bidang_pekerjaan,
            "no_hp" => $no_hp,
            "level" => $level,
            "image_profile" => $image_profile
        );
        array_push($userRecords["user"], $userDetails);
    }
    http_response_code(200);
    echo json_encode($userRecords);
} else {
    http_response_code(404);
    echo json_encode(
        array("message" => "No item found.")
    );
}
?>
