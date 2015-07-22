<?php

namespace imbalance\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use imbalance\Http\Transformers\UserTransformer;
use imbalance\Models\User;

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

        $users = User::with('userDetails')->get();
        return $this->respond([
            'data' => $this->transformCollection($users)
        ]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return \Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Response
     */
    public function show($id) {

        try {
            /** @var User $user */
            $user = User::with('userDetails')->findOrFail($id);

            return $this->respond([
                'data' => $this->transform($user->toArray())
            ]);
        } catch(ModelNotFoundException $e) {
            return $this->respondNotFound("User with ID of $id not found.");
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Response
     */
    public function destroy($id)
    {
        //
    }

}
