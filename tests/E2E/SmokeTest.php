<?php

declare(strict_types=1);

namespace FreezyBee\DataGridBundle\Tests\E2E;

use FreezyBee\DataGridBundle\Tests\App\BeeGridType;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Panther\PantherTestCase;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class SmokeTest extends PantherTestCase
{
    public function testAjax(): void
    {
        $client = self::createPantherClient();
        $crawler = $client->request('GET', '/');

        self::assertSame('<title>DataGrid</title>', $crawler->filter('title')->html());

        $client->waitFor('tbody tr');

        $expected = '<tbody>';
        $expected .= '<tr role="row" class="odd"><td class="sorting_1">name9</td><td>1.3.2019</td><td>1</td></tr>';
        $expected .= '<tr role="row" class="even"><td class="sorting_1">name3</td><td>1.1.2019</td><td>0</td></tr>';
        $expected .= '<tr role="row" class="odd"><td class="sorting_1">name2</td><td>1.2.2019</td><td>9</td></tr>';
        $expected .= '</tbody>';
        self::assertSame($expected, $crawler->filter('tbody')->html());
    }

    public function testExport(): void
    {
        $query = [
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

        /** @var KernelBrowser $client */
        $client = self::createClient();
        $client->request('GET', '/datagrid/export/csv/' . BeeGridType::class, $query);

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
}
