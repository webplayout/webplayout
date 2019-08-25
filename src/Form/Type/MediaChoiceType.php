<?php

declare(strict_types=1);

namespace App\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\ResourceAutocompleteChoiceType;
use Symfony\Component\Form\AbstractType;


use Sylius\Bundle\ResourceBundle\Form\DataTransformer\CollectionToStringTransformer;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\RecursiveTransformer;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Sylius\Component\Registry\ServiceRegistryInterface;
//use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;


class MediaChoiceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!$options['multiple']) {
            $builder->addModelTransformer(
                new ResourceToIdentifierTransformer(
                    $options['repository'],
                    $options['choice_value']
                )
            );
        }

        if ($options['multiple']) {
            $builder
            ->addModelTransformer(new \Symfony\Component\Form\CallbackTransformer(
                function ($values) {

                    $values = $values->map(function ($value) {
                        return $value->getFile();
                    });

                    return $values;
                },
                function ($values) {
                    $ord = 0;
                    $values = $values->map(function ($value) use (&$ord) {
                        ++$ord;
                        $clipFile = new \App\Entity\ClipFile;
                        $clipFile->setFile($value);
                        $clipFile->setOrd($ord);
                        return $clipFile;
                    });

                    return $values;
                }
            ))
            ;
        }
    }


    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return ResourceAutocompleteChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'media_choice';
    }
}
