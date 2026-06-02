<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CardOrder;
use App\Entity\User;
use App\Repository\CardOrderRepository;
use App\Service\UserDetailsQrCodeGenerator;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use ZipArchive;

#[Route('/card_orders/export', name: 'export_card_orders', methods: ['POST'])]
#[IsGranted(User::ADMIN_ROLE)]
final class ExportCardOrdersController extends AbstractController
{
    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly CardOrderRepository $repository,
        private readonly UserDetailsQrCodeGenerator $qrCodeGenerator,
        private readonly EntityManagerInterface $entityManager,
        private readonly MailerInterface $mailer,
    ) {
    }

    public function __invoke(): RedirectResponse
    {
        $orders = $this->repository->findBy(['isPrintOrdered' => false]);

        $zipFilePath = $this->getQrCodesByUsername($orders)
            |> $this->createZipFile(...);
        $this->sendMail($orders, $zipFilePath);
        $this->filesystem->remove($zipFilePath);

        $this->setPrintOrdered($orders);

        $this->addFlash('success', 'Offene Anträge erfolgreich exportiert und per Mail an dich verschickt, bitte zum Druck weitergeben.');

        return $this->redirectToRoute('list_card_orders');
    }

    /**
     * @param CardOrder[] $orders
     *
     * @return array<string, string>
     */
    private function getQrCodesByUsername(array $orders): array
    {
        $qrCodes = [];
        foreach ($orders as $order) {
            $username = $order->user->displayName;
            $digitalCardId = $order->user->digitalCardId;
            $qrCodes[$username] = $this->qrCodeGenerator->generate($digitalCardId)
                ->getString();
        }

        return $qrCodes;
    }

    /**
     * @param array<string, string> $qrCodesByUsername
     */
    private function createZipFile(array $qrCodesByUsername): string
    {
        $zip = new ZipArchive();
        $zipFilePath = $this->filesystem->tempnam(
            sys_get_temp_dir(),
            'ausweis-export',
        );

        $zip->open(
            $zipFilePath,
            ZipArchive::CREATE | ZipArchive::OVERWRITE,
        ) ?: throw new RuntimeException('could not open zip file: '.$zipFilePath);

        $csvData = [];
        foreach ($qrCodesByUsername as $username => $qrCode) {
            $zip->addFromString($username.'.svg', $qrCode)
                ?: throw new RuntimeException('could not add qr code to zip file: '.$zipFilePath);
            $csvData[] = $username;
        }

        $zip->addFromString(
            'ausweis-bestellungen.csv',
            implode(PHP_EOL, $csvData),
        ) ?: throw new RuntimeException('could not add csv file to zip file: '.$zipFilePath);

        $zip->close()
            ?: throw new RuntimeException('could not close zip file: '.$zipFilePath);

        return $zipFilePath;
    }

    /**
     * @param CardOrder[] $orders
     */
    private function setPrintOrdered(array $orders): void
    {
        array_map(
            static fn (CardOrder $order) => $order->setPrintOrdered(),
            $orders,
        );

        $this->entityManager->flush();
    }

    /**
     * @param CardOrder[] $orders
     */
    private function sendMail(
        array $orders,
        string $zipFilePath,
    ): void {
        $user = $this->getUser();
        assert($user instanceof User);

        $this->mailer->send(
            new TemplatedEmail()
                ->to($user->mail)
                ->subject('[FabLab] Export Ausweisanträge')
                ->textTemplate('mail/card_orders_export.txt.twig')
                ->context(['count' => count($orders)])
                ->attachFromPath($zipFilePath, 'fablab-ausweis-bestellungen.zip')
        );
    }
}
