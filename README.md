# php-imagetools

Image tools for detect color.

```
$image = new ImageTools('./test_image.png');

$pallete = $image->getColorPalette();

$hasBlueColor = $image->detectColorHEX('0000FF');
```
