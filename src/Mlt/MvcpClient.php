<?php

namespace App\Mlt;

/**
 * Melted Client.
 *
 * Handles communication between the application and the MVCP server.
 *
 * @package       playout
 */
class MvcpClient extends Client
{
	/**
	 * Contains the name of the program
	 *
	 * @access    public
	 * @var       string
	 */
	var $name = 'melted';

	/**
	 * Contains all of the units that can be used in
	 * the MVCP server
	 *
	 * @access    public
	 * @var       array
	 */
	var $units = array('U0', 'U1', 'U2', 'U3');

	/**
	 * The current unit that operations are being done on
	 *
	 * @access    public
	 * @var       string
	 */
	var $_unit;

	/**
	 * Initializes the MVCP by setting the default unit to U0
	 *
	 * @access    protected
	 * @return    void
	 */
	function _initialize() {
		$this->setUnit();
	}

	/**
	 * Opens a connection with the MVCP server
	 *
	 * @access    public
	 * @return    boolean
	 */
	function open() {
		if( $this->isConnected() ) {
			return true;
		}
		$retval = $this->connect(true, $status);
		sleep(1);
		return $retval;
	}

	/**
	 * Closes the connection with the MVCP server
	 *
	 * @access    public
	 * @return    boolean
	 */
	function close() {
		if( !$this->isConnected() ) {
			return true;
		}
		$this->write('bye');
		return !$this->disconnect();
	}

	/**
	 * Restarts the MVCP service.  This should be done if a connection cannot be made
	 * with the Mvcp Client class
	 *
	 * @access    public
	 * @return    boolean
	 */
	function restart() {
		$output = array();
		$return = null;
		exec("/sbin/service {$this->name} restart 2&>1", $output, $return);
		if( $return != 0 ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Sets the current unit.  By default, sets the unit to the default
	 * unit U0
	 *
	 * @access    public
	 * @param     $unit
	 * @return    boolean
	 */
	function setUnit($unit = 0) {
		if( !is_int($unit) ) {
			$this->_unit = $this->units[0];
		} elseif( $unit < 0 || $unit > sizeof($this->units) - 1 ) {
			return false;
		}
		$this->_unit = $this->units[$unit];
		return true;
	}

	/**
	 * Sends the appropriate command to the MVCP server.
	 *
	 * @access    public
	 * @param     $command - command to run
	 * @param     $arguments - array of arguments
	 * @param     $response - response to return
	 * @return    boolean - status of command
	 */
	function sendCommand($command, $arguments = array(), &$response) {
		switch($command) {
			case 'uset':
				list($key, $value) = $arguments;
				$_command = "uset {$this->_unit} {$key}={$value}";
				break;
			case 'uget':
				list($key) = arguments;
				$_command = "uget {$this->_unit} {$key}";
				break;
			case 'load':
				list($clip, $in, $out) = $arguments;
				$_command = trim("load {$this->_unit} {$clip} {$in} {$out}");
				break;
			case 'apnd':
			case 'append':
				list($clip, $in, $out) = $arguments;
				$_command = trim("apnd {$this->_unit} {$clip} {$in} {$out}");
				break;
			case 'set_in':
			case 'setIn':
				list($frame) = $arguments;
				$_command = "sin {$this->_unit} {$frame}";
				break;
			case 'set_out':
			case 'setOut':
				list($frame) = $arguments;
				$_command = "sout {$this->_unit} {$frame}";
				break;
			case 'play':
				$_command = "play {$this->_unit}";
				break;
			case 'stop':
				$_command = "stop {$this->_unit}";
				break;
			case 'step':
				list($frames) = $arguments;
				$_command = "step {$this->_unit} {$frames}";
				break;
			case 'wipe':
				$_command = "wipe {$this->_unit}";
				break;
			case 'clear':
				$_command = "clear {$this->_unit}";
				break;
			default:
				return false;
				break;
		}
		return $this->runCommand($_command, $response);
	}

	/**
	 * Gets the status of the unit
	 *
	 * @access    public
	 * @return    array
	 */
	function status() {
		if( !$this->isConnected() ) {
			return array();
		}
		$this->write("usta {$this->_unit}");
		sleep(1);

		$return = $this->read();
		$status = $this->read();

		list($unit, $mode, $currentClip, $currentFrame, $speed, $fps, $in, $out,
			 $length, $tailClip, $tailPosition, $tailIn, $tailOut, $tailLength,
			 $seekable, $playlistGenNumber, $clipIndex) = split(' ', $status);

		return compact(array('unit', 'mode', 'currentClip', 'currentFrame', 'speed', 'fps', 'in', 'out',
					   'length', 'tailClip', 'tailPosition', 'tailIn', 'tailOut', 'tailLength',
					   'seekable', 'playlistGenNumber', 'clipIndex'));
	}

	/**
	 * Lists the clips on the unit
	 *
	 * @access    public
	 * @return    array
	 */
	function listClips() {
		if( !$this->isConnected() ) {
			return array();
		}
		$this->setBlockingMode(0);
		$this->write("list {$this->_unit}");
		sleep(1);

		$response = $this->read();
		$_list = explode(PHP_EOL, $response);
		$response = array_shift($_list);
		$genNum = array_shift($_list);
		foreach( $_list as $clip ) {
			list($index, $file, $in, $out, $realLength, $estimatedLength) = explode(' ', $clip);
			$list[] = compact(array('index', 'file', 'in', 'out', 'realLength', 'estimatedLength'));
		}
		$this->setBlockingMode(1);
		return $list;
	}

	/**
	 * Writes a command to the miracle server, gets the reponse and returns the
	 * status of the command as well as the command itself and the response code.
	 *
	 * @access    public
	 * @param     $command
	 * @param     $response - stores the command and response code
	 * @return    boolean true on success, false on failure
	 */
	function runCommand($command, &$response) {
		if( !$this->isConnected() ) {
			$response = "command - \"{$command}\" : not connected";
			return false;
		}
		$this->write($command);
		usleep(500000);
		$_response = $this->read();
		if( !empty($_response) ) {
			$_codes = explode(" ", $_response);
			if( isset($_codes[1]) && $_codes[1] != 'OK' ) {
				$error = false;
			} else {
				$error = true;
			}
		} else {
			$_response = "not connected";
			$error = true;
		}
		$response = "command - \"{$command}\" : {$_response}";
		return $error;
	}
}
