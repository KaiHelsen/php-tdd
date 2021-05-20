<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
        $newCustomer = new User();
        $newCustomer->setPassword($this->passwordEncoder->encodePassword($newCustomer,'123'));
        $newCustomer->setEmail('generic@bloopmail.com');
        $newCustomer->setRoles(['ROLE_USER']);
        $manager->persist($newCustomer);
    }
}
