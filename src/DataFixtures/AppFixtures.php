<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Area;
use App\Entity\CardOrder;
use App\Entity\Instruction;
use App\Entity\Privilege;
use App\Entity\PrivilegeAssignment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $users = $this->addUsers($manager, 10);
        $this->setCardIds($users);
        $this->addCardOrders($manager, $users);

        $this->addAdminUser($manager);

        $privileges = $this->addAreasWithPrivileges($manager);

        $this->assignPrivilege($users, $privileges['workshopInstructions'], $manager, [0, 1, 2, 8, 9]);
        $this->assignPrivilege($users, $privileges['printerUsage'], $manager, [0, 1, 2]);
        $this->assignPrivilege($users, $privileges['multiColorUsage'], $manager, [0, 1]);
        $this->assignPrivilege($users, $privileges['canCut'], $manager, [8, 9]);

        $burned = new User('burned42', '', '');
        $manager->persist($burned);
        $this->assignPrivilege([$burned], $privileges['workshopInstructions'], $manager, [0]);
        $this->assignPrivilege([$burned], $privileges['printerUsage'], $manager, [0]);
        $this->assignPrivilege([$burned], $privileges['canCut'], $manager, [0]);

        $manager->flush();
    }

    public function addAdminUser(ObjectManager $manager): void
    {
        $admin = new User('the.admin', 'The Admin', 'admin@example.com')
            ->setRoles(['USER', 'ADMIN']);
        $manager->persist($admin);
    }

    /**
     * @return User[]
     */
    public function addUsers(ObjectManager $manager, int $count): array
    {
        /** @var User[] $users */
        $users = [];
        for ($i = 0; $i < $count; ++$i) {
            $users[$i] = new User('foo.bar'.$i, 'Foo Bar '.$i, 'foo.bar'.$i.'@example.com')
                ->setRoles(['USER']);
            $manager->persist($users[$i]);
        }

        return $users;
    }

    /**
     * @param User[] $users
     */
    public function addCardOrders(ObjectManager $manager, array $users): void
    {
        $cardOrder = new CardOrder($users[5]);
        $manager->persist($cardOrder);

        $cardOrder = new CardOrder($users[6]);
        $cardOrder->setPrintOrdered();
        $manager->persist($cardOrder);

        $cardOrder = new CardOrder($users[7]);
        $cardOrder->setPrintOrdered();
        $cardOrder->setCardId('AA:BB:CC:DD:07');
        $manager->persist($cardOrder);
    }

    /**
     * @param User[] $users
     */
    public function setCardIds(array $users): void
    {
        for ($i = 0; $i < 5; ++$i) {
            $users[$i]->setCardId('AA:BB:CC:DD:0'.$i);
        }
    }

    /**
     * @param User[] $users
     * @param int[]  $keys
     */
    public function assignPrivilege(
        array $users,
        Privilege $privilege,
        ObjectManager $manager,
        array $keys,
    ): void {
        foreach ($keys as $i) {
            $privilegeAssignment = new PrivilegeAssignment()
                ->setUser($users[$i])
                ->setPrivilege($privilege);
            $manager->persist($privilegeAssignment);
        }
    }

    /**
     * @return Privilege[]
     */
    public function addAreasWithPrivileges(ObjectManager $manager): array
    {
        $privileges = [];

        $fablab = new Area()->setName('FabLab');
        $manager->persist($fablab);

        $privileges['workshopInstructions'] = new Privilege()
            ->setArea($fablab)
            ->setName('Werkstatt-Einweisung')
            ->setDescription('Allgemeine Werkstattanweisung gelesen und verstanden');
        $manager->persist($privileges['workshopInstructions']);

        $printer3D = new Area()->setName('3D-Drucker');
        $manager->persist($printer3D);

        $privileges['printerUsage'] = new Privilege()
            ->setArea($printer3D)
            ->setName('Druck selbstständig starten')
            ->setDescription('Druck darf ohne Rücksprache mit einem Betreuer gestartet werden');
        $manager->persist($privileges['printerUsage']);

        $privileges['multiColorUsage'] = new Privilege()
            ->setArea($printer3D)
            ->setName('Multi-Color Fachwissen vorhanden')
            ->setDescription('Kenntnis über Umgang mit Multi-Color vorhanden');
        $manager->persist($privileges['multiColorUsage']);

        $lasercutter = new Area()->setName('Lasercutter');
        $manager->persist($lasercutter);

        $privileges['canCut'] = new Privilege()
            ->setArea($lasercutter)
            ->setName('Verwendung Lasercutter')
            ->setDescription('Darf den Lasercutter verwenden');
        $manager->persist($privileges['canCut']);

        $printer3DWorkshop = new Instruction()
            ->setName('3D-Drucker Einsteiger-Workshop')
            ->setPrivileges(new ArrayCollection([$privileges['printerUsage']]));
        $manager->persist($printer3DWorkshop);

        $printer3DWorkshopAdvanced = new Instruction()
            ->setName('3D-Drucker Fortgeschrittenen-Workshop')
            ->setPrivileges(new ArrayCollection([$privileges['multiColorUsage'], $privileges['printerUsage']]));
        $manager->persist($printer3DWorkshopAdvanced);

        return $privileges;
    }
}
