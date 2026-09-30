<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Area;
use App\Entity\AreaBan;
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
        $areas = $this->addAreas($manager);

        $users = $this->addUsers($manager, 10);
        $this->setCardIds($users);
        $this->addCardOrders($manager, $users);

        $admin = $this->addAdminUser($manager);
        $this->addBadBoy($manager, $areas, $admin);

        $privileges = $this->addAreasWithPrivileges($manager, $areas);
        $instructions = $this->addInstructions($manager, $privileges);

        $this->assignPrivilege($users, $privileges['workshopInstructions'], $manager, [0, 1, 2, 8, 9], $admin);
        $this->assignPrivilege($users, $privileges['printerUsage'], $manager, [0, 1, 2], $admin, $instructions['3d']);
        $this->assignPrivilege($users, $privileges['multiColorUsage'], $manager, [0, 1], $admin, $instructions['3d_advanced']);
        $this->assignPrivilege($users, $privileges['canCut'], $manager, [8, 9], $admin);

        $burned = new User('burned42', '', '');
        $manager->persist($burned);
        $this->assignPrivilege([$burned], $privileges['workshopInstructions'], $manager, [0], $admin);
        $this->assignPrivilege([$burned], $privileges['printerUsage'], $manager, [0], $admin);
        $this->assignPrivilege([$burned], $privileges['canCut'], $manager, [0], $admin);

        $manager->flush();
    }

    public function addAdminUser(ObjectManager $manager): User
    {
        $admin = new User('the.admin', 'The Admin', 'admin@example.com')
            ->setRoles(['USER', 'ADMIN']);
        $manager->persist($admin);

        return $admin;
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
        $cardOrder->setCardId('AA:BB:CC:07');
        $manager->persist($cardOrder);

        $cardOrder = new CardOrder($users[8]);
        $cardOrder->setPrintOrdered();
        $cardOrder->setCardId('AA:BB:CC:DD:EE:FF:08');
        $manager->persist($cardOrder);
    }

    /**
     * @param User[] $users
     */
    public function setCardIds(array $users): void
    {
        for ($i = 0; $i < 5; ++$i) {
            $users[$i]->setCardId('AA:BB:CC:0'.$i);
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
        User $assignedBy,
        ?Instruction $instruction = null,
    ): void {
        foreach ($keys as $i) {
            $privilegeAssignment = new PrivilegeAssignment()
                ->setUser($users[$i])
                ->setPrivilege($privilege)
                ->setAssignedBy($assignedBy)
                ->setInstruction($instruction);
            $manager->persist($privilegeAssignment);
        }
    }

    /**
     * @return Area[]
     */
    public function addAreas(ObjectManager $manager): array
    {
        $areas = [];

        $areas['fablab'] = new Area()
            ->setName('FabLab');
        $manager->persist($areas['fablab']);

        $areas['printer3D'] = new Area()
            ->setName('3D-Drucker');
        $manager->persist($areas['printer3D']);

        $areas['lasercutter'] = new Area()
            ->setName('Lasercutter');
        $manager->persist($areas['lasercutter']);

        return $areas;
    }

    /**
     * @param Area[] $areas
     *
     * @return Privilege[]
     */
    public function addAreasWithPrivileges(ObjectManager $manager, array $areas): array
    {
        $privileges = [];

        $privileges['workshopInstructions'] = new Privilege()
            ->setArea($areas['fablab'])
            ->setName('Werkstatt-Einweisung')
            ->setDescription('Allgemeine Werkstattanweisung gelesen und verstanden');
        $manager->persist($privileges['workshopInstructions']);

        $privileges['printerUsage'] = new Privilege()
            ->setArea($areas['printer3D'])
            ->setName('Druck selbstständig starten')
            ->setDescription('Druck darf ohne Rücksprache mit einem Betreuer gestartet werden');
        $manager->persist($privileges['printerUsage']);

        $privileges['multiColorUsage'] = new Privilege()
            ->setArea($areas['printer3D'])
            ->setName('Multi-Color Fachwissen vorhanden')
            ->setDescription('Kenntnis über Umgang mit Multi-Color vorhanden');
        $manager->persist($privileges['multiColorUsage']);

        $privileges['canCut'] = new Privilege()
            ->setArea($areas['lasercutter'])
            ->setName('Verwendung Lasercutter')
            ->setDescription('Darf den Lasercutter verwenden');
        $manager->persist($privileges['canCut']);

        return $privileges;
    }

    /**
     * @param Privilege[] $privileges
     *
     * @return Instruction[]
     */
    public function addInstructions(ObjectManager $manager, array $privileges): array
    {
        $printer3DWorkshop = new Instruction()
            ->setName('3D-Drucker Einsteiger-Workshop')
            ->setPrivileges(new ArrayCollection([$privileges['printerUsage']]));
        $manager->persist($printer3DWorkshop);

        $printer3DWorkshopAdvanced = new Instruction()
            ->setName('3D-Drucker Fortgeschrittenen-Workshop')
            ->setPrivileges(new ArrayCollection([$privileges['multiColorUsage'], $privileges['printerUsage']]));
        $manager->persist($printer3DWorkshopAdvanced);

        return [
            '3d' => $printer3DWorkshop,
            '3d_advanced' => $printer3DWorkshopAdvanced,
        ];
    }

    /**
     * @param Area[] $areas
     */
    private function addBadBoy(ObjectManager $manager, array $areas, User $bannedBy): void
    {
        $badBoy = new User('bad.boy', 'Bad Boy', 'bad.boy@example.com')
            ->setRoles(['USER']);
        $manager->persist($badBoy);

        foreach ($areas as $area) {
            new AreaBan()
                ->setUser($badBoy)
                ->setBannedBy($bannedBy)
                ->setArea($area)
                |> $manager->persist(...);
        }
    }
}
