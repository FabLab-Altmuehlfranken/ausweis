<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\CardOrder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template-extends AbstractType<CardOrder>
 */
class AssignCardIdToOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cardId', TextType::class, [
                'label' => 'Ausweis-ID',
                'attr' => ['autocomplete' => 'off'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'zuweisen',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CardOrder::class,
        ]);
    }
}
