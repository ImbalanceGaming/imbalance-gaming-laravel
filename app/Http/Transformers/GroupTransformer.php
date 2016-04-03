<?php

namespace imbalance\Http\Transformers;


class GroupTransformer extends Transformer{

    public function transform($group) {

        if (!$group) {
            return null;
        }

        return [
            'id' => (int)$group['id'],
            'name' => $group['name'],
            'description' => $group['description']
        ];

    }

}