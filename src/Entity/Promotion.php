<?php


namespace App\Entity;


use DateTime;

class Promotion
{
    /**
     * @var int
     */
    protected int $minAmount;

    /**
     * @var int
     */
    protected int $reduction;

    /**
     * @var bool
     */
    protected bool $freeDelivery;

    /**
     * @var int
     */
    protected int $productCount;

    /**
     * @var DateTime|null
     */
    protected ?DateTime $date;

    /**
     * @var int
     */
    protected int $numberOfUse;

    /**
     * @param int $minAmount
     * @param int $reduction
     * @param bool $freeDelivery
     * @param int $productCount
     * @param DateTime|null $date
     * @param int $numberOfUse
     */
    public function __construct(
        int $minAmount,
        int $reduction,
        bool $freeDelivery,
        DateTime|null $date,
        int $numberOfUse,
        int $productCount = 1
    )
    {
        $this->minAmount    = $minAmount;
        $this->reduction    = $reduction;
        $this->freeDelivery = $freeDelivery;
        $this->date         = $date;
        $this->numberOfUse  = $numberOfUse;
        $this->productCount = $productCount;
    }

    public function getMinAMount(): int {
        return $this->minAmount;
    }

    public function getReduction(): int {
        return $this->reduction;
    }

    public function getFreeDelivery(): bool {
        return $this->freeDelivery;
    }

    public function getDate(): ?DateTime {
        return $this->date;
    }

    public function getNumberOfUse(): int {
        return $this->numberOfUse;
    }

    public function getProductCount(): int {
        return $this->productCount;
    }
}
