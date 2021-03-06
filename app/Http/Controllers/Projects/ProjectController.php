<?php

namespace imbalance\Http\Controllers\Projects;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use imbalance\Http\Controllers\Controller;
use imbalance\Http\Transformers\ProjectHistoryTransformer;
use imbalance\Http\Transformers\ProjectPackageTransformer;
use imbalance\Http\Transformers\ProjectTransformer;
use imbalance\Http\Transformers\ServerTransformer;
use imbalance\Http\Transformers\UserTransformer;
use imbalance\Jobs\DeployProject;
use imbalance\Models\Project;
use imbalance\Models\ProjectDeploymentHistory;
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
                $query->limit(5);
            },
            'servers'
        ])->paginate($limit);

        $projectData = [];

        /** @var Project $project */
        foreach ($projects->items() as $project) {

            $projectHistory = $project->history;

            $deploymentStats = [
                'today' => 0,
                'week' => 0,
                'duration' => 0
            ];

            $day = date('w');
            $week_start = date('Y-m-d', strtotime('-'.$day.' days'));
            $week_end = date('Y-m-d', strtotime('+'.(6-$day).' days'));

            /** @var ProjectDeploymentHistory $history */
            foreach ($projectHistory as $history) {
                if ((time()-(60*60*24)) < strtotime($history->deployment_date)) {
                    $deploymentStats['today']++;
                }

                if ($this->check_in_range($week_start, $week_end, $history->deployment_date)) {
                    $deploymentStats['week']++;
                }
            }

            $projectData[$project->id] = [
                'project' => $this->_projectTransformer->transform($project),
                'lead_user' => $this->_userTransformer->transform($project->leadUser),
                'project_packages' => $this->_projectPackageTransformer->transformCollection($project->packages->toArray()),
                'project_history' => $this->_projectHistoryTransformer->transformCollection($project->history->toArray()),
                'servers' => $this->_serverTransformer->transformCollection($project->servers->toArray()),
                'deployment_stats' => $deploymentStats
            ];
        }

        return $this->respondWithPagination($projects, $projectData);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
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
     * @return \Illuminate\Http\JsonResponse
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
     * @return \Illuminate\Http\JsonResponse
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
     * @return \Illuminate\Http\JsonResponse
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
            /** @var Server $server */
            $server = null;
            $firstRun = false;

            /** @var Server $serverObject */
            foreach ($project->servers as $serverObject) {
                if ($serverObject->id == $request->get('serverID')) {
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

            $this->dispatch(new DeployProject($project, $server, $user, $history, $firstRun));

            return $this->respondWithSuccess("Deployment job for project [" . $project->key . "]" . $project->name . " queued");

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

    private function check_in_range($start_date, $end_date, $date_from_user) {
        // Convert to timestamp
        $start_ts = strtotime($start_date);
        $end_ts = strtotime($end_date);
        $user_ts = strtotime($date_from_user);

        // Check that user date is between start & end
        return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
    }
    
}
