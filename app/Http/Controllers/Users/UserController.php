<?php

namespace imbalance\Http\Controllers\Users;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;

use imbalance\Http\Transformers\UserTransformer;
use imbalance\Models\User;
use imbalance\Models\UserDetail;

use Illuminate\Http\Request;
use imbalance\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use PhpParser\Comment;

class UserController extends Controller {

    use UserTransformer;

    /**
     * Display a listing of the resource.
     *
     * @return \Response
     */
    public function index() {

        $users = User::all();
        return $this->respond([
            'data' => $this->transformCollection($users)
        ]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return \Response
     */
    public function store(Request $request) {

        if (!$request->has(array('username', 'email', 'forename', 'surname'))) {
            return $this->parametersFailed('Parameters failed validation for a user.');
        }

        try {
            User::whereEmail($request->get('email'))->firstOrFail();

            return $this->creationError('A user with the email of ' . $request->get('email') . ' already exists.');
        } catch (ModelNotFoundException $e) {
            $password = str_random();
            $user = User::create([
                'username' => $request->get('username'),
                'email' => $request->get('email'),
                'password' => \Hash::make($password)
            ]);

            $userDetail = new UserDetail([
                'forename' => $request->get('forename'),
                'surname' => $request->get('surname')
            ]);

            $user->userDetail()->save($userDetail);

            \Mail::send('users.create', [
                'userId' => $user->id,
                'email' => $user->email,
                'password' => $password
            ], function ($message) use ($user) {
                $message->subject('Account created @ Imbalance Gaming')
                    ->from('imbalanceAdmin@imbalancegaming.com')
                    ->to($user->email);
            });

            return $this->respondCreated("Created user " . $request->get('username') . " successfully");
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Response
     */
    public function show($id) {

        try {
            /** @var User $user */
            $user = User::findOrFail($id);

            return $this->respond([
                'data' => $this->transform($user->toArray())
            ]);
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("User with ID of $id not found.");
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param  int $id
     * @return \Response
     */
    public function update(Request $request, $id) {

        if (!$request->has(array('email', 'role', 'forename', 'surname'))) {
            return $this->parametersFailed('Parameters failed validation for a user.');
        }

        try {
            /** @var User $user */
            $user = User::findOrFail($id);
            $user->email = $request->get('email');
            $user->role = $request->get('role');
            $user->save();

            $user->userDetail()->update([
                'forename' => $request->get('forename'),
                'surname' => $request->get('surname')
            ]);

            return $this->respondUpdated("User " . $user->username . " updated successfully");
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("User with ID of $id not found.");
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Response
     */
    public function destroy($id) {
        //
    }

    public function usersWithDetails() {

        $users = User::with('userDetail')->get();
        return $this->respond([
            'data' => $this->transformCollectionWithRelation($users)
        ]);

    }


}
