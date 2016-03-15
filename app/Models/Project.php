<?php

namespace imbalance\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Project
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property string $description
 * @property string $status
 * @property integer $user_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \imbalance\Models\User $leadUser
 * @property-read \Illuminate\Database\Eloquent\Collection|\imbalance\Models\Group[] $groups
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\Project whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\Project whereKey($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\Project whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\Project whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\Project whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\Project whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\Project whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\Project whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Project extends Model
{
    protected $table = 'project';

    public $timestamps = true;

    protected $fillable = [
        'key',
        'name',
        'description',
        'status',
        'user_id'
    ];

    protected $guarded = [];

    public function leadUser() {
        return $this->belongsTo('imbalance\Models\User');
    }

    public function groups() {
        return $this->belongsToMany('imbalance\Models\Group', 'project_group');
    }

        
}