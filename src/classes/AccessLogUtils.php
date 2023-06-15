<?php
/**
 * Utilities to log access to web pages, really anonymous
 * 
 * @author Cristiano Longo
 *
 * Copyright 2020 Cristiano Longo
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class AccessLogUtils{

	/**
	 * Record a visit to the page with specified $uri
	 * @param $uri string the URI of the visited page 
	 * @param $logFilePath string path of the log file
	 */
	public static function logAccess($uri, $logFilePath){
		//ACCESS COUNTER
		//$uri=$_SERVER["REQUEST_URI"];
		$handle = fopen($logFilePath, "a+");
		fprintf($handle, "%s\t%s\n",date("Y\tm\td\tH\ti\ts"), $uri);
		fflush($handle);
		fclose($handle);	
	}
	
}
?>
