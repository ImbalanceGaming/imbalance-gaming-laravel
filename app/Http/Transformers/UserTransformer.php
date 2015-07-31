<?php

namespace imbalance\Http\Transformers;


trait UserTransformer {

    use Transformer;

    public function transform($userDetails) {

        return [
            'userId' => (int)$userDetails['id'],
            'username' => $userDetails['username'],
            'email' => $userDetails['email'],
        ];

    }

}