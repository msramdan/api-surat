<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../class/User.php';
include_once '../config/token_validation.php';

$database = new Database();
$db = $database->getConnection();
$headers = getallheaders();
$token = isset($headers['Authorization']) ? $headers['Authorization'] : '';


if (!empty($token)) {
    if (validateToken($db, $token)) {
        $user = new User($db);
        $validLevels = array(
            'Waka', 'Kanit Reskrim', 'Kanit Samapta', 'Kanit Intelkam', 'Kanit Binmas',
            'Kanit Lantas', 'Kanit Propam', 'Kanit Spkt I', 'Kanit Spkt II', 'Kanit Spkt III',
            'Kasi Umum', 'Kasi Humas', 'Admin', 'Pimpinan'
        );

        if (
            !empty($_POST['id']) && !empty($_POST['username']) &&
            !empty($_POST['password']) && !empty($_POST['nama_lengkap']) &&
            !empty($_POST['email']) && !empty($_POST['bidang_pekerjaan']) &&
            !empty($_POST['no_hp']) && !empty($_POST['level']) &&
            isset($_FILES['image_profile']) && !empty($_FILES['image_profile']['tmp_name'])
        ) {
            // Check if the provided level is valid
            if (in_array($_POST['level'], $validLevels)) {
                $user->id = $_POST['id'];
                $user->username = $_POST['username'];
                $user->password = $_POST['password'];
                $user->nama_lengkap = $_POST['nama_lengkap'];
                $user->email = $_POST['email'];
                $user->bidang_pekerjaan = $_POST['bidang_pekerjaan'];
                $user->no_hp = $_POST['no_hp'];
                $user->level = $_POST['level'];
                $existingUser = $user->getUserById($_POST['id']); // Assuming a method to get user details by ID
                if ($existingUser !== null) {
                    $existingImagePath = '../assets/user/' . $existingUser['image_profile'];
                    if (file_exists($existingImagePath)) {
                        unlink($existingImagePath);
                    }
                    $uploadDir = '../assets/user/';
                    $extension = pathinfo($_FILES['image_profile']['name'], PATHINFO_EXTENSION);
                    $imageName = base64_encode(random_bytes(8)) . '_profile_image.' . $extension;
                    $imagePath = $uploadDir . $imageName;
                    if (move_uploaded_file($_FILES['image_profile']['tmp_name'], $imagePath)) {
                        $user->image_profile = $imageName;
                        if ($user->update()) {
                            http_response_code(200);
                            echo json_encode(array("message" => "Item was updated."));
                        } else {
                            http_response_code(503);
                            echo json_encode(array("message" => "Unable to update items."));
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(array("message" => "Unable to move the uploaded image."));
                    }
                } else {
                    http_response_code(404);
                    echo json_encode(array("message" => "User with the provided ID not found."));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("message" => "Unable to update items. Invalid 'level' value."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to update items. Data is incomplete."));
        }
    } else {
        http_response_code(401);
        echo json_encode(array("message" => "Token tidak valid atau sudah kadaluarsa."));
    }
} else {
    http_response_code(401);
    echo json_encode(array("message" => "Token tidak ditemukan dalam header."));
}
