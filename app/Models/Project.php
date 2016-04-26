<?php

namespace imbalance\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * imbalance\Models\Project
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property string $description
 * @property string $url
 * @property integer $user_id
 * @property-read \imbalance\Models\User $leadUser
 * @property-read \Illuminate\Database\Eloquent\Collection|\imbalance\Models\Group[] $groups
 * @property-read \Illuminate\Database\Eloquent\Collection|\imbalance\Models\ProjectPackage[] $packages
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\Project whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\Project whereKey($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\Project whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\Project whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\Project whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\Project whereUserId($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\imbalance\Models\Server[] $servers
 * @property-read \Illuminate\Database\Eloquent\Collection|\imbalance\Models\ProjectDeploymentHistory[] $history
 */
class Project extends Model
{
    protected $table = 'project';

    public $timestamps = false;

    protected $fillable = [
        'key',
        'name',
        'description',
        'url',
        'user_id'
    ];

    protected $guarded = [];

    public function leadUser() {
        return $this->belongsTo('imbalance\Models\User', 'user_id');
    }

    public function groups() {
        return $this->belongsToMany('imbalance\Models\Group', 'project_group');
    }

    public function servers() {
        return $this->belongsToMany('imbalance\Models\Server', 'project_server');
    }

    public function packages() {
        return $this->hasMany('imbalance\Models\ProjectPackage');
    }

    public function history() {
        return $this->hasMany('imbalance\Models\ProjectDeploymentHistory');
    }

        
}