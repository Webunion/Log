<?php
namespace Webunion\Log;
Class Log{
	public $path = '';
	public $fileName = 'log.log';

	public function __construct( $path = '', $fileName = 'log.log' ){
		$this->path 	= $path;		
		$this->fileName = $fileName;		
	}
	
	public function logError($errno, $errmsg, $filename, $linenum, $vars){
		$errortype = array (
			E_ERROR              => 'Error',
			E_WARNING            => 'Warning',
			E_PARSE              => 'Parsing Error',
			E_NOTICE             => 'Notice',
			E_CORE_ERROR         => 'Core Error',
			E_CORE_WARNING       => 'Core Warning',
			E_COMPILE_ERROR      => 'Compile Error',
			E_COMPILE_WARNING    => 'Compile Warning',
			E_USER_ERROR         => 'User Error',
			E_USER_WARNING       => 'User Warning',
			E_USER_NOTICE        => 'User Notice',
			E_STRICT             => 'Runtime Notice',
			E_RECOVERABLE_ERROR  => 'Catchable Fatal Error'
		);
		// set of errors for which a var trace will be saved
		$user_errors = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE);

		$err = '<errorEntry>';
		$err .= '<dateTime>' . date('Y-m-d H:i:s') . '</dateTime>';
		$err .= '<errorNum>' . $errno . '</errorNum>';
		$err .= '<errorType>' . $errortype[$errno] . '</errorType>';
		$err .= '<errorMsg>' . $errmsg . '</errorMsg>';
		$err .= '<scriptName>' . $filename . '</scriptName>';
		$err .= '<scriptLineNum>' . $linenum . '</scriptLineNum>';
		$err .= '<uri>'.$_SERVER['REQUEST_URI'].'</uri>';
		$err .= '<param>';
		$array = array_merge($_GET, $_POST);
			if(count($array) > 1){
				foreach($array AS $key=>$value){
					$err .= '<var>'.$key.'='.$value.'</var>';
				}
			}
		$err .= '</param>';	
			
		if( in_array($errno, $user_errors) ){
			$err .= '<varTrace>' . wddx_serialize_value($vars, 'Variables') . '</varTrace>';
		}
		$err .= '</errorEntry>' . PHP_EOL;
		// save to the error log, and e-mail me if there is a critical user error
		error_log( $err, 3, $this->path.$this->fileName );
		if( $errno == E_USER_ERROR ){
			//Helper::enviarEmail($err,'Erro critico '.date('Y-m-d H:i:s'));
		}
	}	
}