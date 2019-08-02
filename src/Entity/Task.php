<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 */
class Task
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     * @Assert\Length(min=5)
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $addDate;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;


    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return Task
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getAddDate(): ?\DateTimeInterface
    {
        return $this->addDate;
    }

    /**
     * @param \DateTimeInterface $addDate
     * @return Task
     */
    public function setAddDate(\DateTimeInterface $addDate): self
    {
        $this->addDate = $addDate;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     * @return Task
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
