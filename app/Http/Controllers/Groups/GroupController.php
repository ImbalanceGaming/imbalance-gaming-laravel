<?php

namespace imbalance\Http\Controllers\Groups;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use imbalance\Http\Controllers\Controller;
use imbalance\Http\Requests;
use imbalance\Http\Transformers\GroupTransformer;
use imbalance\Http\Transformers\ProjectTransformer;
use imbalance\Http\Transformers\UserDetailsTransformer;
use imbalance\Http\Transformers\UserTransformer;
use imbalance\Models\Group;

class GroupController extends Controller {

    private $_userTransformer;
    private $_groupTransformer;
    private $_projectTransformer;

    function __construct() {
        $this->_userTransformer = new UserTransformer();
        $this->_groupTransformer = new GroupTransformer();
        $this->_projectTransformer = new ProjectTransformer();
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

        $groups = Group::paginate($limit);

        $groupData = [];

        /** @var Group $group */
        // @todo add projects stuff
        foreach ($groups->items() as $group) {
            $groupData[$group->id] = [
                'group' => $this->_groupTransformer->transform($group),
                'users' => $this->_userTransformer->transformCollection($group->users->toArray()),
                'projects' => $this->_projectTransformer->transformCollection($group->projects->toArray())
            ];
        }

        return $this->respondWithPagination($groups, $groupData);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        if (!$request->has(array('name', 'description'))) {
            return $this->parametersFailed('Parameters failed validation for a group.');
        }

        try {
            Group::whereName($request->get('name'))->firstOrFail();

            return $this->creationError('A group with the name of ' . $request->get('name') . ' already exists.');
        } catch (ModelNotFoundException $e) {

            $group = Group::create([
                'name' => $request->get('name'),
                'description' => $request->get('description')
            ]);

            return $this->respondCreated("Created group " . $group->name . " successfully");
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
            /** @var Group $group */
            $group = Group::findOrFail($id);

            return $this->respond($this->_groupTransformer->transform($group));
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("Group with ID of $id not found.");
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

        if (!$request->has(array('name', 'description', 'users', 'projects'))) {
            return $this->parametersFailed('Parameters failed validation for a group.');
        }

        try {
            /** @var Group $group */
            $group = Group::findOrFail($id);
            $group->name = $request->get('name');
            $group->description = $request->get('description');
            $group->save();

            $group->users()->sync($request->get('users'));
            $group->projects()->sync($request->get('projects'));

            return $this->respondUpdated("Group " . $group->name . " updated successfully");
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("Group with ID of $id not found.");
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
            /** @var Group $group */
            $group = Group::findOrFail($id);
            $group->delete();

            return $this->respondDeleted("Group " . $group->name . " deleted");
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("Group with ID of $id not found.");
        }

    }

    /**
     * Add a user to a group
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function addUserToGroup(Request $request, $id) {

        try {
            /** @var Group $group */
            $group = Group::findOrFail($id);

            $group->users()->attach($request->get('user_id'));

            return $this->respondUpdated("User added to group");
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("Group with ID of $id not found.");
        }
        
    }

    /**
     * Remove a user from a group
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeUserFromGroup(Request $request, $id) {

        try {
            /** @var Group $group */
            $group = Group::findOrFail($id);

            $group->users()->detach($request->get('user_id'));

            return $this->respondUpdated("User removed from group");
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("Group with ID of $id not found.");
        }

    }

    /**
     * Add a project to a group
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function addProjectToGroup(Request $request, $id) {

        try {
            /** @var Group $group */
            $group = Group::findOrFail($id);

            $group->projects()->attach($request->get('project_id'));

            return $this->respondUpdated("Project added to group");
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("Group with ID of $id not found.");
        }

    }

    /**
     * Remove a project from a group
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeProjectFromGroup(Request $request, $id) {

        try {
            /** @var Group $group */
            $group = Group::findOrFail($id);

            $group->projects()->detach($request->get('project_id'));

            return $this->respondUpdated("Project removed from group");
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("Group with ID of $id not found.");
        }

    }

}
