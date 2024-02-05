<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/Database.php';
include_once '../class/Surat_Keluar.php';
include_once '../config/config.php';
include_once '../config/token_validation.php';

$database = new Database();
$db = $database->getConnection();


$headers = getallheaders();
$token = isset($headers['Authorization']) ? $headers['Authorization'] : '';
if (!empty($token)) {
    if (validateToken($db, $token)) {
        $suratKeluar = new Surat_Keluar($db);

$suratKeluar->perihal = (isset($_GET['perihal']) && $_GET['perihal']) ? $_GET['perihal'] : '';

$result = $suratKeluar->search();

if ($result->num_rows > 0) {
    $suratRecords = array();
    $suratRecords["surat_keluar"] = array();
    while ($suratKeluar = $result->fetch_assoc()) {
        extract($suratKeluar);

        $lampiran_url = null;
        if ($lampiran !== null && $lampiran !== '') {
            $lampiran_url = $base_url . '/user' . $lampiran;
        }

        $image_surat_url = null;
        if ($image_surat !== null && $image_surat !== '') {
            $image_surat_url = $base_url . '/user' . $image_surat;
        }

        $suratDetails = array(
            "id" => $id,
            "tgl_catat" => $tgl_catat,
            "tgl_surat" => $tgl_surat,
            "no_surat" => $no_surat,
            "kategori" => $kategori,
            "lampiran" => $lampiran_url,
            "dikirim_kepada" => $dikirim_kepada,
            "perihal" => $perihal,
            "keterangan" => $keterangan,
            "image_surat" => $image_surat_url
        );
        array_push($suratRecords["surat_keluar"], $suratDetails);
    }
    http_response_code(200);
    echo json_encode($suratRecords);
} else {
    http_response_code(404);
    echo json_encode(
        array("message" => "No item found.")
    );
}

    } else {
        http_response_code(401);
        echo json_encode(array("message" => "Token tidak valid atau sudah kadaluarsa."));
    }
} else {
    http_response_code(401);
    echo json_encode(array("message" => "Token tidak ditemukan dalam header."));
}
