<?php


namespace App\Entity;


class Order
{
    /**
     * @var array
     */
    protected $items;

    public function __construct(array $items) {
        $this->items = $items;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function addItem(Item $item): self
    {
        $this->items[] = $item;
        return $this;
    }
}
