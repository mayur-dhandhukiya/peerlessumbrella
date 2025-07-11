<?php

namespace TheLion\UseyourDrive;

/*
 * Helper functions for building a DataTables server-side processing SQL query
 *
 * The static functions in this class are just helper functions to help build
 * the SQL used in the DataTables demo server-side processing scripts. These
 * functions obviously do not represent all that can be done with server-side
 * processing, they are intentionally simple to show how it works. More complex
 * server-side processing operations will likely require a custom script.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */

// REMOVE THIS BLOCK - used for DataTables test environment only!
// $file = $_SERVER['DOCUMENT_ROOT'].'/datatables/mysql.php';
// if ( is_file( $file ) ) {
//     include( $file );
// }

class SSP
{
    /**
     * Create the data output array for the DataTables rows.
     *
     * @param array $columns Column information array
     * @param array $data    Data from the SQL get
     * @param bool  $isJoin  Determine the the JOIN/complex query or simple one
     *
     * @return array Formatted data in a row based format
     */
    public static function data_output($columns, $data, $isJoin = false)
    {
        $out = [];

        for ($i = 0, $ien = count($data); $i < $ien; ++$i) {
            $row = [];

            for ($j = 0, $jen = count($columns); $j < $jen; ++$j) {
                $column = $columns[$j];

                // Is there a formatter?
                if (isset($column['formatter'])) {
                    $row[$column['dt']] = ($isJoin) ? $column['formatter']($data[$i][$column['field']], $data[$i]) : $column['formatter']($data[$i][$column['db']], $data[$i]);
                } else {
                    $row[$column['dt']] = ($isJoin) ? $data[$i][$columns[$j]['field']] : $data[$i][$columns[$j]['db']];
                }
            }

            $out[] = $row;
        }

        return $out;
    }

    /**
     * Database connection.
     *
     * Obtain an PHP PDO connection from a connection details array
     *
     * @return resource PDO connection
     */
    public static function db()
    {
        return self::sql_connect();
    }

    /**
     * Paging.
     *
     * Construct the LIMIT clause for server-side processing SQL query
     *
     * @param array $request Data sent to server by DataTables
     * @param array $columns Column information array
     * @param mixed $limit
     *
     * @return string SQL limit clause
     */
    public static function limit($request, $columns, $limit = '')
    {
        if (isset($request['start']) && -1 != $request['length']) {
            $limit = 'LIMIT '.intval($request['start']).', '.intval($request['length']);
        }

        return $limit;
    }

    /**
     * Ordering.
     *
     * Construct the ORDER BY clause for server-side processing SQL query
     *
     * @param array $request Data sent to server by DataTables
     * @param array $columns Column information array
     * @param bool  $isJoin  Determine the the JOIN/complex query or simple one
     *
     * @return string SQL order by clause
     */
    public static function order($request, $columns, $isJoin = false)
    {
        $order = '';

        if (isset($request['order']) && count($request['order'])) {
            $orderBy = [];
            $dtColumns = SSP::pluck($columns, 'dt');

            for ($i = 0, $ien = count($request['order']); $i < $ien; ++$i) {
                // Convert the column index into the column data property
                $columnIdx = intval($request['order'][$i]['column']);
                $requestColumn = $request['columns'][$columnIdx];

                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];

                if ('true' == $requestColumn['orderable']) {
                    $dir = 'asc' === $request['order'][$i]['dir'] ?
                            'ASC' :
                            'DESC';

                    $columname = (isset($column['as'])) ? $column['as'] : $column['db'];
                    $orderBy[] = ($isJoin) ? $columname.' '.$dir : '`'.$columname.'` '.$dir;
                }
            }

            $order = 'ORDER BY '.implode(', ', $orderBy);
        }

