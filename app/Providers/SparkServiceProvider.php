<?php

namespace App\Providers;

use Laravel\Spark\Spark;
use Laravel\Spark\Providers\AppServiceProvider as ServiceProvider;
use Laravel\Spark\Exceptions\IneligibleForPlan;
use Laravel\Cashier\Cashier;

class SparkServiceProvider extends ServiceProvider
{
    /**
    * Your application and company details.
    *
    * @var array
    */
    protected $details = [
        'vendor' => 'Cognitivo',
        'product' => 'DebeHaber',
        'street' => 'Planta Industrial, Km 24 Ruta II.',
        'location' => 'Capiata, Paraguay',
        'phone' => '+595-228-63115',
    ];

    /**
    * The address where customer support e-mails should be sent.
    *
    * @var string
    */
    protected $sendSupportEmailsTo = 'soporte@debehaber.com';

    /**
    * All of the application developer e-mail addresses.
    *
    * @var array
    */
    protected $developers = [
        'abhi@cognitivo.in',
        'ashah@indopar.com.py',
        'pankeel@cognitivo.in',
        'heti.shah@gmail.com'
    ];

    /**
    * Indicates if the application will expose an API.
    *
    * @var bool
    */
    protected $usesApi = true;

    /**
    * Finish configuring Spark for the application.
    *
    * @return void
    */
    public function booted()
    {

        Spark::useTwoFactorAuth();

        Spark::useStripe()->noCardUpFront()->teamTrialDays(10);

        Spark::useRoles([
            'mem' => 'Member',
            'aux' => 'Auxiliary',
            'acc' => 'Accountant',
            'aud' => 'Auditor',
        ]);

        // Spark::noAdditionalTeams();

        Spark::chargeTeamsPerSeat('Contribuyente', function ($team) {
            return $team->taxpayers()->count();
        });

        // Cashier::useCurrency('pyg', 'PYG ');

        Spark::freeTeamPlan()
        ->maxTeamMembers(2)
        ->features([
            'First', 'Second', 'Third'
        ]);

        Spark::teamPlan('Pro', 'provider-id-1')
        ->price(50000)
        ->features([
            'First', 'Second', 'Third'
        ]);

        // Spark::promotion('coupon');

        Spark::checkPlanEligibilityUsing(function ($user, $plan)
        {
            if ($plan->name == 'Pro' && $user->todos->count() > 20)
            {
                throw IneligibleForPlan::because('You have too many to-dos.');
            }

            return true;
        });
    }
}
