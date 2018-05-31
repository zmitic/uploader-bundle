<?php

namespace WJB\UploaderBundle\Twig;

use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\Form\FormView;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ButtonAttributesExtension extends AbstractExtension
{
    /** @var TwigEngine */
    private $twig;

    public function __construct(TwigEngine $twig)
    {
        $this->twig = $twig;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('button_attributes', [$this, 'component'], ['is_safe' => ['html']]),
        ];
    }

    public function component(FormView $form): string
    {
        return $this->twig->render('@WJBUploader/upload_button_attributes.html.twig', [
            'form' => $form,
        ]);
    }
}
