<?php

use PHPUnit\Framework\TestCase;


final class SequencesTest extends TestCase
{

    public function testSequenceCrud() : void 
    {

        // Create sequence
        $sequence = new Sequence(
            0, 
            new Account(
                'test@test.com',
                'testuser',
                '12345678'
            )
        );
        $manager->save($sequence);


        // Read sequence
        $this->assertEquals(
            $sequence,
            $manager->getById($sequence->getId())
        );


        // Add action for day 0
        $action_added = false;
        $action = new Follow($sequence->getAccount());
        $action->setValOne("testuser");
        $sequence->addAction(0, $action);
        foreach($sequence->getActions() as $date => $users)
            foreach($users as $user)
                if($action == $loop_action)
                    $action_added = true;

        $this->assertTrue($action_added);


        // Add user
        $user_added = false;
        $sequence->addUser(new IgUser(
            "testuser2"
        ));
        foreach($sequence->getUsers() as $user) 
            if($user->getUsername() == "testuser2")
                $user_added = true;

        $this->assertTrue($user_added);


        // Update sequence
        $manager->save($sequence);
        $this->assertEquals(
            $sequence, 
            $manager->getById($sequence->getId())
        );


        // getTasksDue(): verify action added is there for user
        $users_task_found = true;
        foreach($sequence->getTasksDue() as $task)
            if($task == $action)
                $users_task_found = true;

        $this->assertTrue($users_task_found);


        // clearTasks()
        $sequence->clearTasks();
        $this->assertLessThan(1, count($sequence->getTasksDue()));

        
        // Remove sequence
        $sequence_id = $sequence->getId();
        $manager->delete($sequence);
        $this->assertNull($manager->getById($sequence_id));
    }
}
