<?php

namespace imbalance\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use imbalance\Models\User;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use imbalance\Http\Requests;
use imbalance\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthenticateController extends Controller
{

    use ThrottlesLogins;

    public function register(Request $request) {

    }

    public function validateEmail(Request $request) {

    }

    public function resetPassword(Request $request) {

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function authenticate(Request $request) {

        // grab credentials from the request
        $credentials = $request->only('email', 'password');

        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->authError("Email or password invalid");
            }

            $user = JWTAuth::toUser($token);

            if (!$user->email_verified) {
                return $this->authError("You cannot login till you have verified your email");
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return $this->tokenError('Unable to create token');
        }

        // all good so return the token
        return $this->respond(compact('token'));

    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @internal param string $token
     */
    public function getAuthenticatedUser(Request $request) {

        $token = $request->only('token')['token'];

        try {
            if (!$user = JWTAuth::authenticate($token)) {
                return $this->respondNotFound('User not found');
            }
        } catch (TokenExpiredException $e) {
            return $this->setStatusCode($e->getStatusCode())->respondWithError('Token expired');
        } catch (TokenInvalidException $e) {
            return $this->setStatusCode($e->getStatusCode())->respondWithError('Token invalid');
        } catch (JWTException $e) {
            return $this->setStatusCode($e->getStatusCode())->respondWithError('Token absent');
        }

        // the token is valid and we have found the user via the sub claim
        return $this->respond($user);

    }

}
