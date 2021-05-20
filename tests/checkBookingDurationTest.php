<?php
declare(strict_types=1);

namespace App\Tests;

use App\Entity\Booking;
use App\Entity\Room;
use App\Entity\User;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\Date;

//use cmd command bin/phpunit to run tests
//remember to preface test functions with 'test' or else it won't work.

final class checkBookingDurationTest extends TestCase
{
    const SECONDS_IN_HOUR = 60 * 60;


    /**
     * here we test the booking duration calculation. the calculation should return a correct duration in minutes of someone's stay.
     * if the end date is BEFORE the start date, the response should be a negative value, which we can also use to check if a stay is valid
     */
    public function testBookingDuration(): void
    {

        $booking_1 = new Booking();
        $booking_1->setStartDate(DateTime::createFromFormat('H-i', '2-00'))
            ->setEndDate(DateTime::createFromFormat('H-i', '3-00'));

        $booking_2 = new Booking();
        $booking_2->setStartDate(DateTime::createFromFormat('H-i', '12-00'))
            ->setEndDate(DateTime::createFromFormat('H-i', '17-00'));

        $booking_3 = new Booking();
        $booking_3->setStartDate(DateTime::createFromFormat('H-i', '5-30'))
            ->setEndDate(DateTime::createFromFormat('H-i', '7-30'));

        $booking_4 = new Booking();
        $booking_4->setStartDate(DateTime::createFromFormat('H-i', '5-30'))
            ->setEndDate(DateTime::createFromFormat('H-i', '2-30'));

        self::assertEquals(
            self::SECONDS_IN_HOUR * 1,
            $booking_1->getDuration(),
            "expecting a difference of " . self::SECONDS_IN_HOUR * 1 . " seconds."
        );
        self::assertEquals(
            self::SECONDS_IN_HOUR * 5,
            $booking_2->getDuration(),
            "expecting a difference of " . self::SECONDS_IN_HOUR * 5 . " seconds."
        );
        self::assertEquals(
            self::SECONDS_IN_HOUR * 2,
            $booking_3->getDuration(),
            "expecting a difference of " . self::SECONDS_IN_HOUR * 2 . " seconds."
        );
        self::assertEquals(
            self::SECONDS_IN_HOUR * -3,
            $booking_4->getDuration(),
            "expecting a difference of " . self::SECONDS_IN_HOUR * -3 . " seconds."
        );
    }

    public function testBookingValidity(): void
    {
        $booking_1 = new Booking();
        $booking_1->setStartDate(DateTime::createFromFormat('H-i', '2-00'))
            ->setEndDate(DateTime::createFromFormat('H-i', '3-00'));

        $booking_2 = new Booking();
        $booking_2->setStartDate(DateTime::createFromFormat('H-i', '0-00'))
            ->setEndDate(DateTime::createFromFormat('H-i', '5-00'));

        $booking_3 = new Booking();
        $booking_3->setStartDate(DateTime::createFromFormat('H-i', '5-30'))
            ->setEndDate(DateTime::createFromFormat('H-i', '7-30'));

        $booking_4 = new Booking();
        $booking_4->setStartDate(DateTime::createFromFormat('H-i', '5-30'))
            ->setEndDate(DateTime::createFromFormat('H-i', '2-30'));

        self::assertTrue($booking_1->isValidDuration());
        self::assertFalse($booking_2->isValidDuration());
        self::assertTrue($booking_3->isValidDuration());
        self::assertFalse($booking_4->isValidDuration());

    }

