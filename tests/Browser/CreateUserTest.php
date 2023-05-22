<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\User;
use Auth;

class CreateUserTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->browse(function ($first) {
            Auth::login(User::find(5));
            $first->loginAs(User::find(5))
                ->visit('http://127.0.0.1:8000/users/create')
                ->press('Create User');
            $first->screenshot('filename');
            
        });
    }
}
