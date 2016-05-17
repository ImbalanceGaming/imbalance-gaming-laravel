<?php

namespace imbalance\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * imbalance\Models\Server
 *
 * @property integer $id
 * @property string $name
 * @property string $address
 * @property-read \Illuminate\Database\Eloquent\Collection|\imbalance\Models\Project[] $projects
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\Server whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\Server whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\Server whereAddress($value)
 * @mixin \Eloquent
 */
class Server extends Model
{
    protected $table = 'server';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'address'
    ];

    protected $guarded = [];

    public function projects() {
        return $this->belongsToMany('imbalance\Models\Project', 'project_server')->withPivot('first_run');
    }

        
}