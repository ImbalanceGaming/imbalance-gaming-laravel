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
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property boolean $email_verified
 * @property string $role
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereUsername($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereEmailVerified($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereRole($value)
 * @property-read \imbalance\Models\UserDetail $userDetail
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\imbalance\Models\Project[] $projects
 * @property-read \Illuminate\Database\Eloquent\Collection|\imbalance\Models\UserBoard[] $userBoards
 * @property-read \Illuminate\Database\Eloquent\Collection|\imbalance\Models\Permission[] $permissions
 * @property string $last_login
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereLastLogin($value)
 */
class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['username', 'password', 'email', 'role'];

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
    public function userDetail() {
        return $this->hasOne('imbalance\Models\UserDetail');
    }

    /**
     * Get collection of projects that this user is a lead for
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projects() {
        return $this->hasMany('imbalance\Models\Project');
    }

    public function userBoards() {
        return $this->hasMany('imbalance\Models\UserBoard');
    }

    public function permissions() {
        return $this->belongsToMany('imbalance\Models\Permission', 'permission_assignment');
    }

}
