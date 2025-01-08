<?php

namespace Taddy\Sdk\Dto;

final class ShowCreativeResponse extends AbstractDto {

    public function __construct(
        public Creative $creative,
        public string $link,
    ) {}

}