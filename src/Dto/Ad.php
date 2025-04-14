<?php

namespace Taddy\Sdk\Dto;

final class Ad extends AbstractDto {

    public function __construct(
        public string  $id,
        public ?string $title,
        public ?string $description,
        public ?string $image,
        public ?string $video,
        public ?string $icon,
        public ?string $text,
        public ?string $button,
        public string $link,
    ) {}

}