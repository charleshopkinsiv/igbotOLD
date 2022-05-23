<?php

use PHPUnit\Framework\TestCase;


final class AccountsTest extends TestCase
{

    public function testAccountCrud() : void 
    {

        // Create account
        $account = new Account("test@test.com", "test", "123456");
        $manager->create($account);


        // Read acount
        $this->assertEquals(
            $account,
            $manager->getById($account->getId())
        );


        // Update account
        $account->setUsername("test1");
        $manager->update($account);
        $this->assertEquals(
            $account,
            $manager->getById($account->getId())
        );


        // Delete account
        $account_id = $account->getId();
        $manager->delete($account);
        $this->assertNull($manager->getById($account_id));
    }
}
