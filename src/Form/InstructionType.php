<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Instruction;
use App\Entity\Privilege;
use App\Repository\PrivilegeRepository;
use SortDirection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template-extends AbstractType<Instruction>
 */
class InstructionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('privileges', EntityType::class, [
                'class' => Privilege::class,
                'query_builder' => fn (PrivilegeRepository $r) => $r->createQueryBuilder('p')
                    ->join('p.area', 'a')
                    ->orderBy('a.name')
                    ->addOrderBy('p.name'),
                'choice_label' => 'displayName',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Berechtigungen',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Instruction::class,
        ]);
    }
}
