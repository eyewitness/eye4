<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Eyewitness.io
     |--------------------------------------------------------------------------
     |
     | Communication to the Eyewitness.io API is enabled by default. You can
     | override the value by setting to false, which might be useful for your
     | development environment so you do not trigger false alerts when
     | developing etc.
     |
     */

    'api_enabled' => true,
    'api_url' => 'https://eyew.io/api/v1',


    /*
     |--------------------------------------------------------------------------
     | Eyewitness.io Application Token & Secret Key
     |--------------------------------------------------------------------------
     |
     | Your unique Eyewitness.io application token & secret key. These settings
     | will be automatically set by the installer when you first run
     | 'artisan eyewitness:install'.
     |
     | WARNING: if you change either of these settings, you monitors will not
     | work correctly. Please contact us if you have any questions or need
     | help with your setup: 'support@eyewitness.io'
     |
     */

    'app_token' => 'APP_TOKEN_PLACEHOLDER',
    'secret_key' => 'SECRET_KEY_PLACEHOLDER',


    /*
     |--------------------------------------------------------------------------
     | Eyewitness.io Monitoring
     |--------------------------------------------------------------------------
     |
     | You can turn off certain parts of the monitoring. For example, if your
     | application does not use email, you should turn that off.
     |
     */

    'monitor_email' => true,
    'monitor_queue' => true,
    'monitor_scheduler' => true,
    'monitor_database' => true,
    'monitor_disk' => true,
    'monitor_request' => true,
    'monitor_log' => true,
    'monitor_composer_lock' => true,
    'monitor_maintenance_mode' => true,


    /*
     |--------------------------------------------------------------------------
     | Eyewitness.io Scheduler Task List
     |--------------------------------------------------------------------------
     |
     | As Laravel 4 has no default built in scheduler, there are many ways people
     | schedule their crons (often using either crontab or a package). To have
     | Eyewitness.io monitor specific cron jobs for your applications - simply
     | add the artisan command and cron schedule to the array below.
     |
     | There is no need to modify your actual command to ping or do anything. We
     | will automatically monitor the commands you put into the array without
     | any further changes to your application.
     |
     | This is an example of what you might put:
     |
     | 'scheduled_monitor_list' => ['backup:run' => '0 * * * *',
     |                              'weekly:report' => '5 7 * * 2',
     |                              'another:example' => '4 1 3 * *'],
     |
     | Not sure the best option for your application? Send us a quick email
     | at 'support@eyewitness.io' and we'll be happy to help.
     |
     */

     'scheduled_monitor_list' => [],


    /*
     |--------------------------------------------------------------------------
     | Eyewitness.io Queue Tube List
     |--------------------------------------------------------------------------
     |
     | If you run multiple queue tubes, please list them below for monitoring.
     | A sensible default has been configured for you as part of the installation
     | process that should work for most common applications.
     |
     | Below is a more advanced example of how you can monitor multiple queue
     | connections and tubes.
     |
     | 'queue_tube_list' => ['connection1' => ['tube1', 'tube2'],
     |                       'connection2' => ['tube1', 'tube2', 'tube3']],
     |
     | Not sure the best option for your application? Send us a quick email
     | at 'support@eyewitness.io' and we'll be happy to help.
     |
     */

    'queue_tube_list' => ['QUEUE_CONNECTION_PLACEHOLDER' => ['QUEUE_TUBE_PLACEHOLDER']],


    /*
     |--------------------------------------------------------------------------
     | Composer.lock location
     |--------------------------------------------------------------------------
     |
     | If you have enabled 'monitor_composer_lock' - then a daily check of
     | your composer.lock file will occur against the SensioLabs Security
     | check at https://security.sensiolabs.org/
     |
     | The below is the location of your composer.lock file. You only need to
     | modify this config if your lock file is in a different location than
     | the default location.
     |
     */

    'composer_lock_file_location' => base_path('composer.lock'),


    /*
     |--------------------------------------------------------------------------
     | Queued Emails
     |--------------------------------------------------------------------------
     |
     | When monitoring emails - the default is to send the test email immediately
     | and bypass the email queue. This is because if your application queues
     | a large amount of emails and process them over a long period of time,
     | the 'test' email will not be sent for a while, and that makes it difficult
     | to know if the emails are actually working or not.
     |
     | Because Eyewitness.io is already monitoring your queues seperately - there
     | is often no need for the test email to go via the queue, so it is set
     | to false by default.
     |
     | But depending on your application and specific circumstances - you might
     | want to force the email to still go via the queue - so we provide that
     | option.
     |
     */

    'send_queued_emails' => false,


    /*
     |--------------------------------------------------------------------------
     | Eyewitness.io Routes
     |--------------------------------------------------------------------------
     |
     | Eyewitness.io allows you to view your log files and queue information
     | from the Eyewitness.io website. This occurs via routing, so no information
     | is actually stored on the Eyewitness.io server.
     |
     | These routes are protected by your secret key - so they are not accessible
     | by unauthorized people.
     |
     | You can choose to turn these routes off if you do not use them.
     |
     */

    'routes_queue' => true,
    'routes_log' => true,

];
