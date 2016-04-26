<?php

namespace imbalance\Http\Controllers\Permissions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use imbalance\Http\Controllers\Controller;
use imbalance\Http\Requests;
use imbalance\Http\Transformers\GroupTransformer;
use imbalance\Http\Transformers\ModuleSectionTransformer;
use imbalance\Http\Transformers\PermissionTransformer;
use imbalance\Http\Transformers\UserTransformer;
use imbalance\Models\Permission;

class PermissionController extends Controller {

    private $_permissionTransformer;
    private $_groupTransformer;
    private $_moduleSectionTransformer;
    private $_userTransformer;

    function __construct() {
        $this->_permissionTransformer = new PermissionTransformer();
        $this->_groupTransformer = new GroupTransformer();
        $this->_moduleSectionTransformer = new ModuleSectionTransformer();
        $this->_userTransformer = new UserTransformer();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $limit = \Input::get('limit')?:10;

        if ($limit > 20) {
            return $this->respondWithError('Pagination limit can not be above 20');
        }

        $permissions = Permission::paginate($limit);

        $permissionsData = [];

        /** @var Permission $permission */
        foreach ($permissions->items() as $permission) {
            $permissionsData[$permission->id] = [
                'permission' => $this->_permissionTransformer->transform($permission),
                'groups' => $this->_groupTransformer->transformCollection($permission->groups->toArray()),
                'module_sections' => $this->_moduleSectionTransformer->transformCollection($permission->moduleSections->toArray()),
                'users' => $this->_userTransformer->transformCollection($permission->users->toArray())
            ];
        }

        return $this->respondWithPagination($permissions, $permissionsData);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        if (!$request->has(array('name', 'description', 'view', 'add', 'edit', 'delete'))) {
            return $this->parametersFailed('Parameters failed validation for a permission.');
        }

        try {
            Permission::whereName($request->get('name'))->firstOrFail();

            return $this->creationError('A permission with the name of ' . $request->get('name') . ' already exists.');
        } catch (ModelNotFoundException $e) {

            $permission = Permission::create([
                'name' => $request->get('name'),
                'description' => $request->get('description'),
                'view' => $request->get('view'),
                'add' => $request->get('add'),
                'edit' => $request->get('edit'),
                'delete' => $request->get('delete')
            ]);

            return $this->respondCreated("Created permission " . $permission->name . " successfully");
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {

        try {
            /** @var Permission $permission */
            $permission = Permission::findOrFail($id);

            return $this->respond($this->_permissionTransformer->transform($permission));
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("Permission with ID of $id not found.");
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        if (!$request->has(array('name', 'description', 'view', 'add', 'edit', 'delete'))) {
            return $this->parametersFailed('Parameters failed validation for a permission.');
        }

        try {
            /** @var Permission $permission */
            $permission = Permission::findOrFail($id);
            $permission->name = $request->get('name');
            $permission->description = $request->get('description');
            $permission->view = $request->get('view');
            $permission->add = $request->get('add');
            $permission->edit = $request->get('edit');
            $permission->delete = $request->get('delete');

            $permission->save();

            return $this->respondUpdated("Permission " . $permission->name . " updated successfully");
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("Permission with ID of $id not found.");
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        try {
            /** @var Permission $permission */
            $permission = Permission::findOrFail($id);
            $permission->delete();

            return $this->respondDeleted("Permission " . $permission->name . " deleted");
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("Permission with ID of $id not found.");
        }

    }

    /**
     * Add a user to a group
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function addUserToPermission(Request $request, $id) {

        try {
            /** @var Permission $permission */
            $permission = Permission::findOrFail($id);

            $permission->users()->attach($request->get('user_id'));

            return $this->respondUpdated("User added to permission");
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("Permission with ID of $id not found.");
        }

    }

    /**
     * Remove a user from a group
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeUserFromPermission(Request $request, $id) {

        try {
            /** @var Permission $permission */
            $permission = Permission::findOrFail($id);

            $permission->users()->detach($request->get('user_id'));

            return $this->respondUpdated("User removed from permission");
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("Permission with ID of $id not found.");
        }

    }

    /**
     * Add a project to a group
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function addGroupToPermission(Request $request, $id) {

        try {
            /** @var Permission $permission */
            $permission = Permission::findOrFail($id);

            $permission->groups()->attach($request->get('group_id'));

            return $this->respondUpdated("Group added to permission");
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("Permission with ID of $id not found.");
        }

    }

    /**
     * Remove a project from a group
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeGroupFromPermission(Request $request, $id) {

        try {
            /** @var Permission $permission */
            $permission = Permission::findOrFail($id);

            $permission->groups()->detach($request->get('group_id'));

            return $this->respondUpdated("Group removed from permission");
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("Permission with ID of $id not found.");
        }

    }

    /**
     * Add a project to a group
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function addModuleSectionToPermission(Request $request, $id) {

        try {
            /** @var Permission $permission */
            $permission = Permission::findOrFail($id);

            $permission->moduleSections()->attach($request->get('module_section_id'));

            return $this->respondUpdated("Module section added to permission");
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("Permission with ID of $id not found.");
        }

    }

    /**
     * Remove a project from a group
     * Function name shortened due to preg_match character restrictions
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeSectionFromPermission(Request $request, $id) {

        try {
            /** @var Permission $permission */
            $permission = Permission::findOrFail($id);

            $permission->moduleSections()->detach($request->get('module_section_id'));

            return $this->respondUpdated("Module section removed from permission");
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("Permission with ID of $id not found.");
        }

    }

}
