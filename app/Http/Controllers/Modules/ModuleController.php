<?php

namespace imbalance\Http\Controllers\Modules;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use imbalance\Http\Requests;
use imbalance\Http\Transformers\ModuleSectionTransformer;
use imbalance\Models\Module;
use imbalance\Http\Transformers\ModuleTransformer;
use imbalance\Http\Controllers\Controller;

class ModuleController extends Controller {

    private $_moduleTransformer;
    private $_moduleSectionTransformer;

    function __construct() {
        $this->_moduleTransformer = new ModuleTransformer();
        $this->_moduleSectionTransformer = new ModuleSectionTransformer();
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

        $modules = Module::paginate($limit);
        
        $modulesData = [];

        /** @var Module $module */
        foreach ($modules->items() as $module) {
            $modulesData[$module->id] = [
                'module' => $this->_moduleTransformer->transform($module),
                'module_sections' => $this->_moduleSectionTransformer->transformCollection(
                    $module->moduleSections->toArray()
                )
            ];
        }

        return $this->respondWithPagination($modules, $modulesData);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        if (!$request->has(array('key', 'name', 'description'))) {
            return $this->parametersFailed('Parameters failed validation for a module.');
        }

        try {
            Module::whereKey($request->get('key'))->firstOrFail();

            return $this->creationError('A module with the key of ' . $request->get('key') . ' already exists.');
        } catch (ModelNotFoundException $e) {

            $module = Module::create([
                'key' => $request->get('key'),
                'name' => $request->get('name'),
                'description' => $request->get('description')
            ]);

            return $this->respondCreated("Created module [". $module->key . "] " . $module->name . " successfully");
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
            /** @var Module $group */
            $module = Module::findOrFail($id);

            return $this->respond($this->_moduleTransformer->transform($module));
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("Module with ID of $id not found.");
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

        if (!$request->has(array('description'))) {
            return $this->parametersFailed('Parameters failed validation for a module.');
        }

        try {
            /** @var Module $module */
            $module = Module::findOrFail($id);
            $module->description = $request->get('description');
            $module->save();


            return $this->respondUpdated("Module [". $module->key . "] " . $module->name . " updated successfully");
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("Module with ID of $id not found.");
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
            /** @var Module $module */
            $module = Module::findOrFail($id);
            $module->delete();

            return $this->respondDeleted("Module [". $module->key . "] " . $module->name . " deleted");
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("Module with ID of $id not found.");
        }

    }
}
