<?php

namespace WJB\UploaderBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WJB\UploaderBundle\Model\FileInterface;

class UploaderType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['filter_name'] = $options['filter_name'];
        $view->vars['allowed_mime_types'] = $options['allowed_mime_types'];
    }

    public function getParent(): string
    {
        return CollectionType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'allow_add' => true,
            'allow_delete' => true,
            'entry_type' => FileType::class,
            'by_reference' => false,
            'multiple' => true,
            'allowed_mime_types' => null,
            'on_create' => function (string $filename, string $mime): FileInterface {
                throw new \InvalidArgumentException(
                    sprintf('Missing "on_create" callback for filename "%s" and mime "%s".', $filename, $mime)
                );
            },
            'find_one_by_filename' => function (string $filename): ?FileInterface {
                throw new \InvalidArgumentException(
                    sprintf('You must make finder for filename "%s".', $filename)
                );
            },
        ]);

        $resolver->setDefault('entry_options', function (Options $options) {
            return [
                'on_create' => $options['on_create'],
                'find_one_by_filename' => $options['find_one_by_filename'],
            ];
        });

        $resolver->setAllowedTypes('allowed_mime_types', ['null', 'array']);

        $resolver->setRequired([
            'filter_name',
        ]);
    }
}
