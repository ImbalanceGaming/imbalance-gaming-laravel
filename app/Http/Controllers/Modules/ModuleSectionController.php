<?php

namespace imbalance\Http\Controllers\Modules;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use imbalance\Http\Controllers\Controller;
use imbalance\Http\Requests;
use imbalance\Http\Transformers\ModuleSectionTransformer;
use imbalance\Http\Transformers\ModuleTransformer;
use imbalance\Models\ModuleSection;

class ModuleSectionController extends Controller {

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

        $moduleSections = ModuleSection::paginate($limit);

        $moduleSectionsData = [];

        /** @var ModuleSection $moduleSection */
        foreach ($moduleSections->items() as $moduleSection) {
            $moduleSectionsData[$moduleSection->id] = [
                'module_section' => $this->_moduleSectionTransformer->transform($moduleSection),
                'module' => $this->_moduleTransformer->transformCollection(
                    $moduleSection->module->toArray()
                )
            ];
        }

        return $this->respondWithPagination($moduleSections, $moduleSectionsData);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        if (!$request->has(array('name', 'description', 'module_id'))) {
            return $this->parametersFailed('Parameters failed validation for a module section.');
        }

        try {
            ModuleSection::whereName($request->get('name'))->firstOrFail();

            return $this->creationError(
                'A module section with the name of ' . $request->get('name') . ' already exists.'
            );
        } catch (ModelNotFoundException $e) {

            $moduleSection = ModuleSection::create([
                'name' => $request->get('name'),
                'description' => $request->get('description'),
                'module_id' => $request->get('module_id')
            ]);

            return $this->respondCreated("Created module section " . $moduleSection->name . " successfully");
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
            /** @var ModuleSection $moduleSection */
            $moduleSection = ModuleSection::findOrFail($id);

            return $this->respond($this->_moduleSectionTransformer->transform($moduleSection));
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("Module section with ID of $id not found.");
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

        if (!$request->has(array('name', 'description', 'module_id'))) {
            return $this->parametersFailed('Parameters failed validation for a group.');
        }

        try {
            /** @var ModuleSection $moduleSection */
            $moduleSection = ModuleSection::findOrFail($id);
            $moduleSection->name = $request->get('name');
            $moduleSection->description = $request->get('description');
            $moduleSection->module_id = $request->get('module_id');
            $moduleSection->save();

            return $this->respondUpdated("Module section " . $moduleSection->name . " updated successfully");
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("Module section with ID of $id not found.");
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
            /** @var ModuleSection $moduleSection */
            $moduleSection = ModuleSection::findOrFail($id);
            $moduleSection->delete();

            return $this->respondDeleted("Module section " . $moduleSection->name . " deleted");
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound("Module section with ID of $id not found.");
        }

    }

    /**
     * Find module sections from given search term
     *
     * @param $searchTerm
     * @return \Illuminate\Http\JsonResponse
     */
    public function findModuleSections($searchTerm) {

        if ($searchTerm) {
            $moduleSections = ModuleSection::where('name', 'LIKE', "%$searchTerm%")->get();

            return $this->respond([
                'data' => $this->_moduleSectionTransformer->transformCollection($moduleSections->toArray())
            ]);
        } else {
            return $this->respond([]);
        }

    }

}
