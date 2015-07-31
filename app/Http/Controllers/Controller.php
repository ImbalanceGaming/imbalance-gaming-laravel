<?php

namespace imbalance\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

/**
 * Class Controller
 * @package imbalance\Http\Controllers
 */
abstract class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;

    /**
     * @var int
     */
    private $statusCode = Illuminateresponse::HTTP_OK;

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
     * @param $data
     * @param array $headers
     * @return JsonResponse
     */
    public function respond($data, $headers = []) {
        return \Response::json($data, $this->getStatusCode(), $headers);
    }

    /**
     * @param $message
     * @return JsonResponse
     */
    private function respondWithError($message) {

        return $this->respond([
            'error' => [
                'message'   => $message,
                'code'      => $this->getStatusCode()
            ]
        ]);

    }

    /**
     * @param $message
     * @return JsonResponse
     */
    private function respondWithSuccess($message) {

        return $this->respond([
            'success' => [
                'message'   => $message,
                'code'      => $this->getStatusCode()
            ]
        ]);

    }

    // Errors

    /**
     * @param string $message
     * @return JsonResponse
     */
    protected function respondNotFound($message = 'Not Found') {

        return $this->setStatusCode(Illuminateresponse::HTTP_NOT_FOUND)->respondWithError($message);

    }

    /**
     * @param $message
     * @return JsonResponse
     */
    protected function parametersFailed($message) {
        return $this->setStatusCode(Illuminateresponse::HTTP_UNPROCESSABLE_ENTITY)->respondWithError($message);
    }

    /**
     * @param $message
     * @return JsonResponse
     */
    protected function userExists($message) {
        return $this->setStatusCode(Illuminateresponse::HTTP_UNPROCESSABLE_ENTITY)->respondWithError($message);
    }

    // Success

    /**
     *
     * @param $message
     * @return JsonResponse
     */
    protected function respondCreated($message) {

        return $this->setStatusCode(Illuminateresponse::HTTP_CREATED)->respondWithSuccess($message);

    }

}
