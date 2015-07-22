<?php

namespace imbalance\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;

    /**
     * @var int
     */
    protected $statusCode = 200;

    /**
     * @return int
     */
    public function getStatusCode() {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode) {

        $this->statusCode = $statusCode;
        return $this;

    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondNotFound($message = 'Not Found') {

        return $this->setStatusCode(404)->respondWithError($message);

    }

    public function respond($data, $headers = []) {
        return \Response::json($data, $this->getStatusCode(), $headers);
    }

    public function respondWithError($message) {

        return $this->respond([
            'error' => [
                'message'   =>$message,
                'code'      =>$this->getStatusCode()
            ]
        ]);

    }

}