    public function testCreditCostValidity(): void
    {
        $richUser = new User(true, 100);
        $poorUser = new User(false, 2);
        $dirtPoorUser = new User(false, 0);

        $booking_1 = new Booking();
        $booking_1->setStartDate(DateTime::createFromFormat('H-i', '0-00'))
            ->setEndDate(DateTime::createFromFormat('H-i', '1-00'))
            ->setUser($richUser);


        $booking_2 = new Booking();
        $booking_2->setStartDate(DateTime::createFromFormat('H-i', '0-00'))
            ->setEndDate(DateTime::createFromFormat('H-i', '4-00'))
            ->setUser($richUser);

        $booking_3 = new Booking();
        $booking_3->setStartDate(DateTime::createFromFormat('H-i', '0-00'))
            ->setEndDate(DateTime::createFromFormat('H-i', '1-00'))
            ->setUser($poorUser);

        $booking_4 = new Booking();
        $booking_4->setStartDate(DateTime::createFromFormat('H-i', '0-00'))
            ->setEndDate(DateTime::createFromFormat('H-i', '4-00'))
            ->setUser($poorUser);

        $booking_5 = new Booking();
        $booking_5->setStartDate(DateTime::createFromFormat('H-i', '0-00'))
            ->setEndDate(DateTime::createFromFormat('H-i', '1-00'))
            ->setUser($dirtPoorUser);

        $booking_6 = new Booking();
        $booking_6->setStartDate(DateTime::createFromFormat('H-i', '0-00'))
            ->setEndDate(DateTime::createFromFormat('H-i', '4-00'))
            ->setUser($dirtPoorUser);

        self::assertTrue($booking_1->isAffordableByUser());
        self::assertTrue($booking_2->isAffordableByUser());
        self::assertTrue($booking_3->isAffordableByUser());
        self::assertFalse($booking_4->isAffordableByUser());
        self::assertFalse($booking_5->isAffordableByUser());
        self::assertFalse($booking_6->isAffordableByUser());

    }

    public function testIfBookingIsAvailable(): void
    {
        //defining the data needed for the test. There's a lot going on here.
        #region dataSetup
        $user1 = new User();
        $user2 = new User();
        $user3 = new User();
        $user4 = new User();

        $user5 = new User();

        $bookings = [
            new Booking(
                DateTime::createFromFormat('H-i', '0-00'),
                DateTime::createFromFormat('H-i', '4-00'),
                $user1
            ),
            // 1 hour gap
            new Booking(
                DateTime::createFromFormat('H-i', '5-00'),
                DateTime::createFromFormat('H-i', '6-00'),
                $user2
            ),
            //4 hour gap
            new Booking(
                DateTime::createFromFormat('H-i', '10-00'),
                DateTime::createFromFormat('H-i', '12-00'),
                $user3
            ),
            // no gap
            new Booking(
                DateTime::createFromFormat('H-i', '12-00'),
                DateTime::createFromFormat('H-i', '16-00'),
                $user2
            ),
            // 2 hour gap
            new Booking(
                DateTime::createFromFormat('H-i', '18-00'),
                DateTime::createFromFormat('H-i', '20-00'),
                $user1
            ),
            //no gap
            new Booking(
                DateTime::createFromFormat('H-i', '20-00'),
                DateTime::createFromFormat('H-i', '24-00'),
                $user4
            ),
        ];

        //this one should be available
        $testBooking_1 = new Booking(
            DateTime::createFromFormat('H-i', '4-00'),
            DateTime::createFromFormat('H-i', '5-00'),
            $user5);
        $testBooking_1->setBookingRecord($bookings);

        //this one should be available
        $testBooking_2 = new Booking(
            DateTime::createFromFormat('H-i', '8-00'),
            DateTime::createFromFormat('H-i', '9-00'),
            $user5);
        $testBooking_2->setBookingRecord($bookings);

        //this one should NOT be available
        $testBooking_3 = new Booking(
            DateTime::createFromFormat('H-i', '11-00'),
            DateTime::createFromFormat('H-i', '12-00'),
            $user5);
        $testBooking_3->setBookingRecord($bookings);
        #endregion
        //ACTUAL TEST TIME

        self::assertTrue($testBooking_1->isUnoccupied());
        self::assertTrue($testBooking_2->isUnoccupied());
        self::assertFalse($testBooking_3->isUnoccupied());

        // TODO: write more tests oh god I'm tired

    }
}