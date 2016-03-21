<?php

namespace imbalance\Http\Transformers;


trait UserTransformer {

    use Transformer;

    public function transform($userDetails) {

        if ($userDetails['last_login'] != '0000-00-00 00:00:00') {
            $lastLogin = date('M j, Y, G:i:s', strtotime($userDetails['last_login']));
        } else {
            $lastLogin = 'None';
        }

        return [
            'id' => (int)$userDetails['id'],
            'username' => $userDetails['username'],
            'email' => $userDetails['email'],
            'role' => $userDetails['role'],
            'lastLogin' => $lastLogin
        ];

    }

    public function transformWithRelation($userDetails) {

        return [
            'user' => $this->transform($userDetails),
            'userDetails' => [
                'forename' => $userDetails['user_detail']['forename'],
                'surname' => $userDetails['user_detail']['surname'],
                'dob' => date('d/m/Y', strtotime($userDetails['user_detail']['dob'])),
                'country' => $userDetails['user_detail']['country'],
                'website' => $userDetails['user_detail']['website'],
                'avatar' => $userDetails['user_detail']['avatar'],
                'twitter username' => $userDetails['user_detail']['twitterUsername'],
                'facebook' => $userDetails['user_detail']['facebook'],
            ]
        ];

    }

}