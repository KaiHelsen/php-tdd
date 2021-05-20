<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity(repositoryClass=BookingRepository::class)
 */
class Booking
{

    private const MAX_DURATION_HOURS = 4;
    private const CREDIT_PER_HOUR = 2;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Room::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Room;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $endDate;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoom(): ?Room
    {
        return $this->Room;
    }

    public function setRoom(?Room $Room): self
    {
        $this->Room = $Room;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getDuration(): int
    {
        // TODO: calculate difference between booking startDate and endDate
        // return difference in seconds
        return ($this->endDate->getTimestamp() - $this->startDate->getTimestamp());

    }

    #region validatorGetters
    /**
     * @return bool
     * @Assert\IsTrue(message = "duration invalid")
     */
    public function isValidDuration(): bool
    {
        $duration = $this->getDuration();
        return ($duration > 0 && $duration <= self::MAX_DURATION_HOURS * 60 * 60);
//        return false;
    }

    /**
     * @return bool
     * @Assert\IsTrue (message = "you cannot book this room because you are not a premium member.")
     */
    public function isBookable(): bool
    {
        return $this->getRoom()->canBook($this->getUser());
    }

    /**
     * @return bool
     * @Assert\IsTrue (message = "you cannot afford this room for the requested duration. Please get more credit, ya scrub.")
     */
    public function isAffordableByUser(): bool
    {
        $durationHours  = floor($this->getDuration() / (60 * 60));
        $userCredit = $this->getUser()->getCredit();

        return ($userCredit >= $durationHours * self::CREDIT_PER_HOUR);
    }


    //we're not using this anymore since we're moving to using annotations
    //keeping this here for posterity though, can't hurt to have some reference for the future.

//    public static function loadValidatorMetadata(ClassMetadata $metadata): void
//    {
//        $metadata->addPropertyConstraint('user', new NotBlank());
//        $metadata->addPropertyConstraint('startDate', new NotBlank());
//        $metadata->addPropertyConstraint('startDate', new Assert\GreaterThan('now'));
//        $metadata->addPropertyConstraint('endDate', new NotBlank());
//
//        $metadata->addGetterConstraint('validDuration',
//            new Assert\IsTrue(['message' => 'the duration of your stay is not valid!'])
//        );
//
//
//    }
}
