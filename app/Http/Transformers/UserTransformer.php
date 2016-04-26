<?php

namespace imbalance\Http\Transformers;


class UserTransformer extends Transformer {
    
    public function transform($user) {

        if (!$user) {
            return null;
        }

        if ($user['last_login'] != '0000-00-00 00:00:00') {
            $lastLogin = date('M j, Y, G:i:s', strtotime($user['last_login']));
        } else {
            $lastLogin = 'None';
        }

        return [
            'id' => (int)$user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role'],
            'last_login' => $lastLogin,
            'active' => (boolean)$user['active'],
            'forename' => $user['forename'],
            'surname' => $user['surname'],
            'dob' => date('d/m/Y', strtotime($user['dob'])),
            'country' => $user['country'],
            'website' => $user['website'],
            'avatar' => $user['avatar'],
            'twitter_username' => $user['twitter_username'],
            'facebook' => $user['facebook'],
            'has_dev_area' => $user['has_dev_area']
        ];

    }

}