<?php

Class Cron {

    //connection instance
	private $connection;

    //path of tmp file
	private $path;

    // tmp file handle
	private $handle;

    // cron file
	private $cron_file;

    // month list array
    private $month_php;

    // weekdays array
    private $weekday_php;

    // minute array
	private $minute_php;

    // hours array
    private $hour_php;

    // days array
    private $day_php;

    /**
     * Main controller function
     *
     * @return void
     */
    public function view() {
        onapp_debug(__METHOD__);

        $action = onapp_get_arg('action');

        switch ($action) {
            case 'delete':
                $this->delete();
                break;
            case 'create':
                $this->create();
                break;
            case 'edit':
                $this->edit();
                break;
            default:
                $this->show_template_view( );
                break;
        }
    }

    /**
     * Sets month days and week days arrays
     *
     * @return void
     */
    private function setConstants() {
        onapp_debug(__METHOD__);

        $this->minute_php = array();
        $this->minute_php['*'] = '*';
        $this->minute_php['*/5'] = '*/5';
        $this->minute_php['0.30'] = '0.30';
        for ( $i=0; $i<=59; $i++ ) {
            $this->minute_php[$i] = $i;
        }

        $this->hour_php = array();
        $this->hour_php['*'] = '*';
        $this->hour_php['0.12'] = '0.12';
        for ( $i=0; $i<=23; $i++ ) {
            $this->hour_php[$i] = $i;
        }

        $this->day_php = array();
        $this->day_php['*'] = '*';
        for ( $i=1; $i<=31; $i++ ) {
            $this->day_php[$i] = $i. 'th';
        }

        $this->month_php = array(
            '*'=>    '*',
            '1'   =>    onapp_string('JANUARY_'),
            '2'   =>    onapp_string('FABRUARY_'),
            '3'   =>    onapp_string('MARCH_') ,
            '4'   =>    onapp_string('APRIL_') ,
            '5'   =>    onapp_string('MAY_')  ,
            '6'   =>    onapp_string('JUNE_') ,
            '7'   =>    onapp_string('JULY_') ,
            '8'   =>    onapp_string('AUGUST_') ,
            '9'   =>    onapp_string('SEPTEMBER_'),
            '10'  =>    onapp_string('OCTOBER_'),
            '11'  =>    onapp_string('NOVEMBER_'),
            '12'  =>    onapp_string('DECEMBER_')
            );

        $this->weekday_php = array(
            '*'     =>   '*',
            '0'     =>   onapp_string('MONDAY_'),
            '1'     =>   onapp_string('TUESDAY_'),
            '2'     =>   onapp_string('WEDNESDAY_'),
            '3'     =>   onapp_string('THURSDAY_'),
            '4'     =>   onapp_string('FRIDAY_'),
            '5'     =>   onapp_string('SATURDAY_'),
            '6'     =>   onapp_string('SUNDAY_')
        );
    }

    /**
     * Deletes cron job from cron file
     *
     * @global array $_ALIASES url aliases
     */
    private function delete() {
        onapp_debug(__METHOD__);
        global $_ALIASES;
        $this->ssh_connect (  );

        $this->remove_cronjob( urldecode(onapp_get_arg('cron_job')) );

        $_SESSION['message'] = 'CRON_JOB_HAS_BEEN_DELETED_SUCCESSFULLY';
        onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['cron_manager'] );
    }

    /**
     * Shows list of cron jobs
     *
     * @param string $error Error message
     * @return void
     */
    private function show_template_view ( $error = NULL ) {
        onapp_debug(__METHOD__);
        $this->ssh_connect (  );

        $cron_jobs = $this->get_cronjobs();

        onapp_debug( 'cron_jobs => ' . print_r( $cron_jobs, true) );

        foreach ( $cron_jobs as $cron_job ) {
           $cron_jobs_array[] = preg_split('/ /', $cron_job, -1, PREG_SPLIT_NO_EMPTY);
        }                                                                                  // print('<pre>');print_r($cron_jobs_array); die();

        foreach ( $cron_jobs_array as $key => $job ) {
            foreach ( $job as $k => $v ) {
                if ( $k > 5) {
                    $cron_jobs_array [$key][5] .= ' ' . $v;
                    unset( $cron_jobs_array [$key][$k] );
                }
            }
        }

        onapp_debug( 'cron_jobs_array  => ' . print_r( $cron_jobs_array, true ) );
                                                                                        // print('<pre>');print_r($cron_jobs_array); die();
        $this->setConstants();

        $params = array(
            'month_php'  => $this->month_php,
            'weekday_php'=> $this->weekday_php,
            'cron_jobs' => $cron_jobs,
            'cron_jobs_array'      =>  $cron_jobs_array,
            'title' => onapp_string('CRON_MANAGER'),
            'info_title' => onapp_string('CRON_MANAGER'),
            'info_body' => onapp_string('CRON_MANAGER_INFO'),
            'error' => $error,
        );

        onapp_show_template('cron_view', $params);
    }

    /**
     * Shows cron job create page
     *
     * @return void
     */
    private function show_template_create( $error = NULL ) {
        onapp_debug(__METHOD__);

        $this->setConstants();

        $params = array(
            'minute_php' => $this->minute_php,
            'hour_php'   => $this->hour_php,
            'day_php'    => $this->day_php,
            'month_php'  => $this->month_php,
            'weekday_php'=> $this->weekday_php,
            'title' => onapp_string('ADD_NEW_CRON_JOB'),
            'info_title' => onapp_string('ADD_NEW_CRON_JOB'),
            'info_body' => onapp_string('ADD_NEW_CRON_JOB_INFO'),
            'error' => $error,
        );
        onapp_show_template('cron_create', $params);
    }

    private function edit() {
        global $_ALIASES;
        $cron_jobs = array( urldecode(onapp_get_arg('cron_job')) );
        $cron = onapp_get_arg( 'cron' );

        if ( ! $cron ) {
            foreach ( $cron_jobs as $cron_job ) {
               $cron_jobs_array = preg_split('/ /', $cron_job, -1, PREG_SPLIT_NO_EMPTY);
            }                                                                                  // print('<pre>');print_r($cron_jobs_array); die();

            foreach ( $cron_jobs_array as $k => $v ) {
                if ( $k > 5) {
                    $cron_jobs_array[5] .= ' ' . $v;
                    unset( $cron_jobs_array[$k] );
                }
            }                                                                                    //print('<pre>');print_r($cron_jobs_array); die();

            $this->show_template_edit( $cron_jobs_array );
        }
        else {
            $cron_jobs_array = json_decode( urldecode( onapp_get_arg( 'cron_jobs_array' ) ) );

            $this->ssh_connect( );
            $this->remove_cronjob(
                $cron_jobs_array[0] . ' ' .
                $cron_jobs_array[1] . ' ' .
                $cron_jobs_array[2] . ' ' .
                $cron_jobs_array[3] . ' ' .
                $cron_jobs_array[4] . ' ' .
                $cron_jobs_array[5]
            );
            $this->append_cronjob(
                $cron['minute']. ' ' .
                $cron['hour'] . ' ' .
                $cron['day'] . ' ' .
                $cron['month'] . ' ' .
                $cron['weekday'] . ' ' .
                $cron['command']
            );

            $_SESSION['message'] = 'CRON_JOB_HAS_BEEN_UPDATED_SUCCESSFULLY';
            onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['cron_manager'] );

        }
    }

    private function show_template_edit ( $cron_jobs_array, $error = NULL ) {
        onapp_debug(__METHOD__);

        $this->setConstants();

        $params = array(
            'cron_jobs_array' => $cron_jobs_array,
            'minute_php' => $this->minute_php,
            'hour_php'   => $this->hour_php,
            'day_php'    => $this->day_php,
            'month_php'  => $this->month_php,
            'weekday_php'=> $this->weekday_php,
            'title' => onapp_string('EDIT_CRON_JOB'),
            'info_title' => onapp_string('EDIT_CRON_JOB'),
            'info_body' => onapp_string('EDIT_CRON_JOB_INFO'),
            'error' => $error,
        );
        onapp_show_template('cron_edit', $params);
    }

    /**
     * Creates new cron job in cron file
     *
     * @global array $_ALIASES url aliases
     * @return void
     */
    private function create (  ) {
        onapp_debug(__METHOD__);
        global $_ALIASES;
        $cron = onapp_get_arg('cron');

        if ( ! $cron ) {
            $this->show_template_create();
        }
        else {
            if ( $cron['command'] == '' ) {
                $error = onapp_string( 'COMMAND_FIELD_COULD_NOT_BE_EMPTY' );
                trigger_error ( $error );
                $this->show_template_create( $error );
                exit;
            }

            $this->ssh_connect ( );
            $this->append_cronjob(
                $cron['minute']. ' ' .
                $cron['hour'] . ' ' .
                $cron['day'] . ' ' .
                $cron['month'] . ' ' .
                $cron['weekday'] . ' ' .
                $cron['command']
            );

            $_SESSION['message'] = 'CRON_JOB_HAS_BEEN_CREATED_SUCCESSFULLY';
            onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['cron_manager'] );
        }
    }

    /**
     * Connects with SSH2
     *
     * @return Cron
     */
    public function ssh_connect ( ) {
        onapp_debug(__METHOD__);

        $host = ONAPP_SSH_HOST;
        $port = ONAPP_SSH_PORT;
        $username = ONAPP_SSH_USER;
        $password = ONAPP_SSH_PASSWORD;

        $path_length	 = strrpos(__FILE__, "/");
		$this->path 	 = ONAPP_PATH . ONAPP_DS;                                                              //substr(__FILE__, 0, $path_length) . '/';
		$this->handle	 = 'crontab.txt';
		$this->cron_file = "{$this->path}{$this->handle}";

		try
		{
			if ( $host == '' || $port == '' || $username == '' || $password == '' ) throw new Exception('CHECK_SSH_SETTINGS');

			$this->connection = ssh2_connect($host, $port);
			if ( ! $this->connection) throw new Exception('THE_SSH2_CONNECTION_COULD_NOT_BE_ESTABLISHED');

			$authentication = @ssh2_auth_password($this->connection, $username, $password);
			if ( ! $authentication) throw new Exception('COULD_NOT_AUTHENTICATE_USER');
		}
		catch (Exception $e)
		{
            $this->error_message( $e->getMessage() );
		}
    }

    /**
     * Execs terminal commands by SSH2 connection
     *
     * @return Cron
     */
	public function exec() {
        onapp_debug(__METHOD__);
		$argument_count = func_num_args();

		try
		{
			if ( ! $argument_count ) throw new Exception('THERE_IS_NOTHING_TO_EXECUTE');

			$arguments = func_get_args();

			$command_string = ($argument_count > 1) ? implode(" && ", $arguments) : $arguments[0];

            onapp_debug( '$command_string => ' . print_r( $command_string, true ) );

			$stream = @ssh2_exec($this->connection, $command_string);
			if ( ! $stream) throw new Exception("Unable to execute the specified commands: <br />{$command_string}");
		}
		catch (Exception $e)
		{
			$this->error_message( $e->getMessage() );
		}

		return $this;
	}

    /**
     * Creates tmp file and put there cron file content
     *
     * @param string $path path of tmp file
     * @param  mixed $handle file handle
     * @return Cron
     */
	public function write_to_file($path=NULL, $handle=NULL) {
        onapp_debug(__METHOD__);
        onapp_debug ( '$path => '. $path . ' $handle => ' . print_r( $handle, true ) );

		if ( ! $this->crontab_file_exists())
		{
			$this->handle = (is_null($handle)) ? $this->handle : $handle;
			$this->path   = (is_null($path))   ? $this->path   : $path;
			$this->cron_file = "{$this->path}{$this->handle}";

			$init_cron = "crontab -l > {$this->cron_file} && [ -f {$this->cron_file} ] || > {$this->cron_file}";

			$this->exec($init_cron);
		}

		return $this;
	}

    /**
     * Removes tmp file
     *
     * @return Cron
     */
	public function remove_file() {
        onapp_debug(__METHOD__);
        onapp_debug( 'tmp_file => ' . $this->cron_file );
		if ($this->crontab_file_exists()) $this->exec("rm {$this->cron_file}");
		return $this;
	}

    /**
     * Appends new cron job
     *
     * @param string $cron_jobs cron job string ( * * * * * command)
     * @return Cron
     */
	public function append_cronjob($cron_jobs=NULL) {
        onapp_debug(__METHOD__);
		if (is_null($cron_jobs)) {
            $this->error_message( 'NOTING_TO_APPEND');
        }

		$append_cronfile = "echo '";

		$append_cronfile .= (is_array($cron_jobs)) ? implode("\n", $cron_jobs) : $cron_jobs;

		$append_cronfile .= "'  >> {$this->cron_file}";

		$install_cron = "crontab {$this->cron_file}";

        onapp_debug( 'append_cronfile = >' . $append_cronfile .' $install_cron => ' . $install_cron );

		$this->write_to_file()->exec($append_cronfile, $install_cron)->remove_file();

		return $this;
	}

    /**
     * Gets list of cron jobs from the cron file
     *
     * @return array cron jobs array and boolean false if empty
     */
    public function get_cronjobs () {
        onapp_debug(__METHOD__);

        $this->write_to_file();
        $cron_jobs_array = file($this->cron_file, FILE_IGNORE_NEW_LINES);

        onapp_debug( '$cron_jobs_array => ' . print_r( $cron_jobs_array, true ) );

        $this->remove_file();

        if ( is_null( $cron_jobs_array ) ) {
            return false;
        }
        foreach ( $cron_jobs_array as $key => $cron_job) {
            if ( preg_match('/^\#/', $cron_job )) {
                unset( $cron_jobs_array[$key] );
            }
        }

        return $cron_jobs_array;
    }

    /**
     * Removes cron job string from the cron file
     *
     * @param string $cron_jobs cron job string
     * @return Cron
     */
	public function remove_cronjob($cron_jobs=NULL) {
        onapp_debug(__METHOD__);

		if (is_null($cron_jobs)) {
            $this->error_message("Nothing to remove!  Please specify a cron job or an array of cron jobs.");
        }

		$this->write_to_file();

		$cron_array = file($this->cron_file, FILE_IGNORE_NEW_LINES);                //print('<pre>'); print_r($cron_array); die();

        $count_before = count( $cron_array );

		if (empty($cron_array))
		{
			$this->remove_file()->error_message("Nothing to remove!  The cronTab is already empty.");
		}

		if (is_array($cron_jobs))
		{
			foreach ($cron_jobs as $cron_regex) $cron_array = preg_grep($cron_regex, $cron_array, PREG_GREP_INVERT);
		}
		else
		{
            foreach ( $cron_array as $key => $cron_job ) {
                if ( $cron_job == $cron_jobs ) {
                    unset( $cron_array[$key]);
                }
            }
		}

        $count_after = count( $cron_array );                                       // print('<pre>'); print_r($cron_array); die();
        if ( $count_after == $count_before ) {
            $this->remove_file();
            $error = onapp_string( 'CRON_JOB_HAS_NOT_BEEN_DELETED' );
            trigger_error ( $error );
            $this->show_template_view( $error );
            exit;
        }

        $this->remove_file();
        $this->remove_crontab();

        if ( ! empty( $cron_array ) ) {
            $this->append_cronjob($cron_array);
        }

        return $this;
	}

    /**
     * Cleans up cron file completely
     *
     * @return Cron
     */
	public function remove_crontab() {
        onapp_debug(__METHOD__);

		$this->exec("crontab -r");
		return $this;
	}

    /**
     * Verifies whether crontab file exists
     *
     * @return boolean whether crontab file exists
     */
	private function crontab_file_exists() {
        onapp_debug(__METHOD__);
		return file_exists($this->cron_file);
	}

    /**
     * Redirects to the profile page with the error message
     *
     * @param string $error error message
     * @return void
     */
	private function error_message( $error ) {
        onapp_debug(__METHOD__);

		$profile = new Profile();
        trigger_error ( $error );
        $profile->show_template_view( $error );
        exit;
	}

    /**
     * Checks permission for displaying MENU item
     *
     * @return boolean if has permission to see menu item
     */
    static function access() {
        onapp_debug(__METHOD__);

        $return = onapp_has_permission(array('roles'));
        onapp_debug('return => ' . $return);
        return $return;
    }

}
