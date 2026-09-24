<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Privilege;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template-extends AbstractType<AssignPrivilegesDTO>
 */
class PrivilegesGrantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('privileges', EntityType::class, [
                'class' => Privilege::class,
                'choices' => $options['choices'],
                'choice_label' => fn (Privilege $p): string => $p->area->name.': '.$p->name,
                'multiple' => true,
                'expanded' => true,
                'label' => 'Neue Berechtigungen',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PrivilegesGrantDTO::class,
            'choices' => [],
        ]);
    }
}
