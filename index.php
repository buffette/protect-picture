<?php
require_once 'config.php';
?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buffette - Protéger mes images personnelles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<main class="container">
    <h1 class="mb-4">Protéger mes images personnelles</h1>
    <form method="post" action="valid.php" target="_blank" enctype="multipart/form-data">
        <div class="row justify-content-center">
            <div class="col-md-12 border border-dark">
                <h2>Image :</h2>
                <div class="form-floating mb-3">
                    <input name="picture_file" class="form-control" type="file" accept="<?= implode(',', $conf['authorized_mime_type']) ?>" id="inputPictureFile" placeholder="Fichier image" aria-describedby="privacyPictureInfo">
                    <label for="inputPictureFile">Fichier de l'image à sécuriser :</label>
                    <div id="privacyPictureInfo" class="form-text">
                        <i class="bi bi-info-circle-fill"></i> Les images envoyées à notre serveur sont pas sauvegardées ! <i class="bi bi-info-circle-fill"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-12 border border-dark">
                <h2>Texte :</h2>
                <div class="form-floating mb-3">
                    <input name="text[content]" type="text" id="inputTextContent" class="form-control" placeholder="Texte à insérer">
                    <label for="inputTextContent">Texte à mettre sur l'image :</label>
                </div>
            </div>

            <div class="col-md-6 border border-dark">
                <h2>Choix du texte :</h2>
                    <div class="form-floating mb-3">
                        <select name="text[font]" id="selectTextFont" class="form-select" aria-label="Police du texte">
                            <option value="">Personnalisé</option>
                            <?php foreach (getEnabledFonts() as $fontFile => $fontName): ?>
                                <option value="<?= $fontFile ?>"<?= ($fontName === 'Arial') ? ' selected' : '' ?>><?= mb_convert_encoding($fontName, 'UTF-8', mb_list_encodings()) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="selectTextFont">Police du texte :</label>
                        <div class="d-grid gap-2">
                            <button id="buttonUseTtf" class="btn btn-outline-dark" type="button">Utiliser une police personnalisée fichier (.ttf)</button>
                        </div>
                    </div>
                    <div id="personalize-font-file" class="offset-md-1 form-floating mb-3">
                        <input name="font_file" class="form-control" type="file" accept=".ttf" id="inputFontFile" placeholder="Fichier police" aria-describedby="privacyPoliceInfo">
                        <label for="inputFontFile">Fichier de police personnalisé :</label>
                        <div id="privacyFontInfo" class="form-text">
                            <i class="bi bi-info-circle-fill"></i> Les images envoyées à notre serveur sont pas sauvegardées ! <i class="bi bi-info-circle-fill"></i>
                        </div>
                    </div>

                <div class="mb-3">
                    <label for="inputTextSize" class="form-label">Taille du texte :</label>
                    <div id="textSizeInfo" class="form-text">
                        <input type="text" value="" id="textSizeValue" size="2">/<?= $conf['text']['max_size'] ?>
                    </div>
                    <input name="text[size]" type="range" class="form-range" min="0" max="<?= $conf['text']['max_size'] ?>" id="inputTextSize" value="15" aria-describedby="textSizeInfo">
                </div>
            </div>

            <div class="col-md-6 border border-dark">
                <h2>Couleur :</h2>
                <div class="mb-3">
                    <label for="inputTextColorAlpha" class="form-label">Transparence de la couleur du texte :</label>
                    <div id="textColorAlphaInfo" class="form-text">
                        <input type="text" value="" id="textColorAlphaValue" size="2">/127
                    </div>
                    <input name="text[color][alpha]" type="range" class="form-range" min="0" max="127" id="inputTextColorAlpha" value="100" aria-describedby="textColorAlphaInfo">
                </div>
                <div class="mb-3">
                    <label for="inputTextColorCode">Code de la couleur du texte :</label>
                    <input name="text[color][code]" type="color" class="form-control form-control-color" id="inputTextColorCode" value="#FFFFFF" title="Choisissez la couleur de votre texte">
                </div>
            </div>

            <div class="col-md-6 col- border border-dark">
                <h2>Emplacement du texte :</h2>
                <div class="mb-3">
                    <label for="inputTextAngle" class="form-label">Inclinaison du texte :</label>
                    <div id="textAngleInfo" class="form-text">
                        <input type="text" value="" id="textAngleValue" size="2">°
                    </div>
                    <input name="text[angle]" type="range" class="form-range" min="0" max="360" id="inputTextAngle" value="0" aria-describedby="textAngleInfo">
                </div>
                <div class="form-floating mb-3">
                    <select name="text[position][emplacement]" id="selectTextPosition" class="form-select" aria-label="Position du texte">
                        <?php foreach ($conf['text']['emplacements'] as $emplacementCode => $emplacementName) : ?>
                            <option value="<?=$emplacementCode  ?>"><?= $emplacementName ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="selectTextPosition">Position du texte :</label>
                </div>
                <div id="personalize-text-border" class="offset-md-1 form-floating mb-3">
                    <select name="text[position][border]" id="selectTextBorder" class="form-select" aria-label="Marge entre le texte et le bord de l'image">
                        <option>0</option>
                        <?php for ($i = 0; $i < $conf['text']['border_max_level']; $i++) : ?>
                        <option><?= ($i + 1) * 5 ?></option>
                        <?php endfor; ?>
                    </select>
                    <label for="selectTextBorder">Marge entre le texte et le bord de l'image :</label>
                </div>
            </div>
        </div>

        <button type="submit" name="action" value="download" class="btn btn-primary mt-3">
            Télécharger

        </button>
        <button type="submit" name="action" value="preview" class="btn btn-primary mt-3">
            Prévisualiser

        </button>
    </form>
</main>
<footer class="align-items-center py-3 my-4 border-top">
    <div class="text-center text-body-secondary">© 2023 power by buffette ~ <?= $conf['version'] ?></div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.7.0.slim.min.js" integrity="sha256-tG5mcZUtJsZvyKAxYLVXrmjKBVLd6VpVccqz/r4ypFE=" crossorigin="anonymous"></script>
<script src="js/javascript.js"></script>
</body>
</html>
