<?php


namespace App\Service;


use App\Entity\Image;

class Cropper
{
    /**
     * Crop image
     *
     * @param Image $image
     */
    public function crop(Image $image)
    {
        $fullPath = $image->getPath() . '/' . $image->getName();
        $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
        $savingFullPath = $image->getPath() . '/cropped/' . $image->getName();

        if ($extension === 'jpeg' || $extension === 'jpg') {
            $originalImg = imagecreatefromjpeg($fullPath);
        } else if ($extension == 'png') {
            $originalImg = imagecreatefrompng($fullPath);
        }

        $ratio16x9 = 16 / 9;
        $ratio = imagesx($originalImg) / imagesy($originalImg);

        // Create cropped folder if doesn't exist
        if (!file_exists($image->getPath() . '/cropped')) {
            mkdir($image->getPath() . '/cropped', 0777, true);
        }

        // Crop if the image is not in 16x9, if it is already, we just copy it in the 'cropped' folder
        if (!(round($ratio, 2) === round($ratio16x9, 2))) {
            if ($ratio < $ratio16x9) {
                $width = imagesx($originalImg);
                $height = (imagesx($originalImg) / 16) * 9;
            } else if ($ratio > $ratio16x9) {
                $width = (imagesy($originalImg) / 9) * 16;
                $height = imagesy($originalImg);
            }

            $croppedImg = imagecrop($originalImg, ['x' => 0, 'y' => 0, 'width' => $width, 'height' => $height]);

            if ($croppedImg !== FALSE) {
                if ($extension === 'jpeg' || $extension === 'jpg') {
                    imagejpeg($croppedImg, $savingFullPath);
                } else if ($extension == 'png') {
                    imagepng($croppedImg, $savingFullPath);
                }
                // Release ram
                imagedestroy($croppedImg);
            }
        } else {
            if ($extension === 'jpeg' || $extension === 'jpg') {
                imagejpeg($originalImg, $savingFullPath);
            } else if ($extension == 'png') {
                imagepng($originalImg, $savingFullPath);
            }
        }
        // Release ram
        imagedestroy($originalImg);
    }
}