<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\PrivilegeAssignment;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template-extends AbstractType<AssignPrivilegesDTO>
 */
class PrivilegesRevokeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('privilegeAssignments', EntityType::class, [
                'class' => PrivilegeAssignment::class,
                'choices' => $options['choices'],
                'choice_label' => fn (PrivilegeAssignment $a): string => $a->privilege->area->name.': '.$a->privilege->name,
                'multiple' => true,
                'expanded' => true,
                'label' => 'Berechtigungen abwählen zum Entziehen',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'choices' => [],
        ]);
    }
}
