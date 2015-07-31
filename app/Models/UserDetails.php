<?php

namespace imbalance\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * imbalance\Models\UserDetails
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $forename
 * @property string $surname
 * @property string $dob
 * @property string $country
 * @property string $website
 * @property string $avatar
 * @property string $twitterUsername
 * @property string $facebook
 * @property-read \imbalance\Models\User $user
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\UserDetails whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\UserDetails whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\UserDetails whereForename($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\UserDetails whereSurname($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\UserDetails whereDob($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\UserDetails whereCountry($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\UserDetails whereWebsite($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\UserDetails whereAvatar($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\UserDetails whereTwitterUsername($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\UserDetails whereFacebook($value)
 */
class UserDetails extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_details';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'forename',
        'surname',
        'dob',
        'country',
        'website',
        'avatar',
        'twitterUsername',
        'facebook'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    // Model relation functions

    /**
     * Get related user information
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo('imbalance\Models\User');
    }

}
