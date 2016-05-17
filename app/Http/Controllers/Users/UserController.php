<?php

namespace imbalance\Http\Controllers\Users;

use Illuminate\Database\Eloquent\ModelNotFoundException;

use imbalance\Http\Transformers\GroupTransformer;
use imbalance\Http\Transformers\ModuleSectionTransformer;
use imbalance\Http\Transformers\PermissionTransformer;
use imbalance\Http\Transformers\UserTransformer;
use imbalance\Models\User;

use Illuminate\Http\Request;
use imbalance\Http\Controllers\Controller;
use PhpParser\Comment;

class UserController extends Controller {

    private $_userTransformer;
    private $_groupTransformer;
    private $_permissionTransformer;
    private $_moduleSectionTransformer;

    function __construct() {

        $this->_userTransformer = new UserTransformer();
        $this->_groupTransformer = new GroupTransformer();
        $this->_permissionTransformer = new PermissionTransformer();
        $this->_moduleSectionTransformer = new ModuleSectionTransformer();
        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Response
     */
    public function index() {

        $limit = \Input::get('limit') ?: 10;

        if ($limit > 20) {
            return $this->respondWithError('Pagination limit can not be above 20');
        }

        $users = User::paginate($limit);

        return $this->respondWithPagination($users, $this->_userTransformer->transformCollection($users->items()));

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
            $username = $request->get('username');
            try {
                User::whereUsername($username)->firstOrFail();
                $username = $username . (string)rand();
            } catch (ModelNotFoundException $e) {
            }

            $password = str_random();
            $user = User::create([
                'username' => $username,
                'email' => $request->get('email'),
                'password' => \Hash::make($password),
                'forename' => $request->get('forename'),
                'surname' => $request->get('surname')
            ]);

            \Mail::send('users.create', [
                'userId' => $user->id,
                'email' => $user->email,
                'password' => $password
            ], function ($message) use ($user) {

                $message->subject('Account created @ Imbalance Gaming')
                    ->from('imbalanceAdmin@imbalancegaming.com')
                    ->to($user->email);
            });

            return $this->respondCreated("Created user " . $user->username . " successfully");
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
                'data' => $this->_userTransformer->transform($user->toArray())
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
            $user->forename = $request->get('forename');
            $user->surname = $request->get('surname');

            $output = null;

            if ($request->get('has_dev_area') && !$user->has_dev_area) {
                $output = $this->runEnvoy(
                    "createDev --user=".
                    strtolower(substr($user->forename, 0, 1)).
                    strtolower($user->surname).
                    " --server=envoy@".Controller::DEV_SERVER
                );
            } elseif (!$request->get('has_dev_area') && $user->has_dev_area) {
                $output = $this->runEnvoy(
                    "removeDev --user=".
                    strtolower(substr($user->forename, 0, 1)).
                    strtolower($user->surname).
                    " --server=envoy@".Controller::DEV_SERVER
                );
            }

            if ($output['completed']) {
                $user->has_dev_area = $request->get('has_dev_area');
            }

            $user->save();

            if ($output) {
                $message = "User " . $user->username . " updated successfully. <br>";
                foreach ($output['message'] as $outputMessage) {
                    $message .= $outputMessage."<br>";
                }
                if ($output['completed']) {
                    return $this->respondUpdated($message);
                } else {
                    return $this->updateError($message);
                }
            } else {
                return $this->respondUpdated("User " . $user->username . " updated successfully");
            }
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

        try {
            /** @var User $user */
            $user = User::findOrFail($id);
            $user->delete();

            return $this->respondDeleted("User " . $user->username . " deleted");
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("User with ID of $id not found.");
        }

    }

    /**
     * Set the active status flag on a user
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse#
     */
    public function setActiveStatus($id) {

        try {
            /** @var User $user */
            $user = User::findOrFail($id);
            if ($user->active) {
                $user->active = false;
                $activeStatus = 'deactivated';
            } else {
                $user->active = true;
                $activeStatus = 'activated';
            }
            $user->save();

            return $this->respondUpdated("User " . $user->username . " " . $activeStatus);
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("User with ID of $id not found.");
        }

    }

    /**
     * Find users from given search term
     *
     * @param $searchTerm
     * @return \Illuminate\Http\JsonResponse
     */
    public function findUsers($searchTerm) {

        if ($searchTerm) {
            $users = User::where('forename', 'LIKE', "%$searchTerm%")->get();

            return $this->respond([
                'data' => $this->_userTransformer->transformCollection($users->toArray())
            ]);
        } else {
            return $this->respond([]);
        }

    }

    /**
     * Find all permissions assigned to the user and what sections those permissions relate to.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function findPermissionsForUser($id) {

        $user = User::with('groups.permissions.moduleSections', 'permissions.moduleSections')->whereId($id)->get();

        $permissions = [];

        foreach ($user[0]['permissions'] as $permissionKey => $permission) {
            $permissionMid = $this->_permissionTransformer->transform($permission);
            $permissionMid['module_sections'] = $this->_moduleSectionTransformer->transformCollection($permission['modulesections']->toArray());
            array_push($permissions, $permissionMid);
        }

        foreach ($user[0]['groups'] as $groupKey => $group) {
            foreach ($group['permissions'] as $permissionKey => $permission) {
                $permissionMid = $this->_permissionTransformer->transform($permission);
                $permissionMid['module_sections'] = $this->_moduleSectionTransformer->transformCollection($permission['modulesections']->toArray());
                array_push($permissions, $permissionMid);
            }
        }

        return $this->respond($permissions);

    }

}
