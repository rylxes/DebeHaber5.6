<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Laravel\Spark\CanJoinTeams;
use Laravel\Spark\User as SparkUser;
use Hootlex\Friendships\Traits\Friendable;
use Illuminate\Notifications\Notifiable;

class User extends SparkUser
{
    use CanJoinTeams, HasApiTokens, Notifiable, Friendable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
    ];

    // $user->notify(new InvoicePaid($invoice));

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'authy_id',
        'country_code',
        'phone',
        'card_brand',
        'card_last_four',
        'card_country',
        'billing_address',
        'billing_address_line_2',
        'billing_city',
        'billing_zip',
        'billing_country',
        'extra_billing_information',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'trial_ends_at' => 'datetime',
        'uses_two_factor_auth' => 'boolean',
    ];

    /**
     * Get the taxPayers for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function taxPayers()
    {
        return $this->hasManyThrough('App\TaxpayerIntegration', 'App\Team');
    }

    /**
     * Get the taxPayerFavs for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function taxPayerFavs()
    {
        return $this->hasMany(TaxpayerFav::class);
    }
}
