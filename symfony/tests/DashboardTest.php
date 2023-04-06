<?php

use ApiPlatform\Metadata\HttpOperation;
use App\Entity\Account;
use App\Entity\AccountAssetReturn;
use App\Entity\Asset;
use App\Entity\User;
use App\Repository\AccountAssetReturnRepository;
use App\Repository\AccountRepository;
use App\Repository\AssetRepository;
use App\State\DashboardProvider;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class DashboardTest extends TestCase
{
    private MockObject $entityManager;
    private MockObject $security;
    private DashboardProvider $dashboardProvider;
    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->security = $this->createMock(Security::class);
        $this->dashboardProvider = new DashboardProvider($this->entityManager, $this->security);
    }

    public function testProvideWithNoUser(): void
    {
        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn(null);

        $this->expectException(NotFoundHttpException::class);
        $this->dashboardProvider->provide(new HttpOperation());
    }

    public function testProvideWithEmptyAccounts(): void
    {
        $user = new User();
        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $accountRepository = $this->createMock(AccountRepository::class);
        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(Account::class)
            ->willReturn($accountRepository);

        $accountRepository->expects($this->once())
            ->method('findBy')
            ->with(['user' => $user])
            ->willReturn([]);

        $result = $this->dashboardProvider->provide(new HttpOperation());

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testProvideWithAccounts(): void
    {
        $user = new User();
        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $accountRepository = $this->createMock(AccountRepository::class);
        $accountAssetReturnRepository = $this->createMock(AccountAssetReturnRepository::class);
        $assetRepository = $this->createMock(AssetRepository::class);

        $this->entityManager->expects($this->atLeastOnce())
            ->method('getRepository')
            ->withConsecutive(
                [Account::class],
                [AccountAssetReturn::class],
                [Account::class],
                [Asset::class]
            )
            ->willReturnOnConsecutiveCalls(
                $accountRepository,
                $accountAssetReturnRepository,
                $accountRepository,
                $assetRepository
            );

        $accountRepository->expects($this->once())
            ->method('findBy')
            ->with(['user' => $user])
            ->willReturn([new Account()]);

        $result = $this->dashboardProvider->provide(new HttpOperation());

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }
}
