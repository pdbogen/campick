<?php
	$db_user = "campick";
	$db_pass = "im3ahKae5ruGhaeY";
	$db_db   = "campick";
	$db_host = "localhost";
	$db = load_or_create_db( );

	session_start();
	check_admin();

	if( array_key_exists( "action", $_REQUEST ) ) {
		$func = "do_".$_REQUEST[ "action" ];
		if( is_callable( $func ) ) {
			return call_user_func( $func );
		} else {
			print( "nope." );
			throw new Exception( "user tried to call do_".$_REQUEST[ "action" ].", which doesn't exist" );
		}
	}

	check_if_configured();
	do_index();

	function do_index() {
		render( "index" );
	}

	function do_topcams() {
		render( "topcams" );
	}

	function do_vote() {
		global $db;
		if( !array_key_exists( "url", $_POST ) ) {
			render( "nope" );
			exit;
		}
		if( !($statement = $db->prepare( "UPDATE cameras SET camera_votes = camera_votes+1 WHERE camera_url=?" )) ) {
			throw new Exception( "failed to prepare vote statement: ".$db->error );
		}
		if( !($statement->bind_param( "s", $_POST[ "url" ] )) ) {
			throw new Exception( "error binding params to vote statement: ".$db->error );
		}
		if( !($statement->execute()) ) {
			throw new Exception( "error executing config vote statement: ".$db->error );
		}
		if( $statement->affected_rows == 0 ) {
			throw new Exception( "error: vote statement didn't seem to affect any rows: ".$db->error );
		}
		header( "Location: ".$_SERVER[ "PHP_SELF" ] );
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
				camera_add( trim($c) );
			}
			error_log( "import duration: ".(microtime( TRUE ) - $start)."s" );
			header( "Location: ".$_SERVER[ "REQUEST_URI" ] );
			return TRUE;
		} else {
			render( "import" );
		}
	}

	function do_login() {
		if( array_key_exists( "password", $_POST ) ) {
			$salt = config( "admin_salt" );
			if( hash( 'sha256', $salt.$_POST[ "password" ] ) == config( "admin_password" ) ) {
				session_regenerate_id( TRUE );
				$_SESSION[ "admin" ] = 1;
				header( "Location: ".$_SERVER[ "REQUEST_URI" ] );
				return TRUE;
			} else {
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
				if( !($statement = $db->prepare( "SELECT camera_url, camera_votes FROM cameras WHERE camera_url != ? ORDER BY RAND() LIMIT 1" )) ) {
					throw new Exception( "failed to prepare statement to pick a second camera: ".$db->error );
				}
			}
			if( !($statement->bind_param( "s", $not )) ) {
				throw new Exception( "failed to bind param to statement to pick a second camera: ".$db->error );
			}
			if( !($statement->execute()) ) {
				throw new Exception( "failed to execute statement to pick a second camera: ".$db->error );
			}
			if( !($statement->bind_result( $ret_url, $ret_votes )) ) {
				throw new Exception( "failed to bind result parameter to statement to pick a second camera: ".$db->error );
			}
			if( ($ret = $statement->fetch()) === FALSE ) {
				throw new Exception( "failed to execute fetch on statement to pick a second camera: ".$db->error );
			}
			if( $ret === NULL ) {
				return NULL;
			}
			$statement->free_result();
			return Array( $ret_url, $ret_votes );
		} else {
			if( !($result = $db->query( "SELECT camera_url, camera_votes FROM cameras ORDER BY RAND() LIMIT 1" )) ) {
				throw new Exception( "failed to pick a camera: ".$db->error );
			}
		}
		if( $result->num_rows == 0 ) {
			$result->free();
			return NULL;
		}
		$ret = $result->fetch_array();
		$ret = Array( $ret[0], $ret[1] );
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

	function camera_add( $url ) {
		global $db;
		static $statement = NULL;
		if( $statement == NULL ) {
			if( !($statement = $db->prepare( "INSERT INTO cameras (camera_url,camera_votes) VALUES (?,0)" )) ) {
				throw new Exception( "Failed to prepare camera insert statement: ".$db->error() );
			}
		}
		if( !($statement->bind_param( "s", $url )) ) {
			throw new Exception( "Failed to bind camera URL to insert statement: ".$db->error() );
		}
		if( !($statement->execute()) ) {
			throw new Exception( "Failed to execute camear insert statement: ".$db->error() );
		}
		if( $statement->affected_rows == 0 ) {
			throw new Exception( "Camera insert statement affected 0 rows? ".$db->error() );
		}
		return TRUE;
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
					throw new Exception( "error binding key param to get statement: ".$db->error );
				}
				if( !($get_statement->execute()) ) {
					throw new Exception( "error executing config get statement: ".$db->error );
				}
				if( !($get_statement->bind_result($val)) ) {
					throw new Exception( "error binding result to config get statement: ".$db->error );
				}
				if( $fetch_result = $get_statement->fetch() === FALSE ) {
					throw new Exception( "error fetching result for config get statement: ".$db->error );
				}
				$get_statement->free_result();
				if( $fetch_result === NULL ) {
					$val = NULL;
				}
				$db_cache[ $key ] = $val;
			}
		} else {
			if( is_null( $set_statement ) ) {
				if( !($set_statement = $db->prepare( "INSERT INTO config VALUES (?,?) ON DUPLICATE KEY UPDATE config_value=?" )) ) {
					throw new Exception( "error preparing config set statement: ".$db->error );
				}
			}
			if( !($set_statement->bind_param( "sss", $key, $value, $value )) ) {
				throw new Exception( "error binding params to set statement: ".$db->error );
			}
			if( !($set_statement->execute()) ) {
				throw new Exception( "error executing config set statement: ".$db->error );
			}
			if( $set_statement->affected_rows == 0 ) {
				throw new Exception( "config set didn't seem to affect any rows: ".$db->error );
			}
			$db_cache[ $key ] = $value;
		}
		return $db_cache[ $key ];
	}

	function load_or_create_db( ) {
		global $db, $db_host, $db_user, $db_pass, $db_db;
		$db = new mysqli( $db_host, $db_user, $db_pass, $db_db );
		if( $db->connect_errno != 0 ) {
			throw new Exception( "failed to connect to database: ".$db->connect_error );
		}
		if( !$db->real_query( "CREATE TABLE IF NOT EXISTS config ( config_key VARCHAR(128) PRIMARY KEY, config_value TEXT )" ) ) {
			throw new Exception( "failed to create config table: ".$db->error );
		}
		if( !$db->real_query( "CREATE TABLE IF NOT EXISTS cameras ( camera_id SERIAL PRIMARY KEY, camera_url TEXT, camera_votes INTEGER )" ) ) {
			throw new Exception( "failed to create cameras table: ".$db->error );
		}
		return $db;
	}

	function require_admin() {
		global $admin;
		if( !$admin ) { render( "nope" ); exit; }
	}
?>
