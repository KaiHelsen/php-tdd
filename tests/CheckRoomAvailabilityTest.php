<?php
namespace App\Tests;

use App\Entity\Room;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

//use cmd command bin/phpunit to run tests
//remember to preface test functions with 'test' or else it won't work.

final class CheckRoomAvailabilityTest extends TestCase
{
    public function testPremiumRoom() : void
    {
        $regularRoom = new Room();
        $premiumRoom = new Room(true);
        $regularUser = new User();
        $premiumUser = new User(true);

        self::assertTrue($regularRoom->canBook($regularUser));
        self::assertTrue($regularRoom->canBook($premiumUser));
        self::assertFalse($premiumRoom->canBook($regularUser));
        self::assertTrue($premiumRoom->canBook($premiumUser));
    }
}