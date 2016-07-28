<?php

namespace imbalance\Http\Controllers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Class Controller
 * @package imbalance\Http\Controllers
 */
abstract class Controller extends BaseController {

    use DispatchesJobs, ValidatesRequests;
    const DEV_SERVER = '192.168.2.4';

    /**
     * @var int
     */
    private $statusCode = Illuminateresponse::HTTP_OK;

    /**
     * Get currently set HTTP status code
     *
     * @return int
     */
    public function getStatusCode() {
        return $this->statusCode;
    }

    /**
     * Set HTTP status code
     *
     * @param int $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode) {

        $this->statusCode = $statusCode;
        return $this;

    }

    /**
     * Send response back to caller in a JSON format
     *
     * @param array $data
     * @param array $headers
     * @return JsonResponse
     */
    public function respond($data, $headers = []) {
        return \Response::json($data, $this->getStatusCode(), $headers);
    }

    /**
     * Send error response to caller
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function respondWithError($message) {

        return $this->respond([
            'error' => [
                'message'   => $message,
                'code'      => $this->getStatusCode()
            ]
        ]);

    }

    /**
     * Send success response to caller
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function respondWithSuccess($message) {

        return $this->respond([
            'success' => [
                'message'   => $message,
                'code'      => $this->getStatusCode()
            ]
        ]);

    }

    // Errors

    /**
     * Respond to called with HTTP_NOT_FOUND error code
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function respondNotFound($message = 'Not Found') {
        return $this->setStatusCode(Illuminateresponse::HTTP_NOT_FOUND)->respondWithError($message);
    }

    /**
     * Respond to called with HTTP_UNPROCESSABLE_ENTITY error code
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function parametersFailed($message) {
        return $this->setStatusCode(Illuminateresponse::HTTP_UNPROCESSABLE_ENTITY)->respondWithError($message);
    }

    /**
     * Respond to called with HTTP_UNPROCESSABLE_ENTITY error code
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function creationError($message) {
        return $this->setStatusCode(Illuminateresponse::HTTP_UNPROCESSABLE_ENTITY)->respondWithError($message);
    }

    /**
     * Respond to called with HTTP_UNAUTHORIZED error code
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function authError($message) {
        return $this->setStatusCode(IlluminateResponse::HTTP_UNAUTHORIZED)->respondWithError($message);
    }

    /**
     * Respond to called with HTTP_UNAUTHORIZED error code
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function tokenError($message) {
        return $this->setStatusCode(IlluminateResponse::HTTP_UNAUTHORIZED)->respondWithError($message);
    }

    /**
     * Respond to called with HTTP_NOT_ACCEPTABLE error code
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function updateError($message) {
        return $this->setStatusCode(IlluminateResponse::HTTP_NOT_ACCEPTABLE)->respondWithError($message);
    }

    // Success

    /**
     * Respond to called with HTTP_CREATED success code
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function respondCreated($message) {
        return $this->setStatusCode(Illuminateresponse::HTTP_CREATED)->respondWithSuccess($message);
    }

    /**
     * Respond to called with HTTP_OK success code
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function respondUpdated($message) {
        return $this->setStatusCode(Illuminateresponse::HTTP_OK)->respondWithSuccess($message);
    }

    /**
     * Respond to called with HTTP_OK success code
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function respondDeleted($message) {
        return $this->setStatusCode(Illuminateresponse::HTTP_OK)->respondWithSuccess($message);
    }

    //Pagination
    /**
     * Respond to called with pagination data attached
     *
     * @param LengthAwarePaginator $paginator
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithPagination(LengthAwarePaginator $paginator, $data) {

        return $this->respond([
            'data' => $data,
            'paginator' => [
                'per_page' => $paginator->perPage(),
                'last_page' => $paginator->lastPage(),
                'current_page' => $paginator->currentPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem()
            ]
        ]);
    }

    /**
     * Run envoy command via SSH
     *
     * @param string $task
     * @return array
     */
    protected function runEnvoy($task) {

        $output = [
            'completed' => false,
            'message' => []
        ];

        $directory = base_path();
        $process = new Process("/var/www/.config/composer/vendor/bin/envoy run $task");
        $process->setTimeout(3600);
        $process->setIdleTimeout(300);
        $process->setWorkingDirectory($directory);

        try {
            $process->mustRun(function ($type, $buffer) use (&$output) {
                if (Process::ERR === $type) {
                    $output['completed'] = false;
                } else {
                    $output['completed'] = true;
                }
                array_push($output['message'], $buffer);
            });
            return $output;
        } catch (ProcessFailedException $e) {
            $output = [
                'completed' => false,
                'message' => [$e->getMessage()]
            ];
            return $output;
        }
    }

}
