<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Privilege;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template-extends AbstractType<Privilege>
 */
class PrivilegeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Beschreibung',
                'required' => false,
            ])
            ->add('validityMonths', IntegerType::class, [
                'label' => 'Gültigkeit in Monaten',
                'help' => 'Leer lassen, wenn die Berechtigung nicht abläuft. Jede erneute Einweisung verlängert die Gültigkeit.',
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Speichern',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Privilege::class,
        ]);
    }
}
