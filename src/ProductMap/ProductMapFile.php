<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\ProductMap;

final class ProductMapFile implements ProductMapInterface
{
    /**
     * @var \SplFileInfo
     */
    private $file;

    public function __construct(\SplFileInfo $file)
    {
        $this->file = $file;
    }

    public function current()
    {
        // TODO: Implement current() method.
    }

    public function next()
    {
        // TODO: Implement next() method.
    }

    public function key()
    {
        // TODO: Implement key() method.
    }

    public function valid()
    {
        // TODO: Implement valid() method.
    }

    public function rewind()
    {
        // TODO: Implement rewind() method.
    }

}
