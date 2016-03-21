<?php

namespace imbalance\Http\Transformers;


trait UserDetailsTransformer {

    use Transformer;

    public function transform($userDetails) {

        return [
            'userId' => (int)$userDetails['user_id'],
            'forename' => $userDetails['forename'],
            'surname' => $userDetails['surname'],
            'dob' => date('d/m/Y', strtotime($userDetails['dob'])),
            'country' => $userDetails['country'],
            'website' => $userDetails['website'],
            'avatar' => $userDetails['avatar'],
            'twitter username' => $userDetails['twitterUsername'],
            'facebook' => $userDetails['facebook'],
        ];

    }

    public function transformWithRelation($userDetails) {

    }

}