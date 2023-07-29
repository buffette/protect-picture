<?php
require_once 'config.php';
?>

<!doctype html>
<html lang="fr" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buffette - Protéger mes images personnelles</title>
    <link rel="stylesheet" href="./node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./node_modules/bootstrap-icons/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="./styles/template.css">
</head>
<body class="d-flex flex-column h-100">
<main class="flex-shrink-0">
    <div class="container">
        <h1 class="has-description">Protéger mes images personnelles</h1>
        <small class="description mb-4">Version actuelle : <?= $string_version() ?> | Dernière mise à jour : <?= $conf['version']['update'] ?></small>
        <?php if ($conf['version']['env'] === VERSION_ENV_DEV) : ?>
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" class="alert-icon-3 bi bi-exclamation-triangle-fill flex-shrink-0 me-4" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                </svg>
                <div>
                    <h2>
                        Version <?= $string_env()['long'] ?>
                    </h2>
                    Cette version est actuellement en développement, il se peut que certaines fonctionnalités, voir toutes, ne soient pas disponnibles.
                </div>
            </div>
        <?php endif; ?>

        <div class="alert alert-info d-flex align-items-center" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" class="alert-icon-3 bi bi-info-circle-fill flex-shrink-0 me-4" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
            </svg>
            <div>
                <h2>Le site est passé en version 2 !</h2>
                Voici les principaux changements mis en place pour cette nouvelle version majeur :
                <ul>
                    <li>Il est désormais possible de prévisuialiser l'image avant le téléchargement, directement dans le formulaire.</li>
                    <li>Les fichiers type image/gif (.gif) ne sont plus pris en charge.</li>
                </ul>
            </div>
        </div>

        <form id="form-protect-picture" method="post" action="valid.php" target="_blank" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-12 border border-dark">
                    <label for="inputAddPictureFile"><h2>Image :</h2></label>
                    <div class="mb-3">
                        <input name="picture" class="form-control form-control-lg" type="file" accept="<?= implode(',', $conf['authorized_mime_type']) ?>" id="inputAddPictureFile" placeholder="Fichier image" multiple aria-describedby="privacy-picture-info">
                        <div id="privacy-picture-info" class="form-text">
                            <i class="bi bi-info-circle-fill"></i> Les images envoyées à notre serveur sont pas sauvegardées ! <i class="bi bi-info-circle-fill"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 border border-dark">
<!--                    <h2>Information sur le texte :</h2>-->
<!--                    <h3>Message :</h3>-->
<!--                    <div class="form-floating mb-3">-->
<!--                        <input name="text[content]" type="text" id="inputTextContent" class="form-control" placeholder="Texte à insérer">-->
<!--                        <label for="inputTextContent">Texte à mettre sur l'image :</label>-->
<!--                    </div>-->
<!--                    <h3>Police :</h3>-->
<!--                    <div class="d-grid gap-2">-->
<!--                        <input type="checkbox" class="btn-check" id="buttonUseTtf" autocomplete="off">-->
<!--                        <label class="btn btn-outline-secondary" for="buttonUseTtf">Utiliser une police personnalisée fichier (.ttf)</label><br>-->
<!--                    </div>-->
<!--                    <div id="default-fonts" class="form-floating mb-3">-->
<!--                        <select name="text[font]" id="selectTextFont" class="form-select" aria-label="Police du texte">-->
<!--                            <option value="">Personnalisé</option>-->
<!--                            <?php foreach (getEnabledFonts() as $fontFile => $fontName): ?>
<!--                                <option value="<?= $fontFile ?>"<?= ($fontName === 'Arial') ? ' selected' : '' ?>><?= mb_convert_encoding($fontName, 'UTF-8', mb_list_encodings()) ?></option>-->
<!--                            <?php endforeach; ?>-->
<!--                        </select>-->
<!--                        <label for="selectTextFont">Police du texte :</label>-->
<!--                    </div>-->
<!--                    <div id="personalize-font" class="form-floating mb-3">-->
<!--                        <input name="font_file" class="form-control" type="file" accept=".ttf" id="inputFontFile" placeholder="Fichier police" aria-describedby="privacyPoliceInfo">-->
<!--                        <label for="inputFontFile">Fichier de police personnalisé :</label>-->
<!--                        <div id="privacyFontInfo" class="form-text">-->
<!--                            <i class="bi bi-info-circle-fill"></i> Les images envoyées à notre serveur sont pas sauvegardées ! <i class="bi bi-info-circle-fill"></i>-->
<!--                        </div>-->
<!--                    </div>-->
                </div>
                <div class="col-md-6 border border-dark">
                    <a id="preview-picture" href="#" data-bs-toggle="modal" data-bs-target="#modal-preview"></a>
                </div>
            </div>
            <button type="submit" name="action" value="preview" class="btn btn-primary mt-3">
                Prévisualiser
            </button>
            <button type="submit" name="action" value="download" class="btn btn-primary mt-3">
                Télécharger
            </button>
        </form>
    </div>
</main>

<!-- Modal -->
<div class="modal fade" id="modal-preview" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog">

    </div>
<!--    <div class="modal-dialog modal-fullscreen" style="position: absolute;">-->
<!--        <div class="modal-content">-->
<!--            <div class="modal-body text-center"></div>-->
<!--        </div>-->
<!--        <div class="modal-content">-->
<!--            <div class="modal-body text-center"></div>-->
<!--        </div>-->
<!--    </div>-->
</div>

<footer class="mt-auto py-3 bg-light border-top">
    <div class="container">
        <span class="text-muted">© 2023 power by buffette ~ Version : <?= $string_version() ?> ~ Dernière mise à jour : <?= $conf['version']['update'] ?></span>
    </div>
</footer>

<script src="./node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="./node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="./node_modules/@popperjs/core/dist/umd/popper.min.js"></script>
<script src="./node_modules/jquery/dist/jquery.min.js"></script>
<script src="./js/javascript.js"></script>
</body>
</html>
