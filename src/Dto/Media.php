<?php

namespace Taddy\Sdk\Dto;

final class Media extends AbstractDto {

    public function __construct(
        public string $id,
        public string $url,
        public string $mime,
    ) {}

}