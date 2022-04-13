<?php

namespace App\Services\Messages\Contracts\Attatchments;

interface ImageAttachmentInterface
{
    /**
     * Get the image url.
     *
     * @return string
     */
    public function getUrl();

    /**
     * Get the image width.
     *
     * @return int
     */
    public function getWidth();

    /**
     * Get the image height.
     *
     * @return int
     */
    public function getHeight();
}
