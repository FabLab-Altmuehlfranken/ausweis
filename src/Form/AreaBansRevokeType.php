<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\AreaBan;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template-extends AbstractType<User>
 */
class AreaBansRevokeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('areaBans', EntityType::class, [
                'class' => AreaBan::class,
                'choices' => $options['choices'],
                'choice_label' => 'area.name',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Bereiche abwählen um Verbot aufzuheben',
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
