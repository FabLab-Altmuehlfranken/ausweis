<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Instruction;
use App\Entity\Privilege;
use App\Repository\PrivilegeRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('name', TextType::class, [
                'label' => 'Name',
                'help' => 'z.B. Einweisung Lasercutter',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Beschreibung',
                'required' => false,
            ])
            ->add('privileges', EntityType::class, [
                'label' => 'Vermittelte Berechtigungen',
                'help' => 'Änderungen gelten nur für zukünftig bestätigte Teilnahmen.',
                'class' => Privilege::class,
                'multiple' => true,
                'expanded' => true,
                'by_reference' => false,
                'choice_label' => static fn (Privilege $privilege): string => $privilege->name,
                'group_by' => static fn (Privilege $privilege): string => $privilege->category->name,
                'query_builder' => static fn (PrivilegeRepository $repository): QueryBuilder => $repository->createQueryBuilder('p')
                    ->join('p.category', 'c')
                    ->addSelect('c')
                    ->orderBy('c.name')
                    ->addOrderBy('p.name'),
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Speichern',
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
