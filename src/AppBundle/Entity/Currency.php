<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Currency
 * @ORM\Entity(repositoryClass="AppBundle\EntityRepository\CurrencyRepository")
 * @ORM\Table(name="currencies")
 */
class Currency
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
     * @ORM\Column(type="string", length=1)
     */
    private $symbol = '';

    /**
     * @var string
     * @ORM\Column(type="string", length=3)
     */
    private $isoCode = '';

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
    public function getSymbol(): string
    {
        return $this->symbol;
    }

    /**
     * @param string $symbol
     *
     * @return Currency
     */
    public function setSymbol(string $symbol): Currency
    {
        $this->symbol = $symbol;
        return $this;
    }

    /**
     * @return string
     */
    public function getIsoCode(): string
    {
        return $this->isoCode;
    }

    /**
     * @param string $isoCode
     *
     * @return Currency
     */
    public function setIsoCode(string $isoCode): Currency
    {
        $this->isoCode = $isoCode;
        return $this;
    }
}
