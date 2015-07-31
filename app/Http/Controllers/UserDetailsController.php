<?php

namespace imbalance\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use imbalance\Http\Requests;
use imbalance\Http\Controllers\Controller;
use imbalance\Http\Transformers\UserDetailsTransformer;
use imbalance\Models\User;
use imbalance\Models\UserDetails;

class UserDetailsController extends Controller {

    use UserDetailsTransformer;

    /**
     * Display a listing of the resource.
     *
     * @return \Response
     */
    public function index() {

        $userDetails = UserDetails::all();
        return $this->respond([
            'data' => $this->transformCollection($userDetails)
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return \Response
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param  int $id
     * @return \Response
     */
    public function update(Request $request, $id) {
        //
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

    public function getUsersDetails($id) {

        try {
            /** @var User $user */
            $user = User::findOrFail($id);
        } catch(ModelNotFoundException $e) {
            return $this->respondNotFound("User with ID of $id not found.");
        }

        $userDetails = $user->userDetails;

        if (!empty($userDetails)) {
                return $this->respond([
                    'data' => $this->transform($userDetails)
                ]);
        } else {
            return $this->respondNotFound("User $id does not have any details");
        }
    }
}
