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
use imbalance\Models\ProjectPackageCommand;
use imbalance\Models\ProjectPackageCommandType;

class PackageCommandController extends Controller {

    private $_projectPackageCommandTransformer;

    function __construct() {
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

        $packageCommands = ProjectPackageCommand::paginate($limit);

        $packageCommandData = [];

        /** @var ProjectPackage $package */
        foreach ($packageCommands->items() as $package) {
            $packageCommandData[$package->id] = [
                'commands' => $this->_projectPackageCommandTransformer->transformCollection($package->projectPackageCommands->toArray())
            ];
        }

        return $this->respondWithPagination($packageCommands, $packageCommandData);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        if (!$request->has(array('command', 'order', 'run_on', 'command_type', 'project_package_id'))) {
            return $this->parametersFailed('Parameters failed validation for a project package command.');
        }

        $data = [
            'command' => $request->get('command'),
            'order' => $request->get('order'),
            'run_on' => $request->get('run_on'),
            'project_package_command_type_id' => $request->get('command_type'),
            'project_package_id' => $request->get('project_package_id'),
        ];

        $packageCommand = ProjectPackageCommand::create($data);

        return $this->respondCreated("Created project package command " . $packageCommand->command . " successfully");

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {

        try {
            /** @var ProjectPackageCommand $packageCommand */
            $packageCommand = ProjectPackage::findOrFail($id);

            return $this->respond($this->_projectPackageCommandTransformer->transform($packageCommand));
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("Project package command with ID of $id not found.");
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

        if (!$request->has(array('command', 'order', 'run_on', 'command_type', 'project_package_id'))) {
            return $this->parametersFailed('Parameters failed validation for a project.');
        }

        try {
            /** @var ProjectPackageCommandType $commandType */
            $commandType = ProjectPackageCommandType::whereName($request->get('command_type'))->get();

            /** @var ProjectPackageCommand $packageCommand */
            $packageCommand = ProjectPackage::findOrFail($id);
            $packageCommand->command = $request->get('command');
            $packageCommand->order = $request->get('order');
            $packageCommand->run_on = $request->get('run_on');
            $packageCommand->project_package_command_type_id = $commandType[0]->id;
            $packageCommand->project_package_id = $request->get('project_package_id');
            $packageCommand->save();

            return $this->respondUpdated("Project package command " . $packageCommand->command . " updated successfully");
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("Project package command with ID of $id not found.");
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
            /** @var ProjectPackageCommand $packageCommand */
            $packageCommand = ProjectPackageCommand::findOrFail($id);
            $packageCommand->delete();

            return $this->respondDeleted("Project package command ". $packageCommand->command . " deleted");
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("Project package command with ID of $id not found.");
        }

    }

}
