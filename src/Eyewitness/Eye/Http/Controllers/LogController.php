<?php

namespace Eyewitness\Eye\Http\Controllers;

use Illuminate\Http\Request;
use Eyewitness\Eye\Eye;
use Controller;
use Exception;
use Validator;
use Response;

class LogController extends Controller
{
    /**
     * The log witness.
     *
     * @var \Eyewitness\Eye\Witness\Log
     */
    protected $log;

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Create a new LogController instance.
     *
     * @return void
     */
    public function __construct(Eye $eye, Request $request)
    {
        $this->beforeFilter('eyewitness_log_route');

        $this->log = $eye->log();

        $this->request = $request;
    }

    /**
     * Get the index of log files and their size.
     *
     * @return json
     */
    public function index()
    {
        return $this->jsonp(['log_files' => $this->log->getLogFiles()]);
    }

    /**
     * Show a specific log file from the beginning.
     *
     * @return json
     */
    public function show()
    {
        $validator = Validator::make($this->request->all(), [
            'filename' => 'required|string|min:3|max:60',
            'count' => 'required|integer|min:0',
            'start' => 'integer|min:0',
            'offset' => 'integer|min:0'
        ]);

        if ($validator->fails()) {
            return $this->jsonp(['error' => $validator->messages()->first()], 422);
        }

        if ( ! in_array($this->request->get('filename'), $this->log->getLogFilenames())) {
            return $this->jsonp(['error' => 'File not found'], 404);
        }

        return $this->jsonp($this->log->readLogFile($this->request->get('filename'),
                                                    $this->request->get('count'),
                                                    $this->request->get('offset'),
                                                    $this->request->get('start')));
    }

    /**
     * Delete the log file.
     *
     * @return json
     */
    public function delete()
    {
        $validator = Validator::make($this->request->all(), [
             'filename' => 'required|string|min:3|max:60',
        ]);

        if ($validator->fails()) {
            return $this->jsonp(['error' => $validator->messages()->first()], 422);
        }

        try {
            unlink(storage_path("logs/".$this->request->get('filename')));
        } catch (Exception $e) {
            return $this->jsonp(['error' => 'Log failed to delete: '.$e->getMessage()], 404);
        }

        return $this->jsonp(['msg' => 'Log deleted']);
    }

    /**
     * Return an optional JSONP response.
     *
     * @param  array   $data
     * @param  string  $status_code
     * @return json
     */
    protected function jsonp($data, $status_code = 200)
    {
        return Response::json($data, $status_code)->setCallback($this->request->input('callback'));
    }
}
