<?php

namespace App\Domain\Orders\Services;

class ImageService
{
    private $base64Header = 'data:image/jpeg;base64,';

    public function cropImageFromBase64(string $base64Image): string
    {
        if (!function_exists('imagecreatefromstring') ||
            !function_exists('imagejpeg') ||
            !function_exists('imagedestroy') ||
            !function_exists('imagesx') ||
            !function_exists('imagesy') ||
            !function_exists('imagecreatetruecolor') ||
            !function_exists('imagecopyresampled')) {
            return $base64Image;
        }

        $imageData = $base64Image;
        $isWithHeader = strpos($imageData, $this->base64Header);
        if ($isWithHeader !== false) {
            $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $base64Image);
        }

        $imageData = base64_decode($imageData);

        $sourceImage = imagecreatefromstring($imageData);
        if ($sourceImage === false) {
            throw new \Exception('Create file error');
        }

        $width = imagesx($sourceImage);
        $height = imagesy($sourceImage);

        $newHeight = $height / 2;

        $croppedImage = imagecreatetruecolor($width, $newHeight);

        imagecopyresampled($croppedImage, $sourceImage, 0, 0, 0, 0, $width, $newHeight, $width, $newHeight);

        ob_start();
        imagejpeg($croppedImage);
        $croppedImageData = ob_get_contents();
        ob_end_clean();

        imagedestroy($sourceImage);
        imagedestroy($croppedImage);

        return ($isWithHeader ? $this->base64Header : '') . base64_encode($croppedImageData);
    }

    public function cropImageBase64AndSaveToFile(string $base64Image, string $outputFile): void
    {
        if (!function_exists('imagecreatefromstring') ||
            !function_exists('imagejpeg') ||
            !function_exists('imagedestroy') ||
            !function_exists('imagesx') ||
            !function_exists('imagesy') ||
            !function_exists('imagecreatetruecolor') ||
            !function_exists('imagecopyresampled')) {
            $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $base64Image);
            $binaryData = base64_decode($imageData);
            file_put_contents($outputFile, $binaryData);
            return;
        }

        $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $base64Image);
        $imageData = base64_decode($imageData);

        $sourceImage = imagecreatefromstring($imageData);
        if ($sourceImage === false) {
            throw new \Exception('Create image error');
        }

        $width = imagesx($sourceImage);
        $height = imagesy($sourceImage);

        $newHeight = $height / 2;

        $croppedImage = imagecreatetruecolor($width, $newHeight);

        imagecopyresampled($croppedImage, $sourceImage, 0, 0, 0, 0, $width, $newHeight, $width, $newHeight);

        imagejpeg($croppedImage, $outputFile);

        imagedestroy($sourceImage);
        imagedestroy($croppedImage);
    }
}
