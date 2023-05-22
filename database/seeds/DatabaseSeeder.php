<?php

use App\Account;
use App\Contact;
use App\Organization;
use App\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $account = Account::create(['name' => 'Acme Corporation']);

        factory(User::class)->create([
            'account_id' => $account->id,
            'first_name' => 'Superadmin',
            'fullname' => 'Superadmin',
            'last_name' => 'Doe',
            'email' => 'superadmin@example.com',
            'owner' => true,
            'username' => "superadmin",
            'adress' => 'required',
            'city' => 'required',
            'country' => 'required',
            'mobilephone' => 'required',
            'birthplace' => 'required',
            'birthdate' => Carbon::now(),
            'usertype_id' => 1,
            'is_active' => 1,
            'createdBy' => 1,
        ]);

        factory(User::class, 5)->create(['account_id' => $account->id]);

        $organizations = factory(Organization::class, 100)
            ->create(['account_id' => $account->id]);

        factory(Contact::class, 100)
            ->create(['account_id' => $account->id])
            ->each(function ($contact) use ($organizations) {
                $contact->update(['organization_id' => $organizations->random()->id]);
            });
    }
}
