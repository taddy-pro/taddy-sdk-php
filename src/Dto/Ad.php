<?php

namespace Taddy\Sdk\Dto;

final class Ad extends AbstractDto {

    public function __construct(
        public int    $id,
        public Format $format,
        public array  $data,
        public ?Media $media
    ) {}

}