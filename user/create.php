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

$user = new User($db);

$data = json_decode(file_get_contents("php://input"));
$headers = getallheaders();
$token = isset($headers['Authorization']) ? $headers['Authorization'] : '';


if (!empty($token)) {
    if (validateToken($db, $token)) {
        $validLevels = array(
            'Waka', 'Kanit Reskrim', 'Kanit Samapta', 'Kanit Intelkam', 'Kanit Binmas',
            'Kanit Lantas', 'Kanit Propam', 'Kanit Spkt I', 'Kanit Spkt II', 'Kanit Spkt III',
            'Kasi Umum', 'Kasi Humas', 'Admin', 'Pimpinan'
        );

        if (!empty($data->username) && !empty($data->password) && !empty($data->level) && !empty($data->nama_lengkap)) {
            if (in_array($data->level, $validLevels)) {
                if ($user->isUsernameUnique($data->username)) {

                    $user->username = $data->username;
                    $user->password = $data->password;
                    $user->level = $data->level;
                    $user->nama_lengkap = $data->nama_lengkap;

                    if ($user->create()) {
                        http_response_code(200);
                        echo json_encode(array("message" => "User was created."));
                    } else {
                        http_response_code(503);
                        echo json_encode(array("message" => "Unable to create User."));
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(array("message" => "Unable to create User. Username is not unique."));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("message" => "Unable to create User. Invalid 'level' value."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to create User. Data is incomplete."));
        }
    } else {
        http_response_code(401);
        echo json_encode(array("message" => "Token tidak valid atau sudah kadaluarsa."));
    }
} else {
    http_response_code(401);
    echo json_encode(array("message" => "Token tidak ditemukan dalam header."));
}
