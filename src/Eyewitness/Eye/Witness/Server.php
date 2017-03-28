<?php

namespace Eyewitness\Eye\Witness;

use Config;

class Server
{
    /**
     * Get all the server checks.
     *
     * @return array
     */
    public function check()
    {
        $laravel = app();
        $data['version_php'] = phpversion();
        $data['version_laravel'] = $laravel::VERSION;
        $data['reboot_required'] = $this->checkServerForReboot();
        $data['timezone'] = Config::get('app.timezone');

        return $data;
    }

    /**
     * Check if the server reporting if it needs a reboot.
     *
     * @return boolean
     */
    public function checkServerForReboot()
    {
        return file_exists('/var/run/reboot-required');
    }
}
