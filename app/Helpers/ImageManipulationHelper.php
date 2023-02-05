<?php

namespace App\Helpers;

use Imagick;
use ImagickDraw;
use ImagickPixel;

class ImageManipulationHelper
{
    public function saveTextToImage($text)
    {
        /* Create some objects */
        $image = new Imagick();
        $draw = new ImagickDraw();
        $pixel = new ImagickPixel('white');

        /* New image */
        $image->newImage(1280, 720, $pixel);


        /* Black text */
        $draw->setFillColor('black');

        /* Font properties */
        $draw->setFontSize(30);
        $draw->setGravity(Imagick::GRAVITY_CENTER);

        /* Create text */
        $draw->setFontSize(40);
        list($lines, $lineHeight) = $this->wordWrapAnnotation($image, $draw, $text, 1280);
        for ($i = 0; $i < count($lines); $i++) {
            $image->annotateImage($draw, 0, 0 + $i*$lineHeight, 0, $lines[$i]);
        }
        //$image->annotateImage($draw, 0, 0, 0, $text);

        /* Give image a format */
        $image->setImageFormat('jpg');
        //$image->writeImage('result.png');
        return $image;
        //$file = Storage::disk('ppt_files')->put('test.jpg', $image);
        //return Storage::disk('ppt_files')->url('test.jpg');
    }

    public function wordWrapAnnotation($image, $draw, $text, $maxWidth)
    {
        $text = trim($text);

        $words = preg_split('%\s%', $text, -1, PREG_SPLIT_NO_EMPTY);
        $lines = array();
        $i = 0;
        $lineHeight = 0;

        while (count($words) > 0) {
            $metrics = $image->queryFontMetrics($draw, implode(' ', array_slice($words, 0, ++$i)));
            $lineHeight = max($metrics['textHeight'], $lineHeight);

            // check if we have found the word that exceeds the line width
            if ($metrics['textWidth'] > $maxWidth or count($words) < $i) {
                // handle case where a single word is longer than the allowed line width (just add this as a word on its own line?)
                if ($i == 1) {
                    $i++;
                }

                $lines[] = implode(' ', array_slice($words, 0, --$i));
                $words = array_slice($words, $i);
                $i = 0;
            }
        }

        return array($lines, $lineHeight);
    }

    public function imagesToPdf($images, $filename)
    {
        $image = new Imagick($images);
        $image->setImageFormat('pdf');
        $image->writeImages(storage_path($filename), true);

        return true;
    }
}
