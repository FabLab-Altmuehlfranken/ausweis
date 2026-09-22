<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * @template-extends AbstractType<array{users: iterable<User>, date: ?DateTimeImmutable}>
 */
class BatchConfirmAttendanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('users', EntityType::class, [
                'label' => 'Teilnehmer',
                'help' => 'Mehrfachauswahl mit Strg/Cmd. Mitglieder erscheinen hier erst nach ihrem ersten Login.',
                'class' => User::class,
                'multiple' => true,
                'choice_label' => static fn (User $user): string => $user->displayName,
                'query_builder' => static fn (UserRepository $repository): QueryBuilder => $repository->createQueryBuilder('u')
                    ->orderBy('u.displayName'),
                'constraints' => [new Count(min: 1)],
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
                'label' => 'Teilnahmen bestätigen',
            ])
        ;
    }
}
