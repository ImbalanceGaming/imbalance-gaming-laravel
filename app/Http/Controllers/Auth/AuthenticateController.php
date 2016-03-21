<?php

namespace imbalance\Http\Controllers\Auth;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use imbalance\Models\User;
use imbalance\Models\UserDetail;
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

        if (!$request->has(array('username', 'email', 'password', 'forename', 'surname'))) {
            return $this->parametersFailed('Parameters failed validation for a user.');
        }

        try {
            User::whereUsername($request->get('username'))->firstOrFail();

            return $this->creationError('A user with the username '.$request->get('username').' already exists.');
        } catch(ModelNotFoundException $e) {
            $user = User::create([
                'username' => $request->get('username'),
                'email' => $request->get('email'),
                'password' => \Hash::make($request->get('password'))
            ]);

            $userDetail = new UserDetail([
                'forename' => $request->get('forename'),
                'surname' => $request->get('surname')
            ]);

            $user->userDetail()->save($userDetail);

            \Mail::send('email.reg', ['userId'=>$user->id],function($message) use ($user) {
               $message->subject('Please activate your account for Imbalance Gaming')
                   ->from('imbalanceAdmin@imbalancegaming.com')
                   ->to($user->email);
            });

            return $this->respondCreated("Created user ".$request->get('username')." successfully");
        }

    }

    public function validateEmail(Request $request) {
        $id = $request->only('id')['id'];

        try {
            /** @var User $user */
            $user = User::findOrFail($id);
            $user->email_verified = true;
            $user->save();

            return $this->respondCreated("Activated account for ".$user->username);
        } catch(ModelNotFoundException $e) {
            return $this->respondNotFound("Unable to activate your account.");
        }
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

            /** @var User $user */
            $user = JWTAuth::toUser($token);

            if (!$user->email_verified) {
                return $this->authError("You cannot login till you have verified your email");
            }

            $user->last_login = date('Y-m-d H:i:s');
            $user->save();
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return $this->tokenError('Unable to create token');
        }

        // all good so return the token
        return $this->respond(compact('token'));

    }

    /**
     * @return JsonResponse
     * @internal param Request $request
     * @internal param string $token
     */
    public function getAuthenticatedUser() {

        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
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
