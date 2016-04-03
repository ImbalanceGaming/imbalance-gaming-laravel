<?php

namespace imbalance\Http\Controllers\Projects;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use imbalance\Http\Controllers\Controller;
use imbalance\Http\Requests;
use imbalance\Http\Transformers\GroupTransformer;
use imbalance\Http\Transformers\ProjectTransformer;
use imbalance\Http\Transformers\UserTransformer;
use imbalance\Models\Project;

class ProjectController extends Controller {

    private $_userTransformer;
    private $_projectTransformer;

    function __construct() {
        $this->_userTransformer = new UserTransformer();
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

        $projects = Project::paginate($limit);

        $projectData = [];

        /** @var Project $project */
        foreach ($projects->items() as $project) {
            $projectData[$project->id] = [
                'project' => $this->_projectTransformer->transform($project),
                'lead_user' => $this->_userTransformer->transform($project->leadUser)
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

        if (!$request->has(array('key', 'name', 'description', 'status'))) {
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
                'status' => $request->get('status'),
                'url' => $request->get('url'),
                'git_url' => $request->get('git_url')
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

        if (!$request->has(array('name', 'description', 'status'))) {
            return $this->parametersFailed('Parameters failed validation for a project.');
        }

        try {
            /** @var Project $project */
            $project = Project::findOrFail($id);
            $project->name = $request->get('name');
            $project->description = $request->get('description');
            $project->status = $request->get('status');
            $project->user_id = $request->get('user_id');
            $project->url = $request->get('url');
            $project->git_url = $request->get('git_url');
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
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deployProject($id) {

        try {
            /** @var Project $project */
            $project = Project::findOrFail($id);

            return $this->respondDeleted("Project [" . $project->key . "]" . $project->name . " deployed");
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
