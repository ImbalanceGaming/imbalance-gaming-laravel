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
use imbalance\Models\Project;
use imbalance\Models\ProjectPackage;
use imbalance\Models\ProjectPackageCommand;
use imbalance\Models\Server;
use imbalance\Models\User;

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
     * Find groups from given search term
     *
     * @param $searchTerm
     * @return \Illuminate\Http\JsonResponse
     */
    public function findGroups($searchTerm) {

        if ($searchTerm) {
            $groups = Group::where('name', 'LIKE', "%$searchTerm%")->get();

            return $this->respond([
                'data' => $this->_groupTransformer->transformCollection($groups->toArray())
            ]);
        } else {
            return $this->respond([]);
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
            /** @var Project $projects */
            $projects = $group->projects;
            /** @var User $user */
            $user = User::find($request->get('user_id'));

            $deployResult = false;
            $message = 'User added to group';

            /** @var Project $project */
            foreach ($projects as $project) {
                if (sizeof($project->packages) > 0 && $user->has_dev_area) {
                    $deployResult = $this->deployProject($project, $user);
                }
            }

            if ($deployResult) {
                $message .= '<br /> Deployed project to user development environment';
            }

            $group->users()->attach($request->get('user_id'));

            return $this->respondUpdated($message);
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
            /** @var Project $projects */
            $projects = $group->projects;
            /** @var User $user */
            $user = User::find($request->get('user_id'));

            $deployResult = false;
            $message = 'User removed from group';

            /** @var Project $project */
            foreach ($projects as $project) {
                if (sizeof($project->packages) > 0 && $user->has_dev_area) {
                    $deployResult = $this->deployProject($project, $user, true);
                }
            }

            if ($deployResult) {
                $message .= "<br /> Removed project from user development environment";
            }

            $group->users()->detach($request->get('user_id'));

            return $this->respondUpdated($message);
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
            /** @var Project $project */
            $project = Project::find($request->get('project_id'));
            /** @var User $users */
            $users = $group->users;

            $deployResult = false;
            $message = 'Project added to group';

            /** @var User $user */
            foreach ($users as $user) {
                if (sizeof($project->packages) > 0 && $user->has_dev_area) {
                    $deployResult = $this->deployProject($project, $user);
                }
            }

            if ($deployResult) {
                $message .= "<br /> Deployed project to users development environments";
            }

            $group->projects()->attach($request->get('project_id'));

            return $this->respondUpdated($message);
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
            /** @var Project $project */
            $project = Project::find($request->get('project_id'));
            /** @var User $users */
            $users = $group->users;

            $deployResult = false;
            $message = 'Project removed from group';

            /** @var User $user */
            foreach ($users as $user) {
                if (sizeof($project->packages) > 0 && $user->has_dev_area) {
                    $deployResult = $this->deployProject($project, $user, true);
                }
            }

            if ($deployResult) {
                $message .= "<br /> Removed project from users development environments";
            }

            $group->projects()->detach($request->get('project_id'));

            return $this->respondUpdated($message);
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("Group with ID of $id not found.");
        }

    }

    /**
     * Deploy a project on the server
     *
     * @param Project $project
     * @param User $user
     * @param bool $deleteProject
     * @return \Illuminate\Http\JsonResponse
     * @internal param Request $request
     * @internal param $id
     */
    private function deployProject($project, $user, $deleteProject = false) {

        try {
            $output = null;

            if ($deleteProject) {
                $output = $this->runEnvoy(
                    "removeForDev ".
                    " --deployLocation=" . $project->packages[0]->deploy_location .
                    " --server=envoy@".Controller::DEV_SERVER .
                    " --user=".strtolower(substr($user->forename, 0, 1)).strtolower($user->surname)
                );
            } else {
                /** @var ProjectPackage $package */
                foreach ($project->packages as $package) {

                    $outputTemp = null;

                    $outputTemp = $this->runEnvoy(
                        'installForDev --repo=' . $package->repository .
                        ' --deployLocation=' . $package->deploy_location .
                        ' --server=envoy@'.Controller::DEV_SERVER .
                        " --user=".strtolower(substr($user->forename, 0, 1)).strtolower($user->surname)
                    );

                    if (sizeof($output) < 1) {
                        $output = $outputTemp;
                    } else {
                        array_merge($output['message'], $outputTemp['message']);
                    }

                    /** @var ProjectPackageCommand $command */
                    foreach ($package->projectPackageCommands as $command) {

                        $runCommand = false;

                        if ($command->run_on == 'install') {
                            $runCommand = true;
                        }

                        if ($runCommand) {
                            $outputTemp = $this->runEnvoy(
                                "runCommandForDev --command='" . $command->command . "'".
                                " --deployLocation=" . $package->deploy_location .
                                " --server=envoy@".Controller::DEV_SERVER .
                                " --user=".strtolower(substr($user->forename, 0, 1)).strtolower($user->surname)
                            );
                        }

                        if (isset($output['message'])) {
                            if (isset($outputTemp['message'])) {
                                array_merge($output['message'], $outputTemp['message']);
                            } else {
                                array_push($output['message'], $outputTemp);
                            }
                        } else {
                            $output = $outputTemp;
                        }

                    }

                    if (sizeof($output['message']) < 1) {
                        $output['completed'] = true;
                    }
                }
            }

            if ($output['completed']) {
                return true;
            } else {
                return false;
            }

        } catch (ModelNotFoundException $e) {
            return false;
        }

    }

}
