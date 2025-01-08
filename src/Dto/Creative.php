<?php

namespace Taddy\Sdk\Dto;

final class Creative extends AbstractDto {

    public function __construct(
        public int $id,
        public CreativeFormat $format,
        public array $data,
        public ?Media $media
    ) {}

}