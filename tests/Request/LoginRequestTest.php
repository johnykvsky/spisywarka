<?php

namespace App\Tests\Requests;

use App\Request\LoginRequest;
use Doctrine\Common\Annotations\AnnotationRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Ramsey\Uuid\Uuid;
use Faker\Factory;

class LoginRequestTest extends TestCase
{
    protected function setUp()
    {
        AnnotationRegistry::registerLoader('class_exists');
    }

    /**
     * @throws \Assert\AssertionFailedException
     */
    public function test_construct(): void
    {
        $faker = Factory::create('en_GB');
        $email = $faker->email();
        $password = $faker->password();
        $request = new LoginRequest($email, $password);

        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        $errors = $validator->validate($request);

        $this->assertCount(0, $errors);
        $this->assertSame($email, $request->getEmail());
        $this->assertSame($password, $request->getPassword());
    }
}