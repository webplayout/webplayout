<?php

namespace App\Grid\Filter;

use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;

class SingleTableIngeritanceTypeFilter implements FilterInterface
{
    public function apply(DataSourceInterface $dataSource, $name, $data, array $options = []): void
    {
        $dataSource->restrict(sprintf("o INSTANCE OF App\Entity\%s", ucfirst($data['value'])));
    }
}
