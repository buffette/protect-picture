<?php
if (PHP_OS === 'Linux') {
    putenv('GDFONTPATH=' . realpath('/usr/share/fonts/truetype/'));
} elseif (PHP_OS === 'WINNT') {
    putenv('GDFONTPATH=C:\WINDOWS\Fonts\\');
} else {
    die('PHP_OS non pris en charge : ' . PHP_OS);
}

include 'FontMeta.class.php';

$conf['version'] = 'V1.0.7';
$conf['authorized_mime_type'] = ['image/png', 'image/jpeg', 'image/gif'];
$conf['text']['emplacements'] = [
    'left_top' => 'En haut à gauche',
    'middle_top' => 'Centré en haut',
    'right_top' => 'En haut à droite',
    'left_middle' => 'Au milieu à gauche',
    'middle_middle' => 'Centré au milieu',
    'right_middle' => 'Au milieu à droite',
    'left_bottom' => 'En bas à gauche',
    'middle_bottom' => 'Centré en bas',
    'right_bottom' => 'En bas à droite'
];
$conf['text']['max_size'] = 250;
$conf['text']['border_max_level'] = 20;

function getEnabledFonts() {
    if (PHP_OS === 'Linux') {
        $fonts = [];
        foreach (scandir(getenv('GDFONTPATH')) as $fontType) {
            foreach (scandir(getenv('GDFONTPATH') . '/' . $fontType . '/') as $file) {
                if (substr($file, -4) === '.ttf') {
                    $fontinfo = new FontMeta(getenv('GDFONTPATH') . '/' . $fontType . '/' . $file);
                    $fonts[$fontType . '/' . substr($file, 0, -4)] = $fontinfo->getFontName();
                }
            }
        }
//        foreach (scandir(getenv('GDFONTPATH')) as $file) {
//            if (substr($file, -4) === '.ttf') {
//                $fontinfo = new FontMeta(getenv('GDFONTPATH') . '/' . $file);
//                $fonts[substr($file, 0, -4)] = $fontinfo->getFontName();
//            }
//        }
    } elseif (PHP_OS === 'WINNT') {
        $fonts = [];
        foreach (scandir(getenv('GDFONTPATH')) as $file) {
            if (substr($file, -4) === '.ttf') {
                $fontinfo = new FontMeta(getenv('GDFONTPATH') . '\\' . $file);
                $fonts[substr($file, 0, -4)] = $fontinfo->getFontName();
            }
        }
    } else {
        die('getFont($font) - PHP_OS non pris en charge : ' . PHP_OS);
    }
    asort($fonts);
    return array_unique($fonts);
}

function getFont($font) {
    if (PHP_OS === 'Linux') {
        return $font;
    } elseif (PHP_OS === 'WINNT') {
        return getenv('GDFONTPATH') . $font . '.ttf';
    } else {
        die('getFont($font) - PHP_OS non pris en charge : ' . PHP_OS);
    }
}