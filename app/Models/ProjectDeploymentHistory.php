<?php

namespace imbalance\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * imbalance\Models\ProjectDeploymentHistory
 *
 * @property integer $id
 * @property string $deployment_date
 * @property string $user
 * @property string $server
 * @property string $status
 * @property integer $project_id
 * @property-read \imbalance\Models\Project $project
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\ProjectDeploymentHistory whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\ProjectDeploymentHistory whereDeploymentDate($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\ProjectDeploymentHistory whereUser($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\ProjectDeploymentHistory whereServer($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\ProjectDeploymentHistory whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\ProjectDeploymentHistory whereProjectId($value)
 * @mixin \Eloquent
 * @property string $job_output
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\ProjectDeploymentHistory whereJobOutput($value)
 */
class ProjectDeploymentHistory extends Model
{
    protected $table = 'project_deployment_history';

    public $timestamps = false;

    protected $fillable = [
        'deployment_date',
        'project_id',
        'user',
        'server',
        'status',
        'job_output'
    ];

    protected $guarded = [];

    public function project() {
        return $this->belongsTo('imbalance\Models\Project', 'project_id');
    }
        
}