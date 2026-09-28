<?php

namespace App\Domain\Role\Models;

use App\Domain\Role\Policies\RolePolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Spatie\Permission\Models\Role as SpatieRole;

#[UsePolicy(RolePolicy::class)]
class Role extends SpatieRole
{
    //
}
