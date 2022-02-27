<?php

namespace App\Entity;

class Brand
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var float
     */
    protected $vat;

    /**
     * @var float
     */
    protected $deliveryCost;

    /**
     * @var int|null
     */
    private $deliveryMaxArticlesCount;

    /**
     * @param string $name
     * @param float $vat
     * @param float $deliveryCost
     * @param int|null $deliveryMaxArticlesCount
     */
    public function __construct(
        string $name,
        float $vat,
        float $deliveryCost,
        int $deliveryMaxArticlesCount = null
    )
    {
        $this->name                     = $name;
        $this->vat                      = $vat;
        $this->deliveryCost             = $deliveryCost;
        $this->deliveryMaxArticlesCount = $deliveryMaxArticlesCount;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return float
     */
    public function getVat(): float
    {
        return $this->vat;
    }

    /**
     * @return float
     */
    public function getDeliveryCost(): float
    {
        return $this->deliveryCost;
    }

    /**
     * @return int|null
     */
    public function getDeliveryMaxArticlesCount(): int|null
    {
        return $this->deliveryMaxArticlesCount;
    }

}
