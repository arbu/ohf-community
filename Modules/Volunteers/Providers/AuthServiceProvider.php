<?php

namespace Modules\Volunteers\Providers;

use App\Providers\BaseAuthServiceProvider;

class AuthServiceProvider extends BaseAuthServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        \Modules\Volunteers\Entities\Volunteer::class => \Modules\Volunteers\Policies\VolunteerPolicy::class,
    ];

    protected $permissions = [
        'volunteers.view' => [
            'label' => 'volunteers::permissions.view_volunteers',
            'sensitive' => true,
        ],
        'volunteers.manage' => [
            'label' => 'volunteers::permissions.manage_volunteers',
            'sensitive' => true,
        ],
    ];

    protected $permission_gate_mappings = [
    ];

}
