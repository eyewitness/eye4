<?php

namespace Eyewitness\Eye\Witness;

use Psr\Log\LogLevel;
use ReflectionClass;
use Exception;
use Cache;

class Log
{
    /**
     * Get all the log checks.
     *
     * @return array
     */
    public function check()
    {
        $data['logs'] = $this->getErrorHistory();

        return $data;
    }

    /**
     * Get the exception error and send to Eyewitness server.
     *
     * @return array
     */
    public function logError($level)
    {
        try {
            if (in_array($level, ['emergency', 'alert', 'critical', 'error'])) {
                $tag = gmdate('Y_m_d_H');
                Cache::add('eyewitness_error_count_'.$tag, 0, 100);
                Cache::increment('eyewitness_error_count_'.$tag, 1);
            }
        } catch (Exception $e) {
            // if we cant log - do nothing to prevent loops
        }
    }

    /**
     * Get the error count for the server for the past hours (if tracked).
     *
     * @return array
     */
    public function getErrorHistory()
    {
        for ($i=0; $i<2; $i++) {
            $tag = gmdate('Y_m_d_H', time() - (3600*$i));
            $history[$tag] = Cache::get('eyewitness_error_count_'.$tag, 0);
        }

        return $history;
    }

    /**
     * Get any/all log files in the log directory.
     *
     * @return array
     */
    public function getLogFiles()
    {
        $log_files = glob(storage_path('logs/*.{log,txt}'), GLOB_BRACE);
        $files = [];

        foreach (array_filter($log_files, 'is_file') as $id => $file) {
            $files[$id]['filename'] = basename($file);
            $files[$id]['size'] = filesize($file);
            $files[$id]['log_count'] = $this->getLogCount($file);
        }

        return $files;
    }

    /**
     * Get a list of log filenames.
     *
     * @return array
     */
    public function getLogFilenames()
    {
        $files = [];

        foreach (glob(storage_path('logs/*.{log,txt}'), GLOB_BRACE) as $file) {
            $files[] = basename($file);
        }

        return $files;
    }

    /**
     * Get a list of all log levels.
     *
     * @return array
     */
    public function getLogLevels()
    {
        $loglevels = new ReflectionClass(new LogLevel);
        return $loglevels->getConstants();
    }

    /**
     * Get a count of the number of logs in a file.
     *
     * @param  string  $file
     * @return array
     */
    public function getLogCount($file)
    {
        try {
            $fp = fopen($file, 'rb');
        } catch (Exception $e) {
            return 0;
        }

        $logs = substr_count(fread($fp, 5), "[20");
        rewind($fp);

        while (!feof($fp)) {
            $logs += substr_count(fread($fp, 8192), "\n[20");
        }

        fclose($fp);

        return $logs;
    }

    /**
     * Read a log file in reverse.
     *
     * @param  string  $filename
     * @param  int  $log_count_required
     * @param  int  $offset
     * @param  mixed  $start
     * @return array
     */
    public function readLogFile($filename, $log_count_required, $offset, $start = null)
    {
        $fp = fopen(storage_path('logs/'.$filename), "r");

        if (is_null($start)) {
            $start = filesize(storage_path('logs/'.$filename))-1;
        }

        $pos = $start - (is_null($offset) ? 0 : $offset);

        $logs = [];
        $current_log = [];
        $log_count = 0;
        $current_line = '';

        while (fseek($fp, $pos, SEEK_SET) !== -1) {
            $char = fgetc($fp);

            if (($char == "\n") && ($current_line !== '')) {
                $current_log[] = $current_line;
                if (starts_with($current_line, '[20')) {
                    $logs[$log_count] = $current_log;
                    $current_log = [];
                    $log_count++;
                }
                $current_line = '';
            } else {
                $current_line = $char . $current_line;
            }

            $pos--;

            if ($log_count >= $log_count_required) {
                break;
            }
        }

        if ($log_count < $log_count_required) {
            $current_log[] = $current_line;
            if (starts_with($current_line, '[20')) {
                $logs[$log_count] = $current_log;
            }
        }

        $reversed = [];

        foreach ($logs as $id => $log) {
            $main = array_pop($log);
            $error = $this->extractErrorLevel($main);
            $reversed[$id]['timestamp'] = $this->extractTimestamp($main);
            $reversed[$id]['level'] = strtolower($error);
            $reversed[$id]['error'] = $this->extractErrorMessage($main, $error);
            $reversed[$id]['stack_trace'] = array_reverse($log);
        }

        return ['logs' => $reversed, 'offset' => $pos, 'start' => $start];
    }

    /**
     * Get the timestamp from the log line.
     *
     * @param  string  $log
     * @return string
     */
    protected function extractTimestamp($log)
    {
        return ltrim(substr($log, 0, strpos($log, ']')),'[');
    }

    /**
     * Get the error level from the log line.
     *
     * @param  string  $log
     * @return string
     */
    protected function extractErrorLevel($log)
    {
        $start = ltrim(substr($log, strpos($log, "]") + 1));
        $end = substr($start, 0, strpos($start, ':'));
        return substr($end, strpos($end, ".") + 1);
    }

    /**
     * Get the error level from the log line.
     *
     * @param  string  $log
     * @param  string  $error
     * @return string
     */
    protected function extractErrorMessage($log, $error)
    {
        $log = ltrim(substr($log, strpos($log, $error)));
        return ltrim(substr($log, strpos($log, ':')+1));
    }
}
