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
include_once '../class/Surat_Masuk.php';
include_once '../config/config.php';

$database = new Database();
$db = $database->getConnection();

$suratMasuk = new Surat_Masuk($db);

$suratMasuk->perihal = (isset($_GET['perihal']) && $_GET['perihal']) ? $_GET['perihal'] : '';

$result = $suratMasuk->search();

if ($result->num_rows > 0) {
    $suratRecords = array();
    $suratRecords["surat_masuk"] = array();
    while ($suratMasuk = $result->fetch_assoc()) {
        extract($suratMasuk);
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
            "tgl_penerimaan" => $tgl_penerimaan,
            "tgl_surat" => $tgl_surat,
            "no_surat" => $no_surat,
            "kategori" => $kategori,
            "lampiran" => $lampiran_url,
            "dari_mana" => $dari_mana,
            "perihal" => $perihal,
            "keterangan" => $keterangan,
            "image_surat" => $image_surat_url,
            "klasifikasi" => $klasifikasi,
            "derajat" => $derajat,
            "nomor_agenda" => $nomor_agenda,
            "isi_disposisi" => $isi_disposisi,
            "diteruskan_kepada" => $diteruskan_kepada
        );
        array_push($suratRecords["surat_masuk"], $suratDetails);
    }
    http_response_code(200);
    echo json_encode($suratRecords);
} else {
    http_response_code(404);
    echo json_encode(
        array("message" => "No item found.")
    );
}