        return $order;
    }

    /**
     * Searching / Filtering.
     *
     * Construct the WHERE clause for server-side processing SQL query.
     *
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here performance on large
     * databases would be very poor
     *
     * @param array $request  Data sent to server by DataTables
     * @param array $columns  Column information array
     * @param array $bindings Array of values for \PDO bindings, used in the sql_exec() function
     * @param bool  $isJoin   Determine the the JOIN/complex query or simple one
     *
     * @return string SQL where clause
     */
    public static function filter($request, $columns, &$bindings, $isJoin = false)
    {
        $globalSearch = [];
        $columnSearch = [];
        $dtColumns = SSP::pluck($columns, 'dt');

        if (isset($request['search']) && '' != $request['search']['value']) {
            $str = $request['search']['value'];

            for ($i = 0, $ien = count($request['columns']); $i < $ien; ++$i) {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];

                if ('true' == $requestColumn['searchable']) {
                    $binding = SSP::bind($bindings, '%'.$str.'%', \PDO::PARAM_STR);
                    $globalSearch[] = ($isJoin) ? $column['db'].' LIKE '.$binding : '`'.$column['db'].'` LIKE '.$binding;
                }
            }
        }

        // Individual column filtering
        for ($i = 0, $ien = count($request['columns']); $i < $ien; ++$i) {
            $requestColumn = $request['columns'][$i];
            $columnIdx = array_search($requestColumn['data'], $dtColumns);
            $column = $columns[$columnIdx];

            $str = $requestColumn['search']['value'];

            if ('true' == $requestColumn['searchable']
                    && '' != $str) {
                $binding = SSP::bind($bindings, '%'.$str.'%', \PDO::PARAM_STR);
                $columnSearch[] = ($isJoin) ? $column['db'].' LIKE '.$binding : '`'.$column['db'].'` LIKE '.$binding;
            }
        }

        // Combine the filters into a single string
        $where = '';

        if (count($globalSearch)) {
            $where = '('.implode(' OR ', $globalSearch).')';
        }

        if (count($columnSearch)) {
            $where = '' === $where ?
                    implode(' AND ', $columnSearch) :
                    $where.' AND '.implode(' AND ', $columnSearch);
        }

        if ('' !== $where) {
            $where = 'WHERE '.$where;
        }

        return $where;
    }

    /**
     * Perform the SQL queries needed for an server-side processing requested,
     * utilising the helper functions of this class, limit(), order() and
     * filter() among others. The returned array is ready to be encoded as JSON
     * in response to an SSP request, or can be modified if needed before
     * sending back to the client.
     *
     * @param array  $request    Data sent to server by DataTables
     * @param string $table      SQL table to query
     * @param string $primaryKey Primary key of the table
     * @param array  $columns    Column information array
     * @param array  $joinQuery  Join query String
     * @param string $extraWhere Where query String
     * @param string $groupBy    groupBy by any field will apply
     * @param string $having     HAVING by any condition will apply
     * @param mixed  $limit
     *
     * @return array Server-side processing response array
     */
    public static function simple($request, $table, $primaryKey, $columns, $joinQuery = null, $extraWhere = '', $groupBy = '', $having = '', $limit = '')
    {
        $bindings = [];

        $db = self::db();

        // Build the SQL query string from the request
        if (isset($request['start'])) {
            $limit = SSP::limit($request, $columns, $limit);
        }

        $order = '';
        if (isset($request['order'])) {
            $order = SSP::order($request, $columns, $joinQuery);
        }

        $where = '';
        if (isset($request['search'])) {
            $where = SSP::filter($request, $columns, $bindings, $joinQuery);
        }

        // IF Extra where set then set and prepare query
        if ($extraWhere) {
            $extraWhere = ($where) ? ' AND '.$extraWhere : ' WHERE '.$extraWhere;
        }

        $groupBy = ($groupBy) ? ' GROUP BY '.$groupBy.' ' : '';

        $having = ($having) ? ' HAVING '.$having.' ' : '';

        // Set TimeZone
        SSP::sql_exec($db, [], "SET SESSION time_zone = '+00:00';");

        // Main query to actually get the data
        if ($joinQuery && is_string($joinQuery)) {
            $col = SSP::pluck($columns, 'db', $joinQuery);
            $query = 'SELECT SQL_CALC_FOUND_ROWS '.implode(', ', $col)."
			 {$joinQuery}
			 {$where}
			 {$extraWhere}
			 {$groupBy}
       {$having}
			 {$order}
			 {$limit}";
        } else {
            $query = 'SELECT SQL_CALC_FOUND_ROWS '.implode(', ', SSP::pluck($columns, 'db', true))."
			 FROM `{$table}`
			 {$where}
			 {$extraWhere}
			 {$groupBy}
       {$having}
			 {$order}
			 {$limit}";
        }

        $data = SSP::sql_exec($db, $bindings, $query);

        // Data set length after filtering
        $resFilterLength = SSP::sql_exec(
            $db,
            'SELECT FOUND_ROWS()'
        );
        $recordsFiltered = $resFilterLength[0]['FOUND_ROWS()'];

        // Total data set length
        $resTotalLength = SSP::sql_exec(
            $db,
            "SELECT COUNT(`{$primaryKey}`)
			 FROM   `{$table}`"
        );
        $recordsTotal = $resTotalLength[0]["COUNT(`{$primaryKey}`)"];

        // Output
        return [
            'draw' => isset($request['draw']) ? intval($request['draw']) : '',
            'recordsTotal' => intval($recordsTotal),
            'recordsFiltered' => intval($recordsFiltered),
            'data' => SSP::data_output($columns, $data, $joinQuery),
        ];
    }

    /**
     * Connect to the database.
     *
     * @return resource Database connection handle
     */
    public static function sql_connect()
    {
        global $wpdb;

        return $wpdb;
    }

    /**
     * Execute an SQL query on the database.
     *
     * @param resource $db       Database handler
     * @param array    $bindings Array of \PDO binding values from bind() to be
     *                           used for safely escaping strings. Note that this can be given as the
     *                           SQL query string if no bindings are required.
     * @param string   $sql      SQL query to execute
     *
     * @return array Result from the query (all rows)
     */
    public static function sql_exec($db, $bindings, $sql = null)
    {
        // Argument shifting
        if (null === $sql) {
            $sql = $bindings;
        }

        // echo $sql;
        // Bind parameters
        if (is_array($bindings)) {
            for ($i = 0, $ien = count($bindings); $i < $ien; ++$i) {
                $binding = $bindings[$i];
                $sql = str_replace($binding['key'], '%s', $sql);
                $sql = $db->prepare($sql, $binding['val']);
            }
        }

        // Execute
        try {
            $result = $db->get_results($sql, 'ARRAY_A');
        } catch (\PDOException $e) {
            self::fatal('An SQL error occurred: '.$e->getMessage());
        }

        // Return all
        return $result;
    }

    // Internal methods

    /**
     * Throw a fatal error.
     *
     * This writes out an error message in a JSON string which DataTables will
     * see and show to the user in the browser.
     *
     * @param string $msg Message to send to the client
     */
    public static function fatal($msg)
    {
        echo json_encode([
            'error' => $msg,
        ]);

        exit(0);
    }

    /**
     * Create a \PDO binding key which can be used for escaping variables safely
     * when executing a query with sql_exec().
     *
     * @param array &$a Array of bindings
     * @param  *      $val  Value to bind
     * @param int $type \PDO field type
     *
     * @return string bound key to be used in the SQL where this parameter
     *                would be used
     */
    public static function bind(&$a, $val, $type)
    {
        $key = ':binding_'.count($a);

        $a[] = [
            'key' => $key,
            'val' => $val,
            'type' => $type,
        ];

        return $key;
    }

    /**
     * Pull a particular property from each assoc. array in a numeric array,
     * returning and array of the property values from each item.
     *
     * @param array  $a      Array to get data from
     * @param string $prop   Property to read
     * @param bool   $isJoin Determine the the JOIN/complex query or simple one
     *
     * @return array Array of property values
     */
    public static function pluck($a, $prop, $isJoin = false)
    {
        $out = [];

        for ($i = 0, $len = count($a); $i < $len; ++$i) {
            $out[] = ($isJoin && isset($a[$i]['as'])) ? $a[$i][$prop].' AS '.$a[$i]['as'] : $a[$i][$prop];
        }

        return $out;
    }
}
