<?php

session_start();
// Periksa sesi untuk memastikan pengguna telah login
if (!isset($_SESSION['user_id'])) {
	http_response_code(401);
	echo json_encode(array("message" => "Unauthorized. Harap login terlebih dahulu."));
	exit();
}


header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/Database.php';
include_once '../class/Surat_Masuk.php';

$database = new Database();
$db = $database->getConnection();

$suratMasuk = new Surat_Masuk($db);

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->id)) {
	$suratMasuk->id = $data->id;
	if ($suratMasuk->delete()) {
		http_response_code(200);
		echo json_encode(array("message" => "Item was deleted."));
	} else {
		http_response_code(503);
		echo json_encode(array("message" => "Unable to delete item."));
	}
} else {
	http_response_code(400);
	echo json_encode(array("message" => "Unable to delete items. Data is incomplete."));
}
