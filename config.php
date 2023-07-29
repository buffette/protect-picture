<?php
if (PHP_OS === 'Linux') {
    putenv('GDFONTPATH=' . realpath('/usr/share/fonts/truetype/'));
} elseif (PHP_OS === 'WINNT') {
    putenv('GDFONTPATH=C:\WINDOWS\Fonts\\');
} else {
    die('PHP_OS non pris en charge : ' . PHP_OS);
}

const VERSION_ENV_PROD = 1;
const VERSION_ENV_BETA = 2;
const VERSION_ENV_DEV = 3;

include 'FontMeta.class.php';

$conf['version'] = [
    'env' => VERSION_ENV_DEV,
    'number' => [
        'major' => 2,
        'minor' => 0,
        'revision' => 0,
    ],
    'update' => '29/07/2023'
];

//$conf['authorized_mime_type'] = ['image/png', 'image/jpeg', 'application/zip'];
$conf['authorized_mime_type'] = ['image/png', 'image/jpeg'];
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

$string_env = function() use ($conf) {
    switch ($conf['version']['env']) {
        case VERSION_ENV_PROD:
            return [
                'small' => 'prod',
                'long' => 'production'
            ];
        case VERSION_ENV_BETA:
            return [
                'small' => 'beta',
                'long' => 'beta'
            ];
        case VERSION_ENV_DEV:
            return [
                'small' => 'dev',
                'long' => 'développement'
            ];
    }

    return [
        'small' => 'ind',
        'long' => 'indéterminé'
    ];
};

$string_version = function () use ($conf, $string_env) {
    return $conf['version']['number']['major'] . '.' . $conf['version']['number']['minor'] . '.' . $conf['version']['number']['revision'] . ($conf['version']['env'] !== VERSION_ENV_PROD ? '-' .  $string_env()['small'] : '');
};