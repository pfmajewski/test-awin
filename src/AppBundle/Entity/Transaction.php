<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Transaction
 * @ORM\Entity(repositoryClass="AppBundle\EntityRepository\TransactionRepository")
 * @ORM\Table(name="transactions")
 */
class Transaction
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Merchant
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Merchant", inversedBy="transactions")
     */
    private $merchant = null;

    /**
     * @var \DateTime
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @var Currency
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Currency")
     */
    private $currency;

    /**
     * @var string
     * @ORM\Column(type="decimal", precision=11, scale=2)
     */
    private $value = '0.0';

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Merchant
     */
    public function getMerchant(): ?Merchant
    {
        return $this->merchant;
    }

    /**
     * @param Merchant $merchant
     *
     * @return Transaction
     */
    public function setMerchant(?Merchant $merchant): Transaction
    {
        $this->merchant = $merchant;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     *
     * @return Transaction
     */
    public function setDate(\DateTime $date): Transaction
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @param Currency $currency
     *
     * @return Transaction
     */
    public function setCurrency(Currency $currency): Transaction
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return Transaction
     */
    public function setValue(string $value): Transaction
    {
        $this->value = $value;
        return $this;
    }
}
