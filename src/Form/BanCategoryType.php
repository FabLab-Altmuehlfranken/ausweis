<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\MachineCategory;
use App\Repository\MachineCategoryRepository;
use Doctrine\ORM\QueryBuilder;
use Override;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * @template-extends AbstractType<array{category: ?MachineCategory, reason: ?string}>
 */
class BanCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category', EntityType::class, [
                'label' => 'Gerätekategorie',
                'priority' => 1,
                'class' => MachineCategory::class,
                'choice_label' => static fn (MachineCategory $category): string => $category->name,
                'constraints' => [new NotNull()],
                'query_builder' => static fn (MachineCategoryRepository $repository): QueryBuilder => $repository->createQueryBuilder('c')
                    ->orderBy('c.name'),
            ])
        ;
    }

    #[Override]
    public function getParent(): string
    {
        return ReasonType::class;
    }
}
