<?php

namespace imbalance\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Group
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property-read \Illuminate\Database\Eloquent\Collection|\imbalance\Models\Project[] $projects
 * @property-read \Illuminate\Database\Eloquent\Collection|\imbalance\Models\User[] $users
 * @property-read \Illuminate\Database\Eloquent\Collection|\imbalance\Models\Permission[] $permissions
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\Group whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\Group whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\Group whereDescription($value)
 * @mixin \Eloquent
 */
class Group extends Model
{
    protected $table = 'group';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'description'
    ];

    protected $guarded = [];

    public function projects() {
        return $this->belongsToMany('imbalance\Models\Project', 'project_group');
    }

    public function users() {
        return $this->belongsToMany('imbalance\Models\User', 'group_membership');
    }

    public function permissions() {
        return $this->belongsToMany('imbalance\Models\Permission', 'permission_assignment');
    }
        
}