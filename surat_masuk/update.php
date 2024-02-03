<?php


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

$data = $_POST;


if (
    !empty($data['id']) &&
    !empty($data['tgl_penerimaan']) && !empty($data['tgl_surat']) &&
    !empty($data['no_surat']) && !empty($data['kategori']) &&
    !empty($data['dari_mana']) && !empty($data['perihal']) &&
    !empty($data['keterangan'])
) {

    // Fetch old file names from the database
    $oldFileNames = $suratMasuk->getOldFileNames($data['id']);
    $oldImage_surat = $oldFileNames['image_surat'];
    $oldLampiran = $oldFileNames['lampiran'];
    $suratMasuk->id = $data['id'];
    $suratMasuk->tgl_penerimaan = date('Y-m-d H:i:s');
    $suratMasuk->tgl_surat = date('Y-m-d H:i:s');
    $suratMasuk->no_surat = $data['no_surat'];
    $suratMasuk->kategori = $data['kategori'];
    $suratMasuk->dari_mana = $data['dari_mana'];
    $suratMasuk->perihal = $data['perihal'];
    $suratMasuk->keterangan = $data['keterangan'];


    // Handle image upload
    $uploadDirImage = '../assets/surat_masuk/';
    $imageExtension = pathinfo($_FILES['image_surat']['name'], PATHINFO_EXTENSION);
    $imageFileName = base64_encode(random_bytes(8)) . '_image_surat.' . $imageExtension;
    $imagePath = $uploadDirImage . $imageFileName;

    // Create directory if it doesn't exist
    if (!file_exists(dirname($imagePath))) {
        mkdir(dirname($imagePath), 0777, true);
    }

    // Move the uploaded image to the storage location
    if (move_uploaded_file($_FILES['image_surat']['tmp_name'], $imagePath)) {
        $suratMasuk->image_surat = $imageFileName; // Store image content in the database

        // Handle file (lampiran) upload
        $uploadDirFile = '../assets/surat_masuk/';
        $lampiranExtension = pathinfo($_FILES['lampiran']['name'], PATHINFO_EXTENSION);
        $lampiranFileName = base64_encode(random_bytes(8)) . '_lampiran.' . $lampiranExtension;
        $lampiranPath = $uploadDirFile . $lampiranFileName;

        // Create directory if it doesn't exist
        if (!file_exists(dirname($lampiranPath))) {
            mkdir(dirname($lampiranPath), 0777, true);
        }

        // Move the uploaded file to the storage location
        if (move_uploaded_file($_FILES['lampiran']['tmp_name'], $lampiranPath)) {
            $suratMasuk->lampiran = $lampiranFileName; // Store file content in the database

            // Update record in the database
            if ($suratMasuk->update()) {
                // Unlink old associated files
                if ($oldImage_surat && $oldImage_surat !== $suratMasuk->image_surat) {
                    $oldImagePath = '../assets/surat_masuk/' . $oldImage_surat;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                if ($oldLampiran && $oldLampiran !== $suratMasuk->lampiran) {
                    $oldLampiranPath = '../assets/surat_masuk/' . $oldLampiran;
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
