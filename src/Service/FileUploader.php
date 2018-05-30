<?php

namespace WJB\UploaderBundle\Service;

use Gaufrette\FilesystemInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Ramsey\Uuid\Uuid;
use WJB\UploaderBundle\Model\FileInterface;

class FileUploader
{
    /** @var FilesystemInterface */
    private $fs;

    /** @var CacheManager */
    private $cache;

    public function __construct(FilesystemInterface $uploadsFileSystem, CacheManager $cache)
    {
        $this->fs = $uploadsFileSystem;
        $this->cache = $cache;
    }

    public function saveContentToTmp(?string $content, string $filterName, string $targetFolder = ''): array
    {
        if (!$content) {
            throw new \InvalidArgumentException(sprintf('You did not send any content to be saved'));
        }

        $fileInfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $fileInfo->buffer($content);
        [, $extension] = sscanf($mime, '%[^\/]/%s');

        $filename = sprintf('%s.%s', Uuid::uuid4()->toString(), $extension);
        if ($targetFolder) {
            $targetFolder = trim($targetFolder, '/');
            $filename = $targetFolder.'/'.$filename;
        }
        $filename = 'tmp/'.$filename;
        $this->fs->write($filename, $content, true);

        $previewUrl = $this->cache->getBrowserPath($filename, $filterName);

        return [
            'filename' => $filename,
            'mime' => $mime,
            'preview_url' => $previewUrl,
        ];
    }

    public function moveToPermanentPosition(FileInterface $file): void
    {
        $tmpFilename = $file->getFilename();
        if (0 !== strpos($tmpFilename, 'tmp/')) {
            return;
        }
        if ($this->fs->has($tmpFilename)) {
            $permanentFilename = str_replace('tmp/', '', $tmpFilename);
            $this->fs->rename($tmpFilename, $permanentFilename);
            $file->setFilename($permanentFilename);
        }
    }

    public function removeFile(FileInterface $file): void
    {
        $filename = $file->getFilename();
        $this->cache->remove($filename);
        $this->fs->delete($filename);
    }
}
