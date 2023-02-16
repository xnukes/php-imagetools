<?php

class ImageTools
{
    /**
     * @var false|GdImage|resource
     */
    private $gdImage;

    /**
     * @var false|string
     */
    private $tempFileName;

    public function __construct(string $imagePath)
    {
        if (!file_exists($imagePath)) {
            throw new ImageToolException('Image file does not exists.');
        }

        $this->tempFileName = tempnam(sys_get_temp_dir(), 'PHP_ImageTools');

        switch ($type = exif_imagetype($imagePath)) {
            case IMAGETYPE_PNG:
                $this->gdImage = imagecreatefrompng($imagePath);
                break;
            case IMAGETYPE_JPEG:
                $this->gdImage = imagecreatefromjpeg($imagePath);
                break;
            case IMAGETYPE_GIF:
                $this->gdImage = imagecreatefromgif($imagePath);
                break;
            case IMAGETYPE_BMP:
                $this->gdImage = imagecreatefrombmp($imagePath);
                break;
            default:
                throw new ImageToolException('Image type has not support. (' . $type . ')');
                break;
        }

        imagepng($this->getImage(), $this->tempFileName); // SAVE IMAGE TO TEMP
    }

    public function removeTransparent()
    {
        imagetruecolortopalette($this->getImage(), true, 255);
        $index = imagecolorclosest($this->getImage(), 0, 0, 0); // GET BLACK COLOR
        imagecolorset($this->getImage(), $index, 255, 255, 255); // SET COLOR TO WHITE
        imagepng($this->getImage(), $this->tempFileName); // SAVE IMAGE TO TEMP
        return $this;
    }

    public function getImage()
    {
        return $this->gdImage;
    }

    public function detectColorHEX(string $hexColor)
    {
        $colors = $this->getColorPalette();
        if (in_array($hexColor, $colors) === true)
            return true;
        return false;
    }

    public function getColorPalette(?int $numberOfColors = null, $level = 5)
    {
        $level = (int)$level;
        $palette = array();
        $size = getimagesize($this->tempFileName);
        for ($i = 0; $i < $size[0]; $i += $level) {
            for ($j = 0; $j < $size[1]; $j += $level) {
                $thisColor = imagecolorat($this->getImage(), $i, $j);
                $rgb = imagecolorsforindex($this->getImage(), $thisColor);
                $color = sprintf('%02X%02X%02X', (round(round(($rgb['red'] / 0x33)) * 0x33)), round(round(($rgb['green'] / 0x33)) * 0x33), round(round(($rgb['blue'] / 0x33)) * 0x33));
                $palette[$color] = isset($palette[$color]) ? ++$palette[$color] : 1;
            }
        }
        arsort($palette);
        if ($numberOfColors) {
            return array_slice(array_keys($palette), 0, $numberOfColors);
        } else {
            return array_keys($palette);
        }
    }

    public function __destruct()
    {
        unlink($this->tempFileName);
    }
}

class ImageToolException extends Exception
{
}
