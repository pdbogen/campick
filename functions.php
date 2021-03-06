<?php /*
Copyright (c) 2013 Patrick Bogen

This file is part of campick.

campick is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

campick is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with campick.  If not, see <http://www.gnu.org/licenses/>.
*/ ?>
<?php
	include_once( "dbspecs.php" );

	define( "CURRENT_VERSION", 1 );

	function do_index() {
		render( "index" );
	}

	function do_topcams() {
		render( "topcams" );
	}

	function do_reports() {
		require_admin();
		render( "reports" );
	}

	function do_set_mode() {
		if( !array_key_exists( "mode", $_POST ) || (
			$_POST[ "mode" ] != "virgin" && $_POST[ "mode" ] != "top10" && $_POST[ "mode" ] != "normal" )
		) {
			render( "nope" );
			exit;
		}
		$_SESSION[ "mode" ] = $_POST[ "mode" ];
		header( "Location: ".$_SERVER[ "PHP_SELF" ] );
		exit;
	}

	function do_report() {
		global $db;
		if( !array_key_exists( "url", $_POST ) ) {
			render( "nope" );
			exit;
		}
		$affected_rows = execute( "INSERT INTO reports VALUES (?,1) ON DUPLICATE KEY UPDATE reports = reports+1", "s", Array( $_POST[ "url" ] ), "report statement" );
		if( $affected_rows == 0 ) {
			throw new Exception( "error: report statement didn't seem to affect any rows" );
		}
		redirect_back();
		exit;
	}

	function do_nuke() {
		require_admin();
		global $db;
		if( !array_key_exists( "url", $_POST ) ) {
			render( "nope" );
			exit;
		}
		execute( "DELETE FROM cameras WHERE camera_url=?", "s", Array( $_POST[ "url" ] ), "nuke statement" );
		do_clear_reports();
	}

	function do_clear_reports() {
		require_admin();
		global $db;
		execute( "DELETE FROM reports WHERE url=?", "s", Array( $_POST[ "url" ] ), "clear_repots statement" );
		redirect_back();
		exit;
	}

	function do_bothsuck() {
		global $db;
		if( !array_key_exists( "url1", $_POST ) || !array_key_exists( "url2", $_POST ) ) {
			render( "nope" );
			exit;
		}
		execute( "UPDATE cameras SET camera_votes = camera_votes-1 WHERE camera_url=? OR camera_url=?", "ss", Array( $_POST[ "url1" ], $_POST[ "url2" ] ), "bothsuck statement" );
		header( "Location: ".$_SERVER[ "PHP_SELF" ] );
	}

	function do_vote() {
		global $db;
		if( !array_key_exists( "url", $_POST ) ) {
			render( "nope" );
			exit;
		}
		execute( "UPDATE cameras SET camera_votes = camera_votes+1 WHERE camera_url=?", "s", Array( $_POST[ "url" ] ), "vote statement" );
		header( "Location: ".$_SERVER[ "PHP_SELF" ] );
		exit;
	}

	function do_star() {
		global $db;
		check_admin();
		if( !array_key_exists( "url", $_POST ) ) {
			render( "nope" );
			exit;
		}
		execute( "UPDATE cameras SET starred = !starred WHERE camera_url=?", "s", Array( $_POST[ "url" ] ), "vote statement" );
		redirect_back();
		exit;
	}

	function do_firstrun() {
		$salt = hash( 'sha256', openssl_random_pseudo_bytes( 16 ) );
		$hash = hash( 'sha256', $salt.$_POST[ "admin_password" ] );
		config( "admin_salt", $salt );
		config( "admin_password", $hash );
		header( "Location: ".$_SERVER[ "REQUEST_URI" ] );
		$_SESSION[ "admin" ] = 1;
		print( "Cool. Now go over here." );
		return TRUE;
	}

	function do_logout() {
		session_regenerate_id( TRUE );
		header( "Location: ".$_SERVER[ "REQUEST_URI" ] );
		$_SESSION[ "admin" ] = 0;
		print( "Cool. Now go over here." );
		return TRUE;
	}

	function do_import() {
		require_admin();
		if( array_key_exists( "import", $_POST ) ) {
			cameras_clear();
			$cameras = explode( "\n", $_POST[ "import" ] );
			$start = microtime( TRUE );
			foreach( $cameras as $c ) {
				execute( "INSERT INTO camera (camera_url, camera_votes) VALUES (?,0)", "s", trim($c), "camera insert statement" );
			}
			error_log( "import duration: ".(microtime( TRUE ) - $start)."s" );
			header( "Location: ".$_SERVER[ "REQUEST_URI" ] );
			return TRUE;
		} else {
			render( "import" );
		}
	}

	function do_login() {
		$ip = $_SERVER[ "REMOTE_ADDR" ];
		if( config( "lockout_$ip" ) > time() ) {
			render( "nope" );
			error_log( "failed login attempt (locked out)" );
			return FALSE;
		}
		if( array_key_exists( "password", $_POST ) ) {
			$salt = config( "admin_salt" );
			if( hash( 'sha256', $salt.$_POST[ "password" ] ) == config( "admin_password" ) ) {
				config( "attempts_$ip", 0 );
				session_regenerate_id( TRUE );
				$_SESSION[ "admin" ] = 1;
				header( "Location: ".$_SERVER[ "REQUEST_URI" ] );
				return TRUE;
			} else {
				$attempts = config( "attempts_$ip" ) + 1;
				config( "attempts_$ip", $attempts );
				error_log( "failed login attempt (bad password)" );
				if( $attempts >= 3 ) {
					error_log( "locked out until ".(time()+300) );
					config( "lockout_$ip", (time()+300) );
				}
				render( "nope" );
			}
		} else {
			render( "nope" );
		}
	}

	function get_random_camera( $not = NULL ) {
		global $db;
		static $statement = NULL;
		if( $not !== NULL ) {
			if( $statement === NULL ) {
				if( !($statement = $db->prepare( "SELECT camera_url, camera_votes, starred FROM cameras WHERE camera_url != ? ORDER BY RAND() LIMIT 1" )) ) {
					throw new Exception( "failed to prepare statement to pick a second camera: ".$db->error );
				}
			}
			if( !($statement->bind_param( "s", $not )) ) {
				throw new Exception( "failed to bind param to statement to pick a second camera: ".$statement->error );
			}
			if( !($statement->execute()) ) {
				throw new Exception( "failed to execute statement to pick a second camera: ".$statement->error );
			}
			if( !($statement->bind_result( $ret_url, $ret_votes, $ret_starred )) ) {
				throw new Exception( "failed to bind result parameter to statement to pick a second camera: ".$statement->error );
			}
			if( ($ret = $statement->fetch()) === FALSE ) {
				throw new Exception( "failed to execute fetch on statement to pick a second camera: ".$statement->error );
			}
			if( $ret === NULL ) {
				return NULL;
			}
			$statement->free_result();
			return Array( $ret_url, $ret_votes, $ret_starred );
		} else {
			if( !($result = $db->query( "SELECT camera_url, camera_votes, starred FROM cameras ORDER BY RAND() LIMIT 1" )) ) {
				throw new Exception( "failed to pick a camera: ".$db->error );
			}
		}
		if( $result->num_rows == 0 ) {
			$result->free();
			return NULL;
		}
		$ret = $result->fetch_array();
		$ret = Array( $ret[0], $ret[1], $ret[2] );
		$result->free();
		return $ret;
	}

	function render( $whichView ) {
		global $view;
		$view = $whichView;
		include( "view.php" );
	}

	function check_admin() {
		global $admin;
		$admin = FALSE;
		if( array_key_exists( "admin", $_SESSION ) && $_SESSION[ "admin" ] === 1 ) {
			$admin = TRUE;
		}
	}

	function check_if_configured() {
		global $db;
		if( is_null( config( "admin_password" ) ) ) {
			print( "First run! Welcome! Please select an admin password.<br/>" );
			print( "<FORM action='campick.php' method='POST'><br/>" );
			print( "<input type='hidden' name='action' value='firstrun'>" );
			print( "<input type='password' name='admin_password' placeholder='Admin Password'>" );
			print( "</FORM>" );
			exit;
		}
	}

    function cameras_clear() {
    	global $db;
		if( !$db->real_query( "DELETE FROM cameras" ) ) {
			throw new Exception( "failed to clear cameras table: ".$db->error );
		}
    }

	function config( $key, $value = NULL ) {
		global $db;
		static $db_cache = Array();
		static $get_statement = NULL;
		static $set_statement = NULL;
		if( is_null( $value ) ) {
			if( !array_key_exists( $key, $db_cache ) ) {
				if( is_null( $get_statement ) ) {
					if( !($get_statement = $db->prepare( "SELECT config_value FROM config WHERE config_key=?" )) ) {
						throw new Exception( "error preparing config get statement: ".$db->error );
					}
				}
				if( !($get_statement->bind_param( "s", $key )) ) {
					throw new Exception( "error binding key param to get statement: ".$get_statement->error );
				}
				if( !($get_statement->execute()) ) {
					throw new Exception( "error executing config get statement: ".$get_statement->error );
				}
				if( !($get_statement->bind_result($val)) ) {
					throw new Exception( "error binding result to config get statement: ".$get_statement->error );
				}
				if( $fetch_result = $get_statement->fetch() === FALSE ) {
					throw new Exception( "error fetching result for config get statement: ".$get_statement->error );
				}
				$get_statement->free_result();
				if( $fetch_result === NULL ) {
					$val = NULL;
				}
				$db_cache[ $key ] = $val;
			}
		} else {
			execute( "INSERT INTO config VALUES (?,?) ON DUPLICATE KEY UPDATE config_value=?", "sss", Array( $key, $value, $value ), "config set statement" );
			$db_cache[ $key ] = $value;
		}
		return $db_cache[ $key ];
	}

	function select( $sql, $types = NULL, $args = NULL, $desc = "unknown query" ) {
		global $db;
		static $st_cache = Array();
		if( array_key_exists( $sql, $st_cache ) ) {
			$statement = $st_cache[ $sql ];
		} else {
			if( !($statement = $db->prepare( $sql ) ) ) {
				throw new Exception( "failed to prepare $desc ($sql): ".$db->error );
			}
			$st_cache[ $sql ] = $statement;
		}
		if( $types !== NULL ) {
			$reflection = new ReflectionClass( "mysqli_stmt" );
			$method     = $reflection->getMethod( "bind_param" );
			$invoke_args = Array( $types );
			foreach( $args as $k=>$v ) {
				$invoke_args[] =& $args[$k];
			}
			if( !($method->invokeArgs( $statement, $invoke_args )) ) {
				throw new Exception( "error binding params to $desc ($sql): ".$statement->error );
			}
		}
		if( !($statement->execute()) ) {
			throw new Exception( "error executing config $desc ($sql): ".$statement->error );
		}
		$results = Array();
		$call = Array();
		for( $i = 0; $i < $statement->field_count; $i++ ) {
			$results[$i] = NULL;
			$call[$i] =& $results[$i];
		}
		call_user_func_array( array( $statement, 'bind_result' ), $call );
		$return = Array();
		while( $statement->fetch() ) {
			$tmp = Array();
			foreach( $results as $v ) {
				array_push( $tmp, $v );
			}
			$return[] = $tmp;
		}
		$statement->free_result();
		return $return;
	}

	function execute( $sql, $types = NULL, $args = NULL, $desc = "unknown query" ) {
		global $db;
		static $st_cache = Array();
		if( array_key_exists( $sql, $st_cache ) ) {
			$statement = $st_cache[ $sql ];
		} else {
			if( !($statement = $db->prepare( $sql ) ) ) {
				throw new Exception( "failed to prepare $desc ($sql): ".$db->error );
			}
			$st_cache[ $sql ] = $statement;
		}
		if( $types !== NULL ) {
			$reflection = new ReflectionClass( "mysqli_stmt" );
			$method     = $reflection->getMethod( "bind_param" );
			$invoke_args = Array( $types );
			foreach( $args as $k=>$v ) {
				$invoke_args[] =& $args[$k];
			}
			if( !($method->invokeArgs( $statement, $invoke_args )) ) {
				throw new Exception( "error binding params to $desc ($sql): ".$statement->error );
			}
		}
		if( !($statement->execute()) ) {
			throw new Exception( "error executing config $desc ($sql): ".$statement->error );
		}
		return $statement->affected_rows;
	}

	function upgrade_db() {
		$version = config( "db_version" );
		if( $version == NULL ) {
			config( "db_version", 1 );
			execute( "ALTER TABLE cameras ADD COLUMN starred BOOLEAN DEFAULT false" );
		}
	}

	function load_or_create_db( ) {
		global $db, $db_host, $db_user, $db_pass, $db_db;
		$db = new mysqli( $db_host, $db_user, $db_pass, $db_db );
		if( $db->connect_errno != 0 ) {
			throw new Exception( "failed to connect to database: ".$db->connect_error );
		}
		$shm_key = ftok( __FILE__, 't' );
		$shm_id = shmop_open( $shm_key, "a", 0, 0 );
		if( $shm_id === FALSE ) {
			$shm_id = shmop_open( $shm_key, "c", 0600, 1 );
			error_log( "no shared memory space, checking if we need to initialize DB" );
			if( !$db->real_query( "CREATE TABLE IF NOT EXISTS config ( config_key VARCHAR(128) PRIMARY KEY, config_value TEXT )" ) ) {
				throw new Exception( "failed to create config table: ".$db->error );
			}
			if( !$db->real_query( "CREATE TABLE IF NOT EXISTS cameras ( camera_id SERIAL PRIMARY KEY, camera_url TEXT , camera_votes INTEGER )" ) ) {
				throw new Exception( "failed to create cameras table: ".$db->error );
			}
			if( !$db->real_query( "CREATE TABLE IF NOT EXISTS reports ( url TEXT, reports INTEGER )" ) ) {
				throw new Exception( "failed to create reports table: ".$db->error );
			}
		}
		shmop_close( $shm_id );
		return $db;
	}

	function require_admin() {
		global $admin;
		if( !$admin ) { render( "nope" ); exit; }
	}

	function init_session() {
		session_start();
		if( !array_key_exists( "mode", $_SESSION ) ) {
			$_SESSION[ "mode" ] = "normal";
		}
	}

	function route_request() {
		if( array_key_exists( "action", $_REQUEST ) ) {
			$func = "do_".$_REQUEST[ "action" ];
			if( is_callable( $func ) ) {
				call_user_func( $func );
				exit;
			} else {
				render( "nope" );
				throw new Exception( "user tried to call do_".$_REQUEST[ "action" ].", which doesn't exist" );
			}
		}
	}

	function redirect_back() {
		if( array_key_exists( "back", $_POST ) ) {
			if( $_POST[ "back" ] == "reports" || $_POST[ "back" ] == "topcams" ) {
				header( "Location: ".$_SERVER[ "PHP_SELF" ]."?action=".$_POST[ "back" ]."&offset=".urlencode( $_SESSION[ "offset" ] ) );
			} elseif( $_POST[ "back" ] == "bottomcams" ) {
				header( "Location: ".$_SERVER[ "PHP_SELF" ]."?action=topcams&bottom&offset=".urlencode( $_SESSION[ "offset" ] ) );
			} else {
				header( "Location: ".$_SERVER[ "PHP_SELF" ] );
			}
		} else {
			header( "Location: ".$_SERVER[ "PHP_SELF" ] );
		}
	}
?>
