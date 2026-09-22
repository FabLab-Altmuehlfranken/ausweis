<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Instruction;
use App\Repository\InstructionRepository;
use DateTimeImmutable;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * @template-extends AbstractType<array{instruction: ?Instruction, date: ?DateTimeImmutable}>
 */
class ConfirmAttendanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('instruction', EntityType::class, [
                'label' => 'Einweisung',
                'class' => Instruction::class,
                'choice_label' => static fn (Instruction $instruction): string => $instruction->name,
                'query_builder' => static fn (InstructionRepository $repository): QueryBuilder => $repository->createQueryBuilder('i')
                    ->orderBy('i.name'),
                'constraints' => [new NotNull()],
            ])
            ->add('date', DateType::class, [
                'label' => 'Datum',
                'input' => 'datetime_immutable',
                'data' => new DateTimeImmutable('today'),
                'constraints' => [
                    new NotNull(),
                    new LessThanOrEqual('today'),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Teilnahme bestätigen',
            ])
        ;
    }
}
