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
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $role
 * @property boolean $email_verified
 * @property string $email_verified_code
 * @property boolean $active
 * @property string $last_login
 * @property string $forename
 * @property string $surname
 * @property string $dob
 * @property string $country
 * @property string $website
 * @property string $avatar
 * @property string $twitter_username
 * @property string $facebook
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\imbalance\Models\Project[] $projects
 * @property-read \Illuminate\Database\Eloquent\Collection|\imbalance\Models\UserBoard[] $userBoards
 * @property-read \Illuminate\Database\Eloquent\Collection|\imbalance\Models\Permission[] $permissions
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereUsername($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereRole($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereEmailVerified($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereEmailVerifiedCode($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereActive($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereLastLogin($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereForename($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereSurname($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereDob($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereCountry($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereWebsite($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereAvatar($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereTwitterUsername($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereFacebook($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property boolean $has_dev_area
 * @property-read \Illuminate\Database\Eloquent\Collection|\imbalance\Models\Group[] $groups
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\User whereHasDevArea($value)
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
    protected $fillable = ['username', 'password', 'email', 'role', 'active'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    // Model relation functions

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

    public function groups() {
        return $this->belongsToMany('imbalance\Models\Group', 'group_membership');
    }

}
