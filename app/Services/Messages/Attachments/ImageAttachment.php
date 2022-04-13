<?php

namespace App\Services\Messages\Attachments;

use App\Services\Messages\Contracts\Attatchments\ImageAttachmentInterface;

class ImageAttachment implements ImageAttachmentInterface
{
    /** @var object */
    protected $image;

    /**
     * Create a new image embed.
     *
     * @param object $image
     */
    public function __construct($image)
    {
        $this->image = $image;
    }

    /** @inheritDoc */
    public function getUrl()
    {
        return $this->image->url;
    }

    /** @inheritDoc */
    public function getWidth()
    {
        return $this->image->width;
    }

    /** @inheritDoc */
    public function getHeight()
    {
        return $this->image->height;
    }
}
