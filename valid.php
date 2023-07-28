<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

//CONFIG
require_once 'config.php';
$errors = [];
//CONFIG

//RULES
if(empty($_POST) || empty($_FILES)) {
    die('Cette page n\'est pas pour toi !');
}

if (isset($_FILES['picture_file']) && $_FILES['picture_file']['name'] !== '') {
    if ($_FILES['picture_file']['error'] !== 0) {
        $messageError = 'Erreur lors de l\'envoie du fichier : ';
        switch ($_FILES['picture_file']['error']) {
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
    $imageType = mime_content_type($_FILES['picture_file']['tmp_name']);
    if (!in_array($imageType, $conf['authorized_mime_type'])) {
        die('Le type de l\'image (' . $imageType . ') n\'est pas supporté !');
    }
} else {
    var_dump($_FILES);
    die('Le fichier image est manquant !');
}

if (isset($_POST['text']['font'])) {
    if (!empty($_POST['text']['font'])) {
        $font = getFont($_POST['text']['font']);
    } else {
        $fontType = mime_content_type($_FILES['font_file']['tmp_name']);
        if ($fontType === 'font/sfnt') {
            $font = $_FILES['font_file']['tmp_name'];
        } else {
            $errors['font_file'][] = 'Le type de police (' . $fontType . ') n\'est pas supporté !';
        }
    }
} else {
    $errors['font_file'][] = 'La police est manquante !';
}

//traiter le texte

if (isset($_POST['text']['size'])) {
    if (!is_numeric($_POST['text']['size']) || str_contains($_POST['text']['size'], '.')) {
        $errors['text']['size'][] = 'La taille du texte doit être un nombre entier !';
    } elseif ($_POST['text']['size'] < 0) {
        $errors['text']['size'][] = 'La taille du texte ne doit être positive !';
    } elseif ($_POST['text']['size'] > $conf['text']['max_size']) {
        $errors['text']['size'][] = 'La taille du texte ne doite pas être supérieur à ' . $conf['text']['max_size'];
    }
} else {
    $errors['picture_file'][] = 'La taille du texte est manquante !';
}

if (isset($_POST['text']['color']['alpha'])) {
    if (!is_numeric($_POST['text']['color']['alpha']) || str_contains($_POST['text']['color']['alpha'], '.')) {
        $errors['text']['color']['alpha'][] = 'La transparence du texte doit être un nombre entier !';
    } elseif ($_POST['text']['color']['alpha'] < 0) {
        $errors['text']['color']['alpha'][] = 'La transparence du texte doit être positive !';
    } elseif ($_POST['text']['color']['alpha'] > 127) {
        $errors['text']['color']['alpha'][] = 'La transparence du texte ne doite pas être supérieur à 127';
    }
} else {
    $errors['text']['color']['alpha'][] = 'La transparence de la couleur du texte est manquante !';
}

if (isset($_POST['text']['color']['code'])) {
    if (mb_strlen($_POST['text']['color']['code']) === 7) {
        $code = substr($_POST['text']['color']['code'], 1);
        $colors = str_split($code, 2);
        $red = hexdec($colors[0]);
        $green = hexdec($colors[1]);
        $blue = hexdec($colors[2]);
    } else {
        $errors['text']['color']['code'][] = 'Le code de la couleur du texte est invalide !';
    }
} else {
    $errors['text']['color']['code'][] = 'Le code de la couleur du texte est manquante !';
}

if ($imageType === 'image/jpeg') {
    $image = imagecreatefromjpeg($_FILES['picture_file']['tmp_name']);
} elseif ($imageType === 'image/png') {
    $image = imagecreatefrompng($_FILES['picture_file']['tmp_name']);
} elseif ($imageType === 'image/gif') {
    $image = imagecreatefromgif($_FILES['picture_file']['tmp_name']);
} else {
    die('Ce format d\'image sera pris en charche très bientôt !');
}

if (isset($_POST['text']['position']['emplacement'])) {
    if (isset($conf['text']['emplacements'][$_POST['text']['position']['emplacement']])) {
        if (isset($_POST['text']['angle'])) {
            if (!is_numeric($_POST['text']['angle']) || str_contains($_POST['text']['angle'], '.')) {
                $errors['text']['angle'][] = 'L\'inclinaison du texte doit être un nombre entier !';
            } elseif ($_POST['text']['angle'] < 0) {
                $errors['text']['angle'][] = 'L\'inclinaison du texte doit être positif !';
            } elseif ($_POST['text']['angle'] > 360) {
                $errors['text']['angle'][] = 'L\'inclinaison du texte ne doite pas être supérieur à 360° !';
            } else {
                $bbox = imagettfbbox($_POST['text']['size'], $_POST['text']['angle'], $font, $_POST['text']['content']);

                if (!$bbox) {
                    die('Une erreur c\'est produite (police : ' . $font . ') !');
                }

                $positions = explode("_", $_POST['text']['position']['emplacement']);
                if ($_POST['text']['angle'] < 90) { //45
                    $bboxWeight = $bbox[2] - $bbox[6];
                    $bboxHeight = $bbox[5] - $bbox[1];

                    switch ($positions[0]) {
                        case 'left':
                            $posX = -$bbox[6];
                            break;
                        case 'right':
                            $posX = imagesx($image) - $bboxWeight - $bbox[6];
                            break;
                        case 'middle':
                            $posX = round((imagesx($image) / 2) - ($bboxWeight / 2) - $bbox[6]);
                            break;
                        default:
                            $errors['text']['position']['emplacement'][] = 'Cette emplacement n\'est pas ecore pris en charge avec cettte inclinaison !';
                            break;
                    }

                    switch ($positions[1]) {
                        case 'top':
                            $posY = -$bboxHeight - $bbox[1];
                            break;
                        case 'bottom':
                            $posY = imagesy($image) - $bbox[1];
                            break;
                        case 'middle':
                            $posY = round((imagesy($image) / 2) - ($bboxHeight / 2) - $bbox[1]);
                            break;
                        default:
                            $errors['text']['position']['emplacement'][] = 'Cette emplacement n\'est pas ecore pris en charge avec cettte inclinaison !';
                            break;
                    }
                } elseif ($_POST['text']['angle'] < 180) { //135
                    $bboxWeight = $bbox[0] - $bbox[4];
                    $bboxHeight = $bbox[3] - $bbox[7];

                    switch ($positions[0]) {
                        case 'left':
                            $posX = $bboxWeight - $bbox[0];
                            break;
                        case 'right':
                            $posX = imagesx($image) - $bbox[0];
                            break;
                        case 'middle':
                            $posX = round((imagesx($image) / 2) - ($bboxWeight / 2) - $bbox[4]);
                            break;
                        default:
                            $errors['text']['position']['emplacement'][] = 'Cette emplacement n\'est pas ecore pris en charge avec cettte inclinaison !';
                            break;
                    }

                    switch ($positions[1]) {
                        case 'top':
                            $posY = -$bboxHeight - $bbox[7];
                            break;
                        case 'bottom':
                            $posY = imagesy($image) - $bbox[7];
                            break;
                        case 'middle':
                            $posY = round((imagesy($image) / 2) - ($bboxHeight / 2) - $bbox[7]);
                            break;
                        default:
                            $errors['text']['position']['emplacement'][] = 'Cette emplacement n\'est pas ecore pris en charge avec cettte inclinaison !';
                            break;
                    }
                } elseif ($_POST['text']['angle'] < 270) { //225
                    $bboxWeight = $bbox[6] - $bbox[2];
                    $bboxHeight = $bbox[1] - $bbox[5];

                    switch ($positions[0]) {
                        case 'left':
                            $posX = $bboxWeight - $bbox[6];
                            break;
                        case 'right':
                            $posX = imagesx($image) - $bbox[6];
                            break;
                        case 'middle':
                            $posX = round((imagesx($image) / 2) - ($bboxWeight / 2) - $bbox[2]);
                            break;
                        default:
                            $errors['text']['position']['emplacement'][] = 'Cette emplacement n\'est pas ecore pris en charge avec cettte inclinaison !';
                            break;
                    }

                    switch ($positions[1]) {
                        case 'top':
                            $posY = -$bbox[1];
                            break;
                        case 'bottom':
                            $posY = imagesy($image) + $bboxHeight - $bbox[1];
                            break;
                        case 'middle':
                            $posY = round((imagesy($image) / 2) - ($bboxHeight / 2) - $bbox[5]);
                            break;
                        default:
                            $errors['text']['position']['emplacement'][] = 'Cette emplacement n\'est pas ecore pris en charge avec cettte inclinaison !';
                            break;
                    }
                } elseif ($_POST['text']['angle'] <= 360) { //315
                    $bboxWeight = $bbox[4] - $bbox[0];
                    $bboxHeight = $bbox[7] - $bbox[3];

                    switch ($positions[0]) {
                        case 'left':
                            $posX = -$bbox[0];
                            break;
                        case 'right':
                            $posX = imagesx($image) - $bboxWeight - $bbox[0];
                            break;
                        case 'middle':
                            $posX = round((imagesx($image) / 2) - ($bboxWeight / 2) - $bbox[0]);
                            break;
                        default:
                            $errors['text']['position']['emplacement'][] = 'Cette emplacement n\'est pas ecore pris en charge avec cettte inclinaison !';
                            break;
                    }

                    switch ($positions[1]) {
                        case 'top':
                            $posY = -$bbox[7];
                            break;
                        case 'bottom':
                            $posY = imagesy($image) + $bboxHeight - $bbox[7];
                            break;
                        case 'middle':
                            $posY = round((imagesy($image) / 2) - ($bboxHeight / 2) - $bbox[3]);
                            break;
                        default:
                            $errors['text']['position']['emplacement'][] = 'Cette emplacement n\'est pas ecore pris en charge avec cettte inclinaison !';
                            break;
                    }
                }

                if (empty($errors)) {
                    if (isset($_POST['text']['position']['border'])) {
                        if (!is_numeric($_POST['text']['position']['border']) || str_contains($_POST['text']['position']['border'], '.')) {
                            $errors['text']['angle'][] = 'La marge entre le texte et le bord de l\'image doit être un nombre entier !';
                        } elseif ($_POST['text']['position']['border'] < 0) {
                            $errors['text']['position']['border'][] = 'La marge entre le texte et le bord de l\'image doit être positif !';
                        } elseif (fmod($_POST['text']['position']['border'], 5) != 0) {
                            $errors['text']['position']['border'][] = 'La marge entre le texte et le bord de l\'image doit être un multiple de 5 !';
                        } elseif ($_POST['text']['position']['border'] > $conf['text']['border_max_level'] * 5) {
                            $errors['text']['position']['border'][] = 'La marge entre le texte et le bord de l\'image ne doite pas être supérieur à ' . $conf['text']['border_max_level'] * 5 . ' !';
                        } elseif ($_POST['text']['position']['border'] !== 0) {
                            if ($positions[0] === 'left') {
                                $posX += $_POST['text']['position']['border'];
                                $bboxWeight += $_POST['text']['position']['border'];
                            } elseif ($positions[0] === 'right') {
                                $posX -= $_POST['text']['position']['border'];
                                $bboxWeight += $_POST['text']['position']['border'];
                            }
                            if ($positions[1] === 'top') {
                                $posY += $_POST['text']['position']['border'];
                                $bboxHeight += $_POST['text']['position']['border'];
                            } elseif ($positions[1] === 'bottom') {
                                $posY -= $_POST['text']['position']['border'];
                                $bboxHeight += $_POST['text']['position']['border'];
                            }
                        }
                    } else {
                        $errors['text']['position']['border'][] = 'La marge entre le texte et le bord de l\'image n\'est pas définie !';
                    }
                }

                if (imagesx($image) < $bboxWeight) {
                    $errors['text']['size'][] = 'Le texte est trop grand en largeur !';
                }

                if (imagesy($image) < abs($bboxHeight)) {
                    $errors['text']['size'][] = 'Le texte est trop grand en hauteur !';
                }
            }
        } else {
            $errors['text']['angle'][] = 'L\'inclinaison du texte est manquante !';
        }
    } else {
        $errors['position']['emplacement'][] = 'Cet emplacement du texte n\'est pas supporté !';
    }
} else {
    $errors['position']['emplacement'][] = 'L\'emplacement du texte est manquant !';
}
//RULES

if (!empty($errors)) {
    echo '<pre>';
    print_r($errors);
    echo '</pre>';
    die();
}

//$textcolor = imagecolorallocatealpha($image, $text['color']['red'], $text['color']['green'], $text['color']['blue'], $text['color']['alpha']);
$textcolor = imagecolorallocatealpha($image, $red, $green, $blue, $_POST['text']['color']['alpha']);

//imagettftext($image, $text['size'] * 5, $text['angle'], $text['position']['x'], $text['position']['y'], $textcolor, $text['font'], $text['massage']);
$imageTtfText = imagettftext($image, $_POST['text']['size'], $_POST['text']['angle'], $posX, $posY, $textcolor, $font, $_POST['text']['content']);

//imagepolygon(
//    $image,
//    array(
//        $imageTtfText[6],   $imageTtfText[7],
//        $imageTtfText[0], $imageTtfText[1],
//        $imageTtfText[2], $imageTtfText[3],
//        $imageTtfText[4], $imageTtfText[5]
//    ),
//    $textcolor);

if ($_POST['action'] === 'download') {
    header("Content-Type: application/octet-stream");
    header("Content-Transfer-Encoding: Binary");
    if ($imageType === 'image/jpeg') {
        header('Content-disposition: attachment; filename="protect-picture' . uniqid() . '.jpg"');
        imagejpeg($image);
    } elseif ($imageType === 'image/png') {
        header('Content-disposition: attachment; filename="protect-picture' . uniqid() . '.png"');
        imagepng($image);
    } elseif ($imageType === 'image/gif') {
        header('Content-disposition: attachment; filename="protect-picture' . uniqid() . '.gif"');
        imagegif($image);
    }
} else {
    if ($imageType === 'image/jpeg') {
        header('Content-type: image/jpeg');
        imagejpeg($image);
    } elseif ($imageType === 'image/png') {
        header('Content-type: image/png');
        imagepng($image);
    } elseif ($imageType === 'image/gif') {
        header('Content-type: image/gif');
        imagegif($image);
    }
}

imagedestroy($image);

unlink($_FILES['picture_file']['tmp_name']);
if (!empty($_FILES['font_file']['tmp_name'])) {
    unlink($_FILES['font_file']['tmp_name']);
}
?>