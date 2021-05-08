<?php


namespace App\Service;


use App\Entity\Image;
use Symfony\Component\Filesystem\Filesystem;

class ImageManager
{

    /**
     * Delete an image
     *
     * @param Image $image
     */
    public static function deleteImage(Image $image): void
    {
        $fileSystem = new Filesystem();
        // Delete image in folder tricks
        $fileSystem->remove($image->getPath() . '/' . $image->getName());
        // Delete image in folder cropped
        $fileSystem->remove($image->getPath() . '/cropped/' . $image->getName());
        // Delete image in folder thumbnail
        $fileSystem->remove($image->getPath() . '/thumbnail/' . $image->getName());
    }

    /**
     * Upload an image
     *
     * @param Image $image
     */
    public static function saveImage(Image $image): void
    {
        $file = $image->getFile();
        $file->move($image->getPath(), $image->getName());

        self::crop($image);
        self::resize($image);
    }

    /**
     * Crop image
     *
     * @param Image $image
     */
    public static function crop(Image $image): void
    {
        $fullPath = $image->getPath() . '/' . $image->getName();
        $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
        $savingFullPath = $image->getPath() . '/cropped/' . $image->getName();

        if ($extension === 'jpeg' || $extension === 'jpg') {
            $originalImg = imagecreatefromjpeg($fullPath);
        } else if ($extension === 'png') {
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
                } else if ($extension === 'png') {
                    imagepng($croppedImg, $savingFullPath);
                }
                // Release ram
                imagedestroy($croppedImg);
            }
        } else {
            if ($extension === 'jpeg' || $extension === 'jpg') {
                imagejpeg($originalImg, $savingFullPath);
            } else if ($extension === 'png') {
                imagepng($originalImg, $savingFullPath);
            }
        }
        // Release ram
        imagedestroy($originalImg);
    }


    /**
     * Resize in 16X9
     *
     * @param Image $image
     */
    public static function resize(Image $image): void
    {
        $fullPath = $image->getPath() . '/cropped/' . $image->getName();
        $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
        $savingFullPath = $image->getPath() . '/thumbnail/' . $image->getName();
        $newWidth = 500;
        $newHeight = 281.25;

        if ($extension === 'jpeg' || $extension === 'jpg') {
            $originalImg = imagecreatefromjpeg($fullPath);
        } else if ($extension === 'png') {
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
                } else if ($extension === 'png') {
                    imagepng($thumbnail, $savingFullPath);
                }
                // Release ram
                imagedestroy($thumbnail);
            }
        } else {
            if ($extension === 'jpeg' || $extension === 'jpg') {
                imagejpeg($originalImg, $savingFullPath);
            } else if ($extension === 'png') {
                imagepng($originalImg, $savingFullPath);
            }
        }
        // Release ram
        imagedestroy($originalImg);
    }
}