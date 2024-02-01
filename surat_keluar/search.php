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

include_once '../config/Database.php';
include_once '../class/Surat_Keluar.php';

$database = new Database();
$db = $database->getConnection();
 
$suratKeluar = new Surat_Keluar($db);

$suratKeluar->perihal = (isset($_GET['perihal']) && $_GET['perihal']) ? $_GET['perihal'] : '';

$result = $suratKeluar->search();

if($result->num_rows > 0){
    $suratRecords=array();
    $suratRecords["surat_keluar"]=array(); 
	while ($suratKeluar = $result->fetch_assoc()) { 	
        extract($suratKeluar); 
        $suratDetails=array(
            "id" => $id,
            "tgl_catat" => $tgl_catat,
            "tgl_surat" => $tgl_surat,
			"no_surat" => $no_surat,
            "kategori" => $kategori,
            "lampiran" => $lampiran,            
			"dikirim_kepada" => $dikirim_kepada,
            "perihal" => $perihal,
            "keterangan" => $keterangan,
            "image_surat" => $image_surat
        ); 
       array_push($suratRecords["surat_keluar"], $suratDetails);
    }    
    http_response_code(200);     
    echo json_encode($suratRecords);
}else{     
    http_response_code(404);     
    echo json_encode(
        array("message" => "No item found.")
    );
} 
?>