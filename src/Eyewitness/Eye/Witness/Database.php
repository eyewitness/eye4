<?php

namespace Eyewitness\Eye\Witness;

use Exception;
use DB;

class Database
{
    /**
     * Get all the database checks.
     *
     * @return array
     */
    public function check()
    {
        $data['db_status'] = $this->checkDatabaseStatus();
        $data['db_size'] = $this->checkDatabaseSize();

        return $data;
    }

    /**
     * Check if the database connection is working.
     *
     * @return bool
     */
    public function checkDatabaseStatus()
    {
        try {
            DB::connection()->getPdo();
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Check the size of the database.
     *
     * @return bool
     */
    public function checkDatabaseSize()
    {
        switch(DB::getDriverName()) {
            case 'mysql':
                return $this->checkMySqlDatabaseSize();
            case 'sqlite':
                return $this->checkSqLiteDatabaseSize();
            case 'pgsql':
                return $this->checkPostgresDatabaseSize();
        }

        return 0;
    }

    /**
     * Return the size of the mySql database.
     *
     * @return int
     **/
    protected function checkMySqlDatabaseSize()
    {
        try {
            $result = DB::table('information_schema.TABLES')
                        ->select(DB::raw("sum( data_length + index_length ) AS size"))
                        ->where('table_schema', DB::getConfig('database'))
                        ->first();
        } catch (Exception $e) {
            return 0;
        }

        return $result->size ?: 0;
    }

    /**
     * Return the size of the Sqlite database.
     *
     * @return int
     **/
    protected function checkSqLiteDatabaseSize()
    {
        $db_size = 0;

        try {
            $path = realpath(DB::getConfig('database'));
            if ($path !== false) {
                $db_size = filesize($path);
            }
        } catch (Exception $e) {
            //
        }

        return $db_size;
    }

    /**
     * Return the size of the Postgres database.
     *
     * @return int
     **/
    protected function checkPostgresDatabaseSize()
    {
        try {
            $result = DB::select(DB::raw("select pg_database_size('".DB::getConfig('database')."')"));
        } catch (Exception $e) {
            return 0;
        }

        return $result[0]->pg_database_size;
    }
}
