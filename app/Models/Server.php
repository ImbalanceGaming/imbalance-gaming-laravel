<?php

namespace imbalance\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Server
 *
 * @property integer $id
 * @property string $address
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\Server whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\Server whereAddress($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\imbalance\Models\Project[] $projects
 * @property string $name
 * @method static \Illuminate\Database\Query\Builder|\imbalance\Models\Server whereName($value)
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
        return $this->belongsToMany('imbalance\Models\Project', 'project_server');
    }

        
}