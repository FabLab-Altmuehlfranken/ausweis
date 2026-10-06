<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Area;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template-extends AbstractType<AreaBansCreateDTO>
 */
class AreaBansCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('areas', EntityType::class, [
                'class' => Area::class,
                'choices' => $options['choices'],
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Verbotene Bereiche',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AreaBansCreateDTO::class,
            'choices' => [],
        ]);
    }
}
