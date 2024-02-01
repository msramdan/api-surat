<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/Database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->username) && !empty($data->password)) {
    $username = $db->real_escape_string($data->username);
    $password = $db->real_escape_string($data->password);

    $query = "SELECT * FROM user WHERE username = '$username' AND password = '$password'";
    $result = $db->query($query);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Simpan informasi pengguna dalam sesi
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['level'] = $user['level'];

        http_response_code(200);
        echo json_encode(array(
            "id" => $user['id'],
            "nama_lengkap" => $user['nama_lengkap'],
            "email" => $user['email'],
            "username" => $user['username'],
            "password" => $user['password'],
            "bidang_pekerjaan" => $user['bidang_pekerjaan'],
            "no_hp" => $user['no_hp'],
            "level" => $user['level'],
            "image_profile" => $user['image_profile']
        ));
    } else {
        http_response_code(401);
        echo json_encode(array("message" => "Login gagal. Cek kembali username dan password."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Username dan password diperlukan."));
}
?>