<?php

namespace imbalance\Http\Controllers\Projects;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use imbalance\Http\Controllers\Controller;
use imbalance\Http\Requests;
use imbalance\Http\Transformers\ProjectHistoryTransformer;
use imbalance\Http\Transformers\ProjectPackageCommandTransformer;
use imbalance\Http\Transformers\ProjectPackageTransformer;
use imbalance\Http\Transformers\ProjectTransformer;
use imbalance\Http\Transformers\ServerTransformer;
use imbalance\Http\Transformers\UserTransformer;
use imbalance\Models\Project;
use imbalance\Models\ProjectPackage;

class PackageController extends Controller {

    private $_projectPackageTransformer;
    private $_projectPackageCommandTransformer;

    function __construct() {
        $this->_projectPackageTransformer = new ProjectPackageTransformer();
        $this->_projectPackageCommandTransformer = new ProjectPackageCommandTransformer();
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

        $packages = ProjectPackage::with([
            'projectPackageCommands' => function($query) {
                $query->orderBy('order', 'asc');
            }
        ])->paginate($limit);

        $packageData = [];

        /** @var ProjectPackage $package */
        foreach ($packages->items() as $package) {
            $packageData[$package->id] = [
                'package' => $this->_projectPackageTransformer->transform($package),
                'commands' => $this->_projectPackageCommandTransformer->transformCollection($package->projectPackageCommands->toArray())
            ];
        }

        return $this->respondWithPagination($packages, $packageData);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        if (!$request->has(array('name', 'repository', 'deploy_branch', 'deploy_location', 'project_id'))) {
            return $this->parametersFailed('Parameters failed validation for a project package.');
        }

        try {
            ProjectPackage::whereName($request->get('name'))->firstOrFail();

            return $this->creationError('A project package with the name of ' . $request->get('name') . ' already exists.');
        } catch (ModelNotFoundException $e) {
            
            $data = [
                'name' => $request->get('name'),
                'repository' => $request->get('repository'),
                'deploy_branch' => $request->get('deploy_branch'),
                'deploy_location' => $request->get('deploy_location'),
                'project_id' => $request->get('project_id'),
            ];

            $projectPackage = ProjectPackage::create($data);

            return $this->respondCreated("Created project package " . $projectPackage->name . " successfully");
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
            /** @var ProjectPackage $package */
            $package = ProjectPackage::with([
                'projectPackageCommands' => function($query) {
                    $query->orderBy('project_package_command_type_id', 'asc')->orderBy('run_on', 'asc')->orderBy('order', 'asc');
                }
            ])->findOrFail($id);

            $data = [
                'package' => $this->_projectPackageTransformer->transform($package),
                'commands' => $this->_projectPackageCommandTransformer->transformCollection($package->projectPackageCommands->toArray())
            ];

            return $this->respond($data);
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("Project package with ID of $id not found.");
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

        if (!$request->has(array('name', 'repository', 'deploy_branch', 'deploy_location', 'project_id'))) {
            return $this->parametersFailed('Parameters failed validation for a project.');
        }

        try {
            /** @var ProjectPackage $projectPackage */
            $projectPackage = ProjectPackage::findOrFail($id);
            $projectPackage->name = $request->get('name');
            $projectPackage->repository = $request->get('repository');
            $projectPackage->deploy_branch = $request->get('deploy_branch');
            $projectPackage->deploy_location = $request->get('deploy_location');
            $projectPackage->project_id = $request->get('project_id');
            $projectPackage->save();

            return $this->respondUpdated("Project package " . $projectPackage->name . " updated successfully");
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("Project package with ID of $id not found.");
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
            /** @var ProjectPackage $projectPackage */
            $projectPackage = ProjectPackage::findOrFail($id);
            $projectPackage->delete();

            return $this->respondDeleted("Project package ". $projectPackage->name . " deleted");
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("Project package with ID of $id not found.");
        }

    }

}
