<?php

session_start();

$database = new Database();
$db = $database->getConnection();

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
 
include_once '../config/database.php';
include_once '../class/Surat_Masuk.php';
 
$database = new Database();
$db = $database->getConnection();
 
$suratMasuk = new Surat_Masuk($db);
 
$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id) && !empty($data->klasifikasi) && 
!empty($data->derajat) && !empty($data->nomor_agenda) && 
!empty($data->isi_disposisi) && !empty($data->diteruskan_kepada)) { 

	
    $suratMasuk->id = $data->id;
	$suratMasuk->klasifikasi = $data->klasifikasi;
    $suratMasuk->derajat = $data->derajat;
    $suratMasuk->nomor_agenda = $data->nomor_agenda;
    $suratMasuk->isi_disposisi = $data->isi_disposisi;
    $suratMasuk->diteruskan_kepada = $data->diteruskan_kepada; 
	
	if($suratMasuk->updateDisposisi()){     
		http_response_code(200);   
		echo json_encode(array("message" => "Item was updated."));
	}else{    
		http_response_code(503);     
		echo json_encode(array("message" => "Unable to update items."));
	}
	
} else {
	http_response_code(400);    
    echo json_encode(array("message" => "Unable to update items. Data is incomplete."));
}
?>