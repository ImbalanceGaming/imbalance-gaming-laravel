<?php

namespace imbalance\Http\Controllers\Projects;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use imbalance\Http\Controllers\Controller;
use imbalance\Http\Requests;
use imbalance\Http\Transformers\ProjectPackageCommandTypeTransformer;
use imbalance\Models\ProjectPackageCommandType;

class PackageCommandTypeController extends Controller {

    private $_projectPackageCommandTypeTransformer;

    function __construct() {
        $this->_projectPackageCommandTypeTransformer = new ProjectPackageCommandTypeTransformer();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $packages = ProjectPackageCommandType::all();

        return $this->respond($this->_projectPackageCommandTypeTransformer->transformCollection($packages->toArray()));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {



    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {



    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {



    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {



    }

}
