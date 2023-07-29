<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

//CONFIG
require_once 'config.php';

function resultJsonError($errorMessage) {
    return [
        'valid' => false,
        'errorMessage' => $errorMessage
    ];
}

function resultValid($image) {
    ob_start();
    imagejpeg($image, null, 100);

    // Capture the output
    $imageBase64 = base64_encode(ob_get_contents());

    // Clear the output buffer
    ob_end_clean();

    $imageBase64Adapt = null;
    if (imagesx($image) < imagesy($image)) {
        $image = imagerotate($image, 90, 0);
        ob_start();
        imagejpeg($image, null, 100);

        // Capture the output
        $imageBase64Adapt = base64_encode(ob_get_contents());

        // Clear the output buffer
        ob_end_clean();
    }

    return [
        'valid' => true,
        'imageBase64' => $imageBase64,
        'imageBase64Adapt' => $imageBase64Adapt
    ];
}

if(empty($_POST) || empty($_FILES)) {
    die('Aucune donnée reçu ! Il se peut que le fichier envoyé soit trop gros.');
}

if (!isset($_FILES['picture']) || $_FILES['picture']['name'] === '') {
    die('Le fichier image est manquant !');
}

if ($_FILES['picture']['error'] !== 0) {
    $messageError = 'Erreur lors de l\'envoie du fichier : ';
    switch ($_FILES['picture']['error']) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            $messageError .= 'la taille du fichier envoyé est trop grande !';
            break;
        case UPLOAD_ERR_PARTIAL:
            $messageError .= 'le fichier n\'a été que partiellement réçu !';
            break;
        case UPLOAD_ERR_NO_FILE:
            $messageError .= 'aucun fichier n\'a été reçu !';
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
        case UPLOAD_ERR_CANT_WRITE:
        case UPLOAD_ERR_EXTENSION:
            $messageError .= 'le serveur a rencontré une erreur lors du traitement du fichier !';
            break;
    }
    die($messageError);
}

$imageType = mime_content_type($_FILES['picture']['tmp_name']);

if (!in_array($imageType, $conf['authorized_mime_type'])) {
    die('Le type de l\'image (' . $imageType . ') n\'est pas supporté !');
}

if ($imageType === 'image/jpeg') {
    $image = imagecreatefromjpeg($_FILES['picture']['tmp_name']);

    $exif = exif_read_data($_FILES["picture"]["tmp_name"]);
    if (isset($exif['Orientation'])) {
        switch ($exif['Orientation']) {
            case 2:
                imageflip($image, IMG_FLIP_HORIZONTAL);
                break;
            case 3:
                $image = imagerotate($image, 180, 0);
                break;
            case 4:
                imageflip($image, IMG_FLIP_VERTICAL);
                break;
            case 5:
                $image = imagerotate($image, -90, 0);
                imageflip($image, IMG_FLIP_HORIZONTAL);
                break;
            case 6:
                $image = imagerotate($image, -90, 0);
                break;
            case 7:
                $image = imagerotate($image, 90, 0);
                imageflip($image, IMG_FLIP_HORIZONTAL);
                break;
            case 8:
                $image = imagerotate($image, 90, 0);
                break;
        }
    }
} elseif ($imageType === 'image/png') {
    $image = imagecreatefrompng($_FILES['picture']['tmp_name']);
} elseif ($imageType === 'image/gif') {
    $image = imagecreatefromgif($_FILES['picture']['tmp_name']);
} else {
    die('Ce format d\'image sera pris en charche très bientôt !');
}

if ($_POST['action'] === 'preview') {
    header("Content-Type: application/json");
    $result = resultValid($image);
    echo json_encode($result);
} else {
    header("Content-Type: application/octet-stream");
    header("Content-Transfer-Encoding: Binary");
    if ($imageType === 'image/jpeg') {
        header('Content-disposition: attachment; filename="protect-picture' . uniqid() . '.jpg"');
        imagejpeg($image, null, 100);
    } elseif ($imageType === 'image/png') {
        header('Content-disposition: attachment; filename="protect-picture' . uniqid() . '.png"');
        imagepng($image, null, 0);
    }
}

imagedestroy($image);

unlink($_FILES['picture']['tmp_name']);
if (!empty($_FILES['font_file']['tmp_name'])) {
    unlink($_FILES['font_file']['tmp_name']);
}