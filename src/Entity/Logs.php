<?php

namespace App\Entity;

use App\Repository\LogsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LogsRepository::class)
 */
class Logs
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Month", mappedBy="logs")
     */
    private $months;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMonths(): ?array
    {
        return $this->months;
    }

    public function addMonth(Month $month): self
    {
        if(!$this->months->contains($month))
        {
            $this->months[] = $month;
            $month->setLogs($this);
        }
        return $this;
    }
    public function setMonths(array $months): self
    {
        $this->months = $months;
        return $this;
    }

}
