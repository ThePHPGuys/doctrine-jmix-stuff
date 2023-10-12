<?php
declare(strict_types=1);

namespace TPG\PMix\Doctrine\Serializer\Hydrator;

use Laminas\Hydrator\Filter\FilterInterface;
use TPG\PMix\Data\View;

final readonly class ViewFilter implements FilterInterface
{
    public function __construct(private View $view)
    {

    }

    public function filter(string $property, ?object $instance = null): bool
    {
        return $this->view->hasProperty($property);
    }

}
