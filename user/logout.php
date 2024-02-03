<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../config/token_validation.php';

$database = new Database();
$db = $database->getConnection();

$headers = getallheaders();
$token = isset($headers['Authorization']) ? $headers['Authorization'] : '';

if (!empty($token)) {
    if (validateToken($db, $token)) {
        $tokenParts = explode(" ", $token);
        $actualToken = $tokenParts[1];
        $updateTokenQuery = "UPDATE user SET token = null, token_expiration = null WHERE token = '$actualToken'";
        $db->query($updateTokenQuery);
        http_response_code(200);
        echo json_encode(array("message" => "Logout berhasil."));
    } else {
        http_response_code(401);
        echo json_encode(array("message" => "Token tidak valid atau sudah kadaluarsa."));
    }
} else {
    http_response_code(401);
    echo json_encode(array("message" => "Token tidak ditemukan dalam header."));
}
?>
