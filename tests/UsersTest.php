<?php

use PHPUnit\Framework\TestCase;


final class UsersTest extends TestCase
{

    public function testIgUserCrud() : void 
    {

        // Create user
        $user = new IgUser(
            "test_user",
            "Test User",
            "Test description"
        );
        $manager->saveUser(
            $user,
            "test_source"
        );
        $this->assetEquals(
            $user,
            $manager->getById($user->getId())
        )


        // Save image
        $test_image = __DIR__ . "test/image.jpg";
        $manager->saveUserImage(
            $user->getUsername(),
            $test_image
        );
        $this->assertEquals(
            file_get_contents(__DIR__ . "test/image.jpg"),
            file_get_contents($user->getImageFile())
        );


        // Update user
        $user->setName("Test User2");
        $this->assertEquals(
            $manager->getById($user->getId()),
            $user
        );


        // Delete
        $user_id = $user->getId();
        $manager->delete($user);
        $this->assertNull($manager->getById($user_id));
    }
}
