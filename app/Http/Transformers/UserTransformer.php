<?php

namespace imbalance\Http\Transformers;


trait UserTransformer {

    use Transformer;

    public function transform($user) {

        return [
            'userId' => (int)$user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'forename' => $user['user_details']['forename'],
            'surname' => $user['user_details']['surname'],
            'dob' => date('d-m-Y', strtotime($user['user_details']['dob'])),
            'country' => $user['user_details']['country'],
            'website' => $user['user_details']['website'],
            'avatar' => $user['user_details']['avatar'],
            'twitter username' => $user['user_details']['twitterUsername'],
            'facebook' => $user['user_details']['facebook'],
        ];

    }

}