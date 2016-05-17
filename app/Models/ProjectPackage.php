<?php

namespace imbalance\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ProjectPackage
 *
 * @property integer $id
 * @property string $name
 * @property string $repository
 * @property string $deploy_branch
 * @property integer $project_id
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\ProjectPackage whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\ProjectPackage whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\ProjectPackage whereRepository($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\ProjectPackage whereDeployBranch($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\ProjectPackage whereProjectId($value)
 * @mixin \Eloquent
 * @property-read \imbalance\Models\Project $project
 * @property-read \Illuminate\Database\Eloquent\Collection|\imbalance\Models\ProjectPackageCommand[] $projectPackageCommands
 * @property string $deploy_location
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\ProjectPackage whereDeployLocation($value)
 * @property integer $order
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\ProjectPackage whereOrder($value)
 */
class ProjectPackage extends Model
{
    protected $table = 'project_package';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'repository',
        'deploy_branch',
        'deploy_location',
        'project_id',
        'order'
    ];

    protected $guarded = [];

    public function project() {
        return $this->belongsTo('imbalance\Models\Project', 'project_id');
    }

    public function projectPackageCommands() {
        return $this->hasMany('imbalance\Models\ProjectPackageCommand');
    }

        
}