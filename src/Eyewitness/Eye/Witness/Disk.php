<?php

namespace Eyewitness\Eye\Witness;

class Disk
{
    /**
     * Get all the disk checks.
     *
     * @return array
     */
    public function check()
    {
        $data['disk_space_total'] = $this->checkTotalDiskSpace();
        $data['disk_space_available'] = $this->checkDiskFreeSpace();

        return $data;
    }

    /**
     * Get the total disk space.
     *
     * @return int
     */
    public function checkTotalDiskSpace()
    {
        return disk_total_space(base_path())/1024/1024/1024;
    }

    /**
     * Get the available disk space.
     *
     * @return int
     */
    public function checkDiskFreeSpace()
    {
        return disk_free_space(base_path())/1024/1024/1024;
    }
}
