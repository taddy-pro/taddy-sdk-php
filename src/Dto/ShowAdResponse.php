<?php

namespace Taddy\Sdk\Dto;

final class ShowAdResponse extends AbstractDto {

    public function __construct(
        public Ad     $ad,
        public string $link,
    ) {}

}