<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @template-extends AbstractType<array{reason: ?string}>
 */
class ReasonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reason', TextareaType::class, [
                'label' => 'Begründung',
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Bestätigen',
            ])
        ;
    }
}
