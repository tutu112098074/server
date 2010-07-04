<?php

/**
* ownCloud
*
* @author Frank Karlitschek 
* @copyright 2010 Frank Karlitschek karlitschek@kde.org 
* 
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
* License as published by the Free Software Foundation; either 
* version 3 of the License, or any later version.
* 
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU AFFERO GENERAL PUBLIC LICENSE for more details.
*  
* You should have received a copy of the GNU Lesser General Public 
* License along with this library.  If not, see <http://www.gnu.org/licenses/>.
* 
*/


/**
 * Class for logging features
 *
 */
class OC_LOG {

  /**
   * array to define different log types
   *
   */
  public static $TYPE = array (
  1=>'login',
  2=>'logout',
  3=>'read',
  4=>'write',
  );


  /**
   * log an event
   *
   * @param username $user
   * @param type $type
   * @param message $message
   */
  public static function event($user, $type, $message){
	global $CONFIG_DBTABLEPREFIX;
	$dbTableLog = $CONFIG_DBTABLEPREFIX . 'log';
	$query = 'insert into ' . $dbTableLog . ' (timestamp,user,type,message) values
	         ("' . time() . '","' . addslashes($user) . '","' . addslashes($type) . '","' . addslashes($message) . '")';
    $result = OC_DB::query($query);
    OC_DB::free_result($result);
  }


  /**
   * show the log entries in a web GUI
   *
   */
  public static function show(){
	global $CONFIG_DATEFORMAT;
	global $CONFIG_DBTABLEPREFIX;
    echo('<div class="center"><table cellpadding="6" cellspacing="0" border="0" class="log">');

	$dbTableLog = $CONFIG_DBTABLEPREFIX . 'log';
	if(OC_USER::ingroup($_SESSION['username_clean'],'admin')){
		$query = "select timestamp,user,type,message from $dbTableLog order by timestamp desc limit 20";
	}else{
		$user = $_SESSION['username_clean'];
		$query = "select timestamp,user,type,message from $dbTableLog where user='$user' order by timestamp desc limit 20";
	}
	$result = OC_DB::select($query);
    foreach($result as $entry){
      echo('<tr class="browserline">');
      echo('<td class="sizetext">'.date($CONFIG_DATEFORMAT,$entry['timestamp']).'</td>');
      echo('<td class="highlighttext">'.OC_LOG::$TYPE[$entry['type']].'</td>');
      echo('<td class="nametext">'.$entry['user'].'</td>');
      echo('<td class="nametext">'.$entry['message'].'</td>');
      echo('</tr>');
    }
    echo('</table></div>');
  }

}



?>
