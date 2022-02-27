<?php

namespace App\Entity;

class Product
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var int
     */
    protected $price;

    /**
     * @var Brand
     */
    protected $brand;

    /**
     * @param string $title
     * @param int $price
     * @param Brand $brand
     */
    public function __construct(string $title, int $price, Brand $brand)
    {
        $this->title = $title;
        $this->price = $price;
        $this->brand = $brand;
    }


    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return Brand
     */
    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }
}
