<?php


namespace App\Service;


use App\Entity\Image;

class Thumbnail
{
    /**
     * Resize in 16X9
     *
     * @param Image $image
     */
    public function resize(Image $image)
    {
        $fullPath = $image->getPath() . '/cropped/' . $image->getName();
        $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
        $savingFullPath = $image->getPath() . '/thumbnail/' . $image->getName();
        $newWidth = 500;
        $newHeight = 281.25;

        if ($extension === 'jpeg' || $extension === 'jpg') {
            $originalImg = imagecreatefromjpeg($fullPath);
        } else if ($extension == 'png') {
            $originalImg = imagecreatefrompng($fullPath);
        }

        // Create Thumbnail folder if doesn't exist
        if (!file_exists($image->getPath() . '/thumbnail')) {
            mkdir($image->getPath() . '/thumbnail', 0777, true);
        }

        // Get the current size
        list($width, $height) = getimagesize($fullPath);

        // Resize image is not already below or equal to the target size
        if (!(($width <= $newWidth) && ($height <= $newHeight))) {
            $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
            $resizeSuccess = imagecopyresized($thumbnail, $originalImg, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            if ($resizeSuccess !== FALSE) {
                if ($extension === 'jpeg' || $extension === 'jpg') {
                    imagejpeg($thumbnail, $savingFullPath);
                } else if ($extension == 'png') {
                    imagepng($thumbnail, $savingFullPath);
                }
                // Release ram
                imagedestroy($thumbnail);
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