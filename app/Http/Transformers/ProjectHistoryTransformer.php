<?php

namespace imbalance\Http\Transformers;


class ProjectHistoryTransformer extends Transformer {

    public function transform($project) {

        if (!$project) {
            return null;
        }

        if ($project['deployment_date'] != '0000-00-00 00:00:00') {
            $deploymentDate = date('M j, Y, G:i:s', strtotime($project['deployment_date']));
        } else {
            $deploymentDate = 'None';
        }

        return [
            'id' => (int)$project['id'],
            'deployment_date' => $deploymentDate,
            'committer' => $project['committer'],
            'commit' => $project['commit'],
            'status' => $project['status']
        ];

    }

}