<?php

namespace App\Services\ImageAdapter;

use Intervention\Image\Image;
use Intervention\Image\Facades\Image as ImageFacade;

class ImageAdapter
{
    public int $supportHeight;
    public int $supportWidth;

    /**
     * @param mixed $data
     *
     * @return \Intervention\Image\Image
     */
    public function make(mixed $data): Image
    {
        return ImageFacade::make($data);
    }

    /**
     * @param \Intervention\Image\Image $image
     * @param int                       $width
     * @param int                       $height
     *
     * @return void
     */
    public function resize(Image $image, int $width, int $height): void
    {
        $heightDifferent = $height - $this->supportHeight;
        $widthDifferent = $width - $this->supportWidth;

        if ($heightDifferent > $widthDifferent && $heightDifferent > 0) {
            $percent = $heightDifferent / $height * 100;
            $width = (int)($width - $width * $percent / 100);
            $height = $this->supportHeight;
        }

        if ($widthDifferent > $heightDifferent && $widthDifferent > 0) {
            $percent = $widthDifferent / $width * 100;
            $height = (int)($height - $height * $percent / 100);
            $width = $this->supportWidth;
        }

        $image->resize($width, $height)->crop($this->supportWidth, $this->supportHeight);
    }
}