<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class DashboardControllerTest extends WebTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        self::ensureKernelShutdown();
        while (restore_exception_handler()) {
        }
        while (restore_error_handler()) {
        }
    }

    public function testIndex(): void
    {
        $client = static::createClient();
        $em = static::getContainer()->get(EntityManagerInterface::class);

        $metadata = $em->getMetadataFactory()->getAllMetadata();
        if (!empty($metadata)) {
            $schemaTool = new SchemaTool($em);
            $schemaTool->dropSchema($metadata);
            $schemaTool->createSchema($metadata);
        }

        $em->getConnection()->insert('dashboard_read_model', [
            'id' => 1,
            'title' => 'Site 1',
            'visits' => 100,
            'revenue' => 12.5,
            'created_at' => '2026-03-11 12:00:00',
        ]);

        $client->request('GET', '/dashboard');

        self::assertResponseIsSuccessful();
    }
}
