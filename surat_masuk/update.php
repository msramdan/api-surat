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

if(!empty($data->id) && !empty($data->tgl_penerimaan) && 
!empty($data->tgl_surat) && !empty($data->no_surat) && 
!empty($data->kategori) && !empty($data->lampiran) && 
!empty($data->dari_mana) && !empty($data->perihal) && 
!empty($data->keterangan) && !empty($data->image_surat)){ 

	
    $suratMasuk->id = $data->id;
	$suratMasuk->tgl_penerimaan = $data->tgl_penerimaan;
    $suratMasuk->tgl_surat = $data->tgl_surat;
    $suratMasuk->no_surat = $data->no_surat;
    $suratMasuk->kategori = $data->kategori;
    $suratMasuk->lampiran = $data->lampiran;	
    $suratMasuk->dari_mana = $data->dari_mana;
    $suratMasuk->perihal = $data->perihal;
    $suratMasuk->keterangan = $data->keterangan;
    $suratMasuk->image_surat = $data->image_surat; 
	
	if($suratMasuk->update()){     
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