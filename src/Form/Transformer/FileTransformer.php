<?php

namespace WJB\UploaderBundle\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use WJB\UploaderBundle\Model\FileInterface;

class FileTransformer implements DataTransformerInterface
{
    private $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * @param FileInterface|null $file
     */
    public function transform($file): array
    {
        return [
            'filename' => $file ? $file->getFilename() : '',
            'mime' => $file ? $file->getFilename() : '',
        ];
    }

    public function reverseTransform($array): FileInterface
    {
        if (null === $array) {
            return null;
        }

        if (!\is_array($array)) {
            throw new TransformationFailedException('Expected an array.');
        }

        $filename = $array['filename'];
        $mime = $array['mime'];

        $ifExistsCallback = $this->options['find_one_by_filename'];

        if ($file = $ifExistsCallback($filename)) {
            return $file;
        }

        $onCreateCallback = $this->options['on_create'];

        return $onCreateCallback($filename, $mime);
    }
}
