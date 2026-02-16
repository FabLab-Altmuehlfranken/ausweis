<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @template-extends AbstractType<null>
 */
class OrderCardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('confirm', CheckboxType::class, [
                'label' => 'Ich bestätige, dass die angezeigten Daten korrekt sind und möchte einen physichen Vereinsausweis beantragen.',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Beantragen',
            ])
        ;
    }
}
