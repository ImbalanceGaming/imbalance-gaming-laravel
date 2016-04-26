<?php

namespace imbalance\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ProjectDeploymentHistory
 *
 * @property integer $id
 * @property string $deployment_date
 * @property integer $project_id
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\ProjectDeploymentHistory whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\ProjectDeploymentHistory whereDeploymentDate($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\ProjectDeploymentHistory whereProjectId($value)
 * @mixin \Eloquent
 * @property-read \imbalance\Models\Project $project
 * @property string $committer
 * @property string $commit
 * @property string $status
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\ProjectDeploymentHistory whereCommitter($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\ProjectDeploymentHistory whereCommit($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\ProjectDeploymentHistory whereStatus($value)
 */
class ProjectDeploymentHistory extends Model
{
    protected $table = 'project_deployment_history';

    public $timestamps = false;

    protected $fillable = [
        'deployment_date',
        'project_id',
        'committer',
        'commit',
        'status'
    ];

    protected $guarded = [];

    public function project() {
        return $this->belongsTo('imbalance\Models\Project', 'project_id');
    }
        
}