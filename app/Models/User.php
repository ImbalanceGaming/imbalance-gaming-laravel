<?php

namespace imbalance\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

/**
 * imbalance\Models\User
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereUpdatedAt($value)
 * @property-read \imbalance\Models\UserDetails $userDetails
 * @property string $username 
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereUsername($value)
 */
class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['username', 'password', 'email'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    // Model relation functions

    /**
     * Get details for this user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function userDetails() {
        return $this->hasOne('imbalance\Models\UserDetails');
    }

}
