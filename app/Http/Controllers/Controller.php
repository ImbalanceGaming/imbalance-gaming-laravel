<?php

namespace imbalance\Http\Controllers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
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
     * @param array $data
     * @param array $headers
     * @return JsonResponse
     */
    public function respond($data, $headers = []) {
        return \Response::json($data, $this->getStatusCode(), $headers);
    }

    /**
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
     * @param string $message
     * @return JsonResponse
     */
    protected function respondNotFound($message = 'Not Found') {
        return $this->setStatusCode(Illuminateresponse::HTTP_NOT_FOUND)->respondWithError($message);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    protected function parametersFailed($message) {
        return $this->setStatusCode(Illuminateresponse::HTTP_UNPROCESSABLE_ENTITY)->respondWithError($message);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    protected function creationError($message) {
        return $this->setStatusCode(Illuminateresponse::HTTP_UNPROCESSABLE_ENTITY)->respondWithError($message);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    protected function authError($message) {
        return $this->setStatusCode(IlluminateResponse::HTTP_UNAUTHORIZED)->respondWithError($message);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    protected function tokenError($message) {
        return $this->setStatusCode(IlluminateResponse::HTTP_INTERNAL_SERVER_ERROR)->respondWithError($message);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    protected function updateError($message) {
        return $this->setStatusCode(IlluminateResponse::HTTP_NOT_ACCEPTABLE)->respondWithError($message);
    }

    // Success

    /**
     * @param string $message
     * @return JsonResponse
     */
    protected function respondCreated($message) {
        return $this->setStatusCode(Illuminateresponse::HTTP_CREATED)->respondWithSuccess($message);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    protected function respondUpdated($message) {
        return $this->setStatusCode(Illuminateresponse::HTTP_OK)->respondWithSuccess($message);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    protected function respondDeleted($message) {
        return $this->setStatusCode(Illuminateresponse::HTTP_OK)->respondWithSuccess($message);
    }

    //Pagination
    /**
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
                'message' => $e->getMessage()
            ];
            return $output;
        }
    }

}
