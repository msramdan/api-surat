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

include_once '../config/database.php';
include_once '../class/Surat_Keluar.php';

$database = new Database();
$db = $database->getConnection();

$suratKeluar = new Surat_Keluar($db);

$data = $_POST;

if (
    !empty($data['id']) &&
    !empty($data['tgl_catat']) && !empty($data['tgl_surat']) &&
    !empty($data['no_surat']) && !empty($data['kategori']) &&
    !empty($data['dikirim_kepada']) &&
    !empty($data['perihal']) && !empty($data['keterangan'])
) {
    // Fetch old file names from the database
    $oldFileNames = $suratKeluar->getOldFileNames($data['id']);
    $oldImage_surat = $oldFileNames['image_surat'];
    $oldLampiran = $oldFileNames['lampiran'];

    $suratKeluar->id = $data['id'];
    $suratKeluar->tgl_catat = date('Y-m-d H:i:s', strtotime($data['tgl_catat']));
    $suratKeluar->tgl_surat = date('Y-m-d H:i:s', strtotime($data['tgl_surat']));
    $suratKeluar->no_surat = $data['no_surat'];
    $suratKeluar->kategori = $data['kategori'];
    $suratKeluar->dikirim_kepada = $data['dikirim_kepada'];
    $suratKeluar->perihal = $data['perihal'];
    $suratKeluar->keterangan = $data['keterangan'];

    // Handle image upload
    $uploadDirImage = '../assets/surat_keluar/';
    $imageExtension = pathinfo($_FILES['image_surat']['name'], PATHINFO_EXTENSION);
    $imageFileName = base64_encode(random_bytes(8)) . '_image_surat.' . $imageExtension;
    $imagePath = $uploadDirImage . $imageFileName;

    // Create directory if it doesn't exist
    if (!file_exists(dirname($imagePath))) {
        mkdir(dirname($imagePath), 0777, true);
    }

    // Move the uploaded image to the storage location
    if (move_uploaded_file($_FILES['image_surat']['tmp_name'], $imagePath)) {
        $suratKeluar->image_surat = $imageFileName; // Store image content in the database

        // Handle file (lampiran) upload
        $uploadDirFile = '../assets/surat_keluar/';
        $lampiranExtension = pathinfo($_FILES['lampiran']['name'], PATHINFO_EXTENSION);
        $lampiranFileName = base64_encode(random_bytes(8)) . '_lampiran.' . $lampiranExtension;
        $lampiranPath = $uploadDirFile . $lampiranFileName;

        // Create directory if it doesn't exist
        if (!file_exists(dirname($lampiranPath))) {
            mkdir(dirname($lampiranPath), 0777, true);
        }

        // Move the uploaded file to the storage location
        if (move_uploaded_file($_FILES['lampiran']['tmp_name'], $lampiranPath)) {
            $suratKeluar->lampiran = $lampiranFileName; // Store file content in the database

            // Update record in the database
            if ($suratKeluar->update()) {
                // Unlink old associated files
                if ($oldImage_surat && $oldImage_surat !== $suratKeluar->image_surat) {
                    $oldImagePath = '../assets/surat_keluar/' . $oldImage_surat;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                if ($oldLampiran && $oldLampiran !== $suratKeluar->lampiran) {
                    $oldLampiranPath = '../assets/surat_keluar/' . $oldLampiran;
                    if (file_exists($oldLampiranPath)) {
                        unlink($oldLampiranPath);
                    }
                }

                http_response_code(200);
                echo json_encode(array("message" => "Item was updated."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to update items."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to move the uploaded file (lampiran)."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Unable to move the uploaded image."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to update items. Data is incomplete."));
}
?>
