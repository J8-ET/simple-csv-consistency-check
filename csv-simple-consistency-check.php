<?php
$inFileDir = __DIR__.'/../_data_in/classification-cockpit-SPMED-num-corrections/';
$logLocation = __DIR__.'/../_data_out/classification-cockpit-SPMED-num-corrections/action-needed.log';
$errLogLocation = __DIR__.'/../_data_out/classification-cockpit-SPMED-num-corrections/error.log';

ini_set ('display_errors', 'on');
ini_set ('log_errors', 'on');
ini_set ('display_startup_errors', 'on');
ini_set ('error_reporting', E_ALL);
ini_set ('error_log', $errLogLocation);

$inFiles = scandir($inFileDir);
$fileProbs = [];

foreach($inFiles as $file){
	$fileLoc = $inFileDir.$file;
	try{
		if(is_file($fileLoc)){
			$data = str_replace(PHP_EOL,'',explode("\n",file_get_contents($fileLoc)));
			$headers = [];
			$lastHeaderIndex = 0;
			foreach($data as $index=>$lineCont){
				$lData = explode(';',$lineCont);
				$lastDataIndex = count($lData)-1;
				if($index==0){
					$headers = $lData;
					$lastHeaderIndex = $lastDataIndex;
				}
				elseif($lastDataIndex>$lastHeaderIndex) $fileProbs[] = ['file'=>$file,'line'=>$index+1];
			}
		}
	}catch(Exception $e){
		error_log($e->getMessage(),0);
	}
}

$fileProbsLog = [];
foreach($fileProbs as $prob){
	$fileProbsLog[] = 'Problem gefunden:	Datei: "'.$prob['file'].'"	Zeile: '.$prob['line'];
}

$fileProbsLogStr = implode(PHP_EOL,$fileProbsLog);
if(is_file($logLocation))unlink($logLocation);
file_put_contents($logLocation,$fileProbsLogStr);
echo($fileProbsLogStr);
