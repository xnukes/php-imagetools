<?php

require_once './src/imagetools.php';

$imageFilled = (new ImageTools('./test_image.png'))->removeTransparent();

echo '<strong>Image info: test_image.png (transparent)</strong><br>';
dump('<strong>Paleta barev:</strong>', $imageFilled->getColorPalette());
dump($imageFilled->detectColorHEX('0000FF') ? 'Detekována modrá barva !' : 'Není detekována barva !');


function dump(...$var) {
    echo '<pre>';
    foreach ($var as $v) {
        print_r($v);
        echo "\n";
    };
    echo '</pre>';
}
