<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Merchant
 * @ORM\Entity(repositoryClass="AppBundle\EntityRepository\MerchantRepository")
 * @ORM\Table(name="merchants")
 */
class Merchant
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $name = '';

    /**
     * @var Transaction[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Transaction", mappedBy="merchant")
     */
    private $transactions;

    /**
     * Merchant constructor.
     */
    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Merchant
     */
    public function setName(string $name): Merchant
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Transaction[]
     */
    public function getTransactions(): array
    {
        return $this->transactions;
    }

    /**
     * @param Transaction $transaction
     *
     * @return Merchant
     */
    public function addTransaction(Transaction $transaction): Merchant
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setMerchant($this);
        }

        return $this;
    }

    /**
     * @param Transaction $transaction
     *
     * @return Merchant
     */
    public function removeTransaction(Transaction $transaction): Merchant
    {
        if ($this->transactions->contains($transaction)) {
            $this->transactions->removeElement($transaction);
            if ($transaction->getMerchant() === $this) {
                $transaction->setMerchant(null);
            }
        }

        return $this;
    }
}
