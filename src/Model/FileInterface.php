<?php

namespace WJB\UploaderBundle\Model;

interface FileInterface
{
    public function getFilename(): string;

    public function setFilename(?string $filename): void;

    public function getMime(): ?string;

    public function setMime(?string $mime): void;
}
