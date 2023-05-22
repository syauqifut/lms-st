<?php

namespace App;

class Account extends Model
{
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function organizations()
    {
        return $this->hasMany(Organization::class);
    }

    public function usertypes()
    {
        return $this->hasMany(Usertype::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}
