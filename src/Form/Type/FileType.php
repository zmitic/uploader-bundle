<?php

namespace WJB\UploaderBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WJB\UploaderBundle\Form\Transformer\FileTransformer;
use WJB\UploaderBundle\Model\FileInterface;

class FileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('filename', HiddenType::class, [
            'label' => false,
            'attr' => [
                'data-file-widget-filename' => '',
            ],
        ]);

        $builder->add('mime', HiddenType::class, [
            'label' => false,
            'attr' => [
                'data-file-widget-mime' => '',
            ],
        ]);

        $builder->addViewTransformer(new FileTransformer($options));
    }

//    public function getParent()
//    {
//        return EntityType::class;
//    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        /** @var FileInterface|null $data */
        $data = $form->getData();

        $filename = $data ? $data->getFilename() : null;
        $view->vars['filename'] = $filename;
        $view->vars['filter_name'] = $options['filter_name'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
            'compound' => true,
            'find_one_by_filename' => function (string $filename): ?FileInterface {
                throw new \InvalidArgumentException(sprintf('You must make finder for filename "%s".', $filename));
            },
        ]);

        $resolver->setRequired([
            'on_create',
            'filter_name',
        ]);
    }
}
