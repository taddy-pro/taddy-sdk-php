<?php

namespace Taddy\Sdk\Dto;

final class ExchangeFeedItem extends AbstractDto {

    public function __construct(
        public int|string $id,
        public string $title,
        public string $description,
        public string $image,
        public ResourceType $type,
        public string $link,
    ) {}

}