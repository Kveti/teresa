<?php

namespace App\Entity;

use App\Repository\MonthRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MonthRepository::class)
 */
class Month
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Logs", inversedBy="months")
     */
    private $logs;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $month;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogs(): ?Logs
    {
        return $this->logs;
    }

    public function setLogs(string $logs): self
    {
        $this->logs = $logs;

        return $this;
    }

    public function getMonth(): ?string
    {
        return $this->month;
    }

    public function setMonth(string $month): self
    {
        $this->month = $month;

        return $this;
    }

    public function __toString(): string
    {
        return $this->month;
    }
}
