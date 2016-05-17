<?php

namespace imbalance\Http\Controllers\Projects;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use imbalance\Http\Controllers\Controller;
use imbalance\Http\Requests;
use imbalance\Http\Transformers\GroupTransformer;
use imbalance\Http\Transformers\ProjectHistoryTransformer;
use imbalance\Http\Transformers\ProjectPackageTransformer;
use imbalance\Http\Transformers\ProjectTransformer;
use imbalance\Http\Transformers\ServerTransformer;
use imbalance\Http\Transformers\UserTransformer;
use imbalance\Models\Project;
use imbalance\Models\ProjectDeploymentHistory;
use imbalance\Models\ProjectPackage;
use imbalance\Models\ProjectPackageCommand;
use imbalance\Models\Server;
use imbalance\Models\User;

class ProjectController extends Controller {

    private $_userTransformer;
    private $_projectTransformer;
    private $_projectPackageTransformer;
    private $_projectHistoryTransformer;
    private $_serverTransformer;

    function __construct() {
        $this->_userTransformer = new UserTransformer();
        $this->_projectTransformer = new ProjectTransformer();
        $this->_projectPackageTransformer = new ProjectPackageTransformer();
        $this->_projectHistoryTransformer = new ProjectHistoryTransformer();
        $this->_serverTransformer = new ServerTransformer();
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

        $projects = Project::with([
            'leadUser',
            'packages',
            'history' => function($query) {
                $query->orderBy('deployment_date', 'desc');
            },
            'servers'
        ])->paginate($limit);

        $projectData = [];

        /** @var Project $project */
        foreach ($projects->items() as $project) {
            $projectData[$project->id] = [
                'project' => $this->_projectTransformer->transform($project),
                'lead_user' => $this->_userTransformer->transform($project->leadUser),
                'project_packages' => $this->_projectPackageTransformer->transformCollection($project->packages->toArray()),
                'project_history' => $this->_projectHistoryTransformer->transformCollection($project->history->toArray()),
                'servers' => $this->_serverTransformer->transformCollection($project->servers->toArray())
            ];
        }

        return $this->respondWithPagination($projects, $projectData);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        if (!$request->has(array('key', 'name', 'description'))) {
            return $this->parametersFailed('Parameters failed validation for a project.');
        }

        try {
            Project::whereName($request->get('key'))->firstOrFail();

            return $this->creationError('A project with the key of ' . $request->get('key') . ' already exists.');
        } catch (ModelNotFoundException $e) {
            
            $data = [
                'key' => $request->get('key'),
                'name' => $request->get('name'),
                'description' => $request->get('description'),
                'url' => $request->get('url'),
            ];
            
            if ($request->get('lead_user_id')) {
                $data['user_id'] = $request->get('lead_user_id');
            }

            $project = Project::create($data);

            return $this->respondCreated("Created project [" . $project->key . "]" . $project->name . " successfully");
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
            /** @var Project $project */
            $project = Project::findOrFail($id);

            return $this->respond($this->_projectTransformer->transform($project));
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("Project with ID of $id not found.");
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

        if (!$request->has(array('name', 'description'))) {
            return $this->parametersFailed('Parameters failed validation for a project.');
        }

        try {
            /** @var Project $project */
            $project = Project::findOrFail($id);
            $project->name = $request->get('name');
            $project->description = $request->get('description');
            $project->user_id = $request->get('user_id');
            $project->url = $request->get('url');
            $project->user_id = $request->get('lead_user_id');
            $project->save();

            return $this->respondUpdated("Project [" . $project->key . "]" . $project->name . " updated successfully");
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("Project with ID of $id not found.");
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
            /** @var Project $project */
            $project = Project::findOrFail($id);
            $project->delete();

            return $this->respondDeleted("Project [" . $project->key . "]" . $project->name . " deleted");
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("Project with ID of $id not found.");
        }

    }

    /**
     * Deploy a project on the server
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deployProject(Request $request, $id) {

        try {
            /** @var Project $project */
            $project = Project::with(['packages' => function($query) {
                $query->orderBy('order', 'asc');
            }])->findOrFail($id);
            $serverId = $request->get('serverID');
            /** @var Server $server */
            $server = null;
            $firstRun = false;

            /** @var Server $serverObject */
            foreach ($project->servers as $serverObject) {
                if ($serverObject->id == $serverId) {
                    $server = $serverObject;
                    $firstRun = $server->pivot->first_run;
                }
            }

            /** @var User $user */
            $user = User::find($request->get('userID'));

            $history = ProjectDeploymentHistory::create([
                'deployment_date' => date('Y-m-d H:i:s'),
                'project_id' => $project->id,
                'user' => $user->forename." ".$user->surname,
                'server' => $server->name,
                'status' => 'In Progress'
            ]);

            $output = null;

            /** @var ProjectPackage $package */
            foreach ($project->packages as $package) {

                $outputTemp = null;

                if ($firstRun) {
                    $outputTemp = $this->runEnvoy(
                        'install --repo=' . $package->repository .
                        ' --deployLocation=' . $package->deploy_location .
                        ' --server=envoy@' . $server->address
                    );

                    if (sizeof($output) < 1) {
                        $output = $outputTemp;
                    } else {
                        array_merge($output['message'], $outputTemp['message']);
                    }
                }

                $this->runEnvoy(
                    'gitPull --deployLocation=' . $package->deploy_location.
                    ' --server=envoy@' . $server->address
                );

                /** @var ProjectPackageCommand $command */
                foreach ($package->projectPackageCommands as $command) {

                    $runCommand = false;

                    if ($firstRun && $command->run_on == 'install') {
                        $runCommand = true;
                    } elseif (!$firstRun && $command->run_on == 'update') {
                        $runCommand = true;
                    }

                    if ($runCommand) {
                        $outputTemp = $this->runEnvoy(
                            "runCommand --command='" . $command->command . "'".
                            " --deployLocation=" . $package->deploy_location .
                            " --server=envoy@" . $server->address
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

            if ($output['completed']) {
                $message = "Project [" . $project->key . "]" . $project->name . " deployed <br>";
            } else {
                $message = "Project [" . $project->key . "]" . $project->name . " failed to deploy <br>";
            }

            if (isset($output['message'])) {
                foreach ($output['message'] as $outputMessage) {
                    $message .= $outputMessage."<br>";
                }
            }

            if ($output['completed']) {
                $history->status = 'Finished';
                $history->save();
                if ($firstRun) {
                    $project->servers()->updateExistingPivot($serverId, ['first_run'=>false]);
                }

                return $this->respondUpdated($message);
            } else {
                $history->status = 'Failed';
                $history->save();
                return $this->updateError($message);
            }

        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("Project with ID of $id not found.");
        }

    }

    /**
     * Find projects from search term
     *
     * @param $searchTerm
     * @return \Illuminate\Http\JsonResponse
     */
    public function findProjects($searchTerm) {

        if ($searchTerm) {
            $projects = Project::where('name', 'LIKE', "%$searchTerm%")->get();

            return $this->respond([
                'data' => $this->_projectTransformer->transformCollection($projects->toArray())
            ]);
        } else {
            return $this->respond([]);
        }

    }
    
}
