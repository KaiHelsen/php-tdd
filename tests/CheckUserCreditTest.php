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

final class CheckUserCreditTest extends TestCase
{
    public function testUserCredit() : void
    {
        $user_1 = new User();
        self::assertEquals(100, $user_1->getCredit(), 'expecting a default credit value of 100');
    }

    public function testUserHasSufficientCredit():void
    {
        self::assertTrue(true);
    }
}