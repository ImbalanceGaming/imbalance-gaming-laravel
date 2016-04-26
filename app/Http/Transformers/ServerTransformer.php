<?php

namespace imbalance\Http\Transformers;


class ServerTransformer extends Transformer {
    
    public function transform($server) {

        if (!$server) {
            return null;
        }

        return [
            'id' => (int)$server['id'],
            'name' => $server['name'],
            'address' => $server['address'],
        ];

    }

}