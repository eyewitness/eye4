<?php

namespace Eyewitness\Eye\Witness;

use Cache;

class Request
{
    /**
     * Get all the request checks.
     *
     * @return array
     */
    public function check()
    {
        $data['requests'] = $this->getRequestHistory();

        return $data;
    }

    /**
     * Get the requests for the server for the past hour (if tracked).
     *
     * @return array
     */
    public function getRequestHistory()
    {
        for ($i=0; $i<2; $i++) {
            $tag = gmdate('Y_m_d_H', time() - (3600*$i));

            $history[$tag]['request_count'] = Cache::get('eyewitness_request_count_'.$tag, 0);
            $history[$tag]['total_execution_time'] = Cache::get('eyewitness_total_execution_time_'.$tag, 0);
        }

        return $history;
    }
}
