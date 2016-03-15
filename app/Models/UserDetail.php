<?php

namespace imbalance\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserDetail
 *
 * @property integer $id
 * @property string $forename
 * @property string $surname
 * @property string $dob
 * @property string $country
 * @property string $website
 * @property string $avatar
 * @property string $twitterUsername
 * @property string $facebook
 * @property integer $user_id
 * @property-read \imbalance\Models\User $user
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\UserDetail whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\UserDetail whereForename($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\UserDetail whereSurname($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\UserDetail whereDob($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\UserDetail whereCountry($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\UserDetail whereWebsite($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\UserDetail whereAvatar($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\UserDetail whereTwitterUsername($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\UserDetail whereFacebook($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\UserDetail whereUserId($value)
 * @mixin \Eloquent
 */
class UserDetail extends Model
{
    protected $table = 'user_detail';

    public $timestamps = false;

    protected $fillable = [
        'forename',
        'surname',
        'dob',
        'country',
        'website',
        'avatar',
        'twitterUsername',
        'facebook',
        'user_id'
    ];

    protected $guarded = [];

    public function user() {
        return $this->hasOne('imbalance\Models\User');
    }

        
}