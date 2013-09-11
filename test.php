<table>
	<tr><th>function</th><th>use case</th><th>result</th></tr>
<?php
	include_once( "functions.php" );
	try {
		$db = load_or_create_db();
		print( "<tr><td>load_or_create_db</td><td>-</td><td style='color: green;'>OK</td></tr>" );
	} catch ( Exception $e ) {
		print( "<tr><td>load_or_create_db</td><td>-</td><td style='color: red;'>FAIL</td></tr>" );
	}
	try {
		execute( "CREATE TABLE test_table ( test_column TEXT )" );
		print( "<tr><td>execute</td><td>CREATE TABLE</td><td style='color: green;'>OK</td></tr>" );
	} catch ( Exception $e ) {
		print( "<tr><td>execute</td><td>CREATE TABLE</td><td style='color: red;'>FAIL</td></tr>" );
	}
	try {
		execute( "INSERT INTO test_table VALUES ('hi there')" );
		print( "<tr><td>execute</td><td>INSERT INTO</td><td style='color: green;'>OK</td></tr>" );
	} catch ( Exception $e ) {
		print( "<tr><td>execute</td><td>INSERT INTO</td><td style='color: red;'>FAIL</td></tr>" );
	}
	try {
		execute( "INSERT INTO test_table VALUES (?)", "s", Array( "second test" ) );
		print( "<tr><td>execute</td><td>INSERT w/ params</td><td style='color: green;'>OK</td></tr>" );
	} catch ( Exception $e ) {
		print( "<tr><td>execute</td><td>INSERT w/ params</td><td style='color: red;'>FAIL</td></tr>" );
	}
	try {
		$result = select( "SELECT * FROM test_table" );
		if( $result[0][0] == "hi there" ) {
			if( $result[1][0] == "second test" ) {
				print( "<tr><td>select</td><td>SELECT</td><td style='color: green;'>OK</td></tr>" );
			} else {
				throw new Exception( "second row/first column wasn't as expected" );
			}
		} else {
			throw new Exception( "first row/column wasn't as expected" );
		}
	} catch ( Exception $e ) {
		print( "<tr><td>select</td><td>SELECT</td><td style='color: red;'>FAIL</td></tr>" );
	}
	try {
		$result = select( "SELECT * FROM test_table WHERE test_column=?", "s", Array( "second test" ) );
		if( $result[0][0] == "second test" ) {
			print( "<tr><td>select</td><td>SELECT w/ params</td><td style='color: green;'>OK</td></tr>" );
		} else {
			throw new Exception( "second row/first column wasn't as expected" );
		}
	} catch ( Exception $e ) {
		print( "<tr><td>select</td><td>SELECT w/ params</td><td style='color: red;'>FAIL</td></tr>" );
	}
	try {
		execute( "DELETE FROM test_table WHERE test_column='hi there'" );
		print( "<tr><td>execute</td><td>DELETE</td><td style='color: green;'>OK</td></tr>" );
	} catch ( Exception $e ) {
		print( "<tr><td>execute</td><td>DELETE</td><td style='color: red;'>FAIL</td></tr>" );
	}
	try {
		execute( "DELETE FROM test_table WHERE test_column=?", "s", Array( "second test" ) );
		print( "<tr><td>execute</td><td>DELETE w/ params</td><td style='color: green;'>OK</td></tr>" );
	} catch ( Exception $e ) {
		print( "<tr><td>execute</td><td>DELETE w/ params</td><td style='color: red;'>FAIL</td></tr>" );
	}
	try {
		execute( "DROP TABLE test_table" );
		print( "<tr><td>execute</td><td>DROP TABLE</td><td style='color: green;'>OK</td></tr>" );
	} catch ( Exception $e ) {
		print( "<tr><td>execute</td><td>DROP TABLE</td><td style='color: red;'>FAIL</td></tr>" );
	}
?>
