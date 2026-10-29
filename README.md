FreezyBee/DataGridBundle
========================

[![Build Status](https://travis-ci.com/FreezyBee/DataGridBundle.svg?branch=master)](https://travis-ci.com/github/FreezyBee/DataGridBundle)
[![Coverage Status](https://coveralls.io/repos/github/FreezyBee/DataGridBundle/badge.svg?branch=master)](https://coveralls.io/github/FreezyBee/DataGridBundle)


Requirements
------------

FreezyBee/DataGridBundle requires PHP 8.5 or higher and Symfony 8.0 or higher

- [DataTables](https://datatables.net)
- [flatpickr](https://flatpickr.js.org)


Installation
------------

```sh
composer require freezy-bee/datagrid-bundle
```

Register the bundle (if you don't use Symfony Flex) in `config/bundles.php`:

```php
return [
    // ...
    FreezyBee\DataGridBundle\FreezyBeeDataGridBundle::class => ['all' => true],
];
```

Import the bundle's routes in `config/routes.yaml`:

```yaml
freezy_bee_data_grid:
    resource: '@FreezyBeeDataGridBundle/config/routes.php'
```


Usage
-----

Create a grid type by implementing `DataGridTypeInterface`:

```php
use FreezyBee\DataGridBundle\Attribute\AsDataGridType;
use FreezyBee\DataGridBundle\DataGridBuilder;
use FreezyBee\DataGridBundle\DataGridTypeInterface;
use FreezyBee\DataGridBundle\DataSource\DoctrineDataSource;

#[AsDataGridType('user-grid')]
class UserGridType implements DataGridTypeInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function buildGrid(DataGridBuilder $builder): void
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u');

        $builder->setDataSource(new DoctrineDataSource($qb, 'u'));

        $builder->addText('name', 'Name')
            ->setFilterable()
            ->setSortable();

        $builder->addDateTime('createdAt', 'Created')
            ->setSortable();
    }
}
```

The `#[AsDataGridType]` attribute gives the grid a stable, URL-safe name used for the AJAX and
export routes. It is optional – without it, the name is derived from the class name
(e.g. `UserGridType` becomes `user-grid-type`). The name must only contain letters, digits, `-` and
`_`, and must be unique across all registered grid types.

The grid type class is a regular autowired service, so you can inject any dependency (entity
manager, security, …) through the constructor.

Render the grid in a controller and template:

```php
public function list(DataGridFactory $dataGridFactory): Response
{
    return $this->render('user/list.html.twig', [
        'grid' => $dataGridFactory->create(UserGridType::class),
    ]);
}
```

```twig
{{ datagrid(grid) }}
```


Translations
------------

All grid chrome (pagination, search, filter placeholders, the boolean column labels, …) is
translated through the `datagrid` domain (`translations/datagrid.*.yaml`). Czech (`cs`) and
English (`en`) are bundled.

To add another locale or override a message, drop a file for the same `datagrid` domain into your
own `translations/` directory, e.g. `translations/datagrid.de.yaml`:

```yaml
grid.search: 'Suchen:'
grid.zeroRecords: 'Keine passenden Einträge gefunden'
```

You only need to provide the keys you want to override – Symfony merges your file with the
bundle's for the rest. See `translations/datagrid.en.yaml` in this repository for the full list of
keys.
