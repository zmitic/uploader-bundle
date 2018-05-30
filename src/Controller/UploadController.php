<?php

namespace WJB\UploaderBundle\Controller;

use Imagine\Image\Box;
use Liip\ImagineBundle\Templating\FilterExtension;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WJB\UploaderBundle\Service\FileUploader;
use function in_array;

class UploadController extends AbstractController
{
    public function upload(Request $request, FileUploader $fileUploader, FilterExtension $filterExtension)
    {
        $filterName = $request->attributes->get('filter_name');
        $allowedMimeTypes = $request->get('allowed_mime_types', []);

        $files = $request->files;

        /** @var UploadedFile $uploadedFile */
        if (!$uploadedFile = $files->get('file')) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }
        $mime = $uploadedFile->getMimeType();

        if ($allowedMimeTypes && !in_array($mime, $allowedMimeTypes, false)) {
            return new JsonResponse(['error' => 'Mime not allowed'], 400);
        }

        $content = file_get_contents($uploadedFile->getRealPath());
        $this->processContent($content, $mime);
        $info = $fileUploader->saveContentToTmp($content, $filterName);

        return new JsonResponse($info);
    }

    private function processContent(&$content, $mime): void
    {
        if (0 !== strpos($mime, 'image/')) {
            return;
        }
        $imagine = new \Imagine\Imagick\Imagine();

        $image = $imagine->load($content);
        $content = $image->thumbnail(new Box(2048, 2048))
            ->get('jpeg');
    }
}
