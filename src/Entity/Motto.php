<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MottoRepository")
 */
class Motto
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $motto;
    /**
      * @ORM\ManyToOne(targetEntity="User", inversedBy="id")
      */
    private $player;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMotto(): ?string
    {
        return $this->motto;
    }

    public function setMotto(string $motto): self
    {
        $this->motto = $motto;

        return $this;
    }

    public function getPlayer(): ?User
    {
        return $this->player;
    }

    public function setPlayer(?User $player): self
    {
        $this->player = $player;

        return $this;
    }
}
