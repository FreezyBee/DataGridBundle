<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Tests\E2E;

use FreezyBee\DataGridBundle\Tests\App\BeeGridType;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class SmokeTest extends WebTestCase
{
    public function testAjax(): void
    {
        $client = self::createClient();

        $client->request('GET', '/datagrid/ajax/' . BeeGridType::class, self::getQuery());
        self::assertResponseIsSuccessful();

        $content = (string) $client->getResponse()->getContent();
        $data = json_decode($content, true);

        $expected = [
            'draw' => 1,
            'recordsTotal' => 3,
            'recordsFiltered' => 3,
            'data' => [
                [
                    'name9',
                    '1.3.2019',
                    '1',
                ],
                [
                    'name3',
                    '1.1.2019',
                    '0',
                ],
                [
                    'name2',
                    '1.2.2019',
                    '9',
                ],
            ],
        ];

        self::assertSame($expected, $data);
    }

    public function testExport(): void
    {
        /** @var KernelBrowser $client */
        $client = self::createClient();
        $client->request('GET', '/datagrid/export/csv/' . BeeGridType::class, self::getQuery());

        $expected = "A;B;D\n";
        $expected .= "name9;1.3.2019;Yes\n";
        $expected .= "name3;1.1.2019;No\n";
        $expected .= 'name2;1.2.2019;Yes';

        /** @var Response $response */
        $response = $client->getResponse();
        self::assertStringContainsString('text/csv', self::getHeader($response, 'Content-Type'));
        self::assertStringContainsString(
            'attachment; filename="export.csv"',
            self::getHeader($response, 'Content-Disposition')
        );
        self::assertStringContainsString($expected, $response->getContent() ?: '');
    }

    private static function getHeader(Response $response, string $name): string
    {
        $header = $response->headers->get($name);

        if (is_string($header)) {
            return $header;
        }

        // phpstan hack
        self::assertIsString($header);
        return '';
    }

    /**
     * @return array<string, mixed>
     */
    private static function getQuery(): array
    {
        return [
            'draw' => '1',
            'columns' => [
                [
                    'data' => '0',
                    'name' => 'a',
                    'searchable' => 'true',
                    'orderable' => 'false',
                    'search' => [
                        'value' => '',
                        'regex' => 'false',
                    ],
                ],
                [
                    'data' => '1',
                    'name' => 'b',
                    'searchable' => 'true',
                    'orderable' => 'false',
                    'search' => [
                        'value' => '',
                        'regex' => 'false',
                    ],
                ],
                [
                    'data' => '2',
                    'name' => 'c',
                    'searchable' => 'true',
                    'orderable' => 'false',
                    'search' => [
                        'value' => '',
                        'regex' => 'false',
                    ],
                ],
            ],
            'order' => [
                [
                    'column' => '0',
                    'dir' => 'desc',
                ],
            ],
            'start' => '0',
            'length' => '10',
            'search' => [
                'value' => '',
                'regex' => 'false',
            ],
        ];
    }
}
