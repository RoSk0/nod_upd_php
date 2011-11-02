<?php  
function func_rar($path1, $file2, $unrar='', $verrar='') {
	if (!function_exists("rar_open")){
		//echo "rar_bash\n";
		//echo "$unrar  -x  $path1/$file2 $path1/\n";
		exec("$unrar  $path1/$file2 $path1/", $out, $val);
		//echo "out=";
		//print_r($out);
		if (!isset($out[1])){
		    echo "There no installed php functions php-rar and bash function unrar. Install one of this functions\n";
		}
	    	if ($out[7]=="All OK") {return true;} else {return false;}
	}else{
		//echo "rar_php\n";
		$rar_file = @rar_open($path1.'/'.$file2);
		if ($rar_file==false){return false;
		}else {	
		$entries = rar_list($rar_file);
		foreach ($entries as $entry) {
			$entry->extract($path1);
		}	
		rar_close($rar_file);
		return true;
		}
	}	
}

if(!function_exists("file_put_contents")){ 
    /** 
     * file_put_contents PHP4 replace 
     * 
     * @param string $file 
     * @param string $data 
     * @return boolean 
     */ 
    function file_put_contents($file,$data){ 
        $fp = fopen($file,"w"); 
        if(!$fp){ 
            return false; 
        } 
        fwrite($fp,$data); 
        fclose($fp); 
        return true; 
    } 
} 

if(!function_exists("file_get_contents")){ 
    /** 
     * file_put_contents PHP4 replace 
     * 
     * @param string $file 
     * @return mixed 
     */ 
    function file_get_contents($file){ 
        $fp = fopen($file,"r"); 
        if(!$fp){ 
            return false; 
        } 
        $result = ""; 
        while (!feof($fp)) { 
            $result .= fread($fp,1024);     
        } 
        fclose($fp); 
        return $result; 
		
    } 
} 

/** 
 * parses update.ver  
 *  
 * @param string $db  
 * @return array  
 */  
function parseDB($db){ 
    $result = array(); 
    $last_section = ""; 
    $lines = explode("\n",$db); 
	foreach ($lines as $line){ 
        $line = trim($line); 
        if(!empty($line)){ 
            if(@$line[0] == "[" and $line[strlen($line)-1]=="]"){ 
                $last_section = strtoupper(trim($line,"[]")); 
                $result[$last_section] = array(); 
            }else{ 
                $a10=@strpos($line, "=");
		$var=@substr($line, 0, $a10 );
		//@list($var,$val) = explode("=",$line); 
		$val1=@substr($line,  $a10+1);
		preg_match('/^(["\']?)(.*[^"\'])(["\']?)/', $val1, $val);
		$result[$last_section][$var] = $val[2]; 
            } 
        } 
    } 
	return $result; 
	
} 

/** 
 * Creates update.ver from array  
 *  
 * @param unknown_type $arr  
 * @return unknown  
 */  
function createDB($arr){ 
    $return = ""; 
	foreach ($arr as $section=>$params){ 
        $return .= "[{$section}]\n"; 
        foreach ($params as $key=>$value){ 
            $return .= "{$key}={$value}\n"; 
        } 
    $return .="\n";
	} 
    return $return; 
} 

/** 
 * Small function to help parse HTTP Headers  
 *  
 * @param unknown_type $array  
 * @return unknown  
 */  
function parseHeader($array){ 
    $result = array(); 
    foreach ($array as $value){ 
        if(substr_count($value,":")){ 
            $data = explode(":",$value); 
            $result[trim($data[0])] = trim($data[1]); 
        } 
    } 
    return $result; 
} 


/** 
 * Downloads file from given host  
 *  
 * @param string $host HTTP Host  
 * @param string $file File on host to download  
 * @param string $save If not empty - save to file  
 * @param string $user HTTP Auth User  
 * @param string $password HTTP Auth Password  
 * @return mixed  
 */  
function getHTTPFilec($host,$file,$save="",$user="",$password="", $proxy="", $quiet){ 
    $host = trim(str_replace("http://","",$host),"/"); 
    $file_name=basename($file);
	$user_password = ($user)?"$user".(($password)?":{$password}":"")."@":""; 
	$open_url = "http://{$user_password}{$host}/{$file}"; 
	//echo "$open_url\n";
	$ch = curl_init($open_url); // create cURL handle (ch) 
	if (!$ch) { 
		die("Couldn't initialize a cURL handle"); 
    } 
	$fp = fopen ($save, "w");

	$ret = curl_setopt ($ch, CURLOPT_FILE, $fp);
	
	// Пример взят с http://ca.php.net/manual/en/function.curl-setopt.php#91952
	// This is required to curl give us some progress
	// if this is not set to false the progress function never
	// gets called
	$ret = curl_setopt($ch, CURLOPT_NOPROGRESS, $quiet);
	// Set up the callback Работает только с версии php 5.3
	//$ret = curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, 'callback');
	// Big buffer less progress info/callbacks
	// Small buffer more progress info/callbacks
	$ret = curl_setopt($ch, CURLOPT_BUFFERSIZE, 512);
	
	
	$ret = curl_setopt($ch, CURLOPT_HEADER,          0);
	$ret = curl_setopt($ch, CURLOPT_FOLLOWLOCATION,  1);
	$ret = curl_setopt($ch, CURLOPT_VERBOSE,  0);
	//$ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER,  1);
	$ret = curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 0);
	$ret = curl_setopt($ch, CURLOPT_HTTPAUTH,  CURLAUTH_ANY);
	if ($proxy==true){
		$ret = curl_setopt($ch, CURLOPT_PROXY,  $proxy['host']);
		$ret = curl_setopt($ch, CURLOPT_PROXYPORT, $proxy['port']);
		$ret = curl_setopt($ch, CURLOPT_PROXYUSERPWD,    $proxy['user'].':'.$proxy['passwd']);}
	//$ret = curl_setopt($ch, CURLOPT_TIMEOUT,         30);
	curl_exec ($ch);
	
	// Chek proxy BEGIN
fclose($fp);
if ($quiet==false){echo "Download $file_name ....";}
if (empty($ret)) { 
    // some kind of an error happened 
    echo curl_error($ch)."\n";
    curl_close($ch); // close cURL handler 
	
} else { 
    $info = curl_getinfo($ch); 
	//print_r($info);
    curl_close($ch); // close cURL handler
	if ($info['http_code'] == 200) { 
            if ($quiet==false){	echo "OK.\n";}
			return true;
	}
	elseif ($info['http_code'] == 407){
		if ($quiet==false) echo "Error Proxy Authentication!\n";
		return false;
	}elseif ($info['http_code'] == 0){
		if ($quiet==false) echo "Bad Proxy name or port!\n";
		return false;
	}else{
		if ($quiet==false) echo "Can't connect to NOD32 server!\n";
		return false;	
	}	
}
}


//function callback ($a=0,$b=0,$c=0, $d=0)
//($download_size, $downloaded, $upload_size, $uploaded)
//{
    
//	echo "$a\n $b\n $c\n $d\n";
	// do your progress stuff here
	//echo "download_size=$download_size\n";
	//echo "downloaded=$downloaded\n";
//	return (0);
//}



function version1($db) {
	if (isset($db['ENGINE2'])) $ver=$db['ENGINE2']['version']; 
	elseif (isset($db['ENGINE1'])) $ver=$db['ENGINE1']['version']; 
	else  $ver=$db['ENGINE0']['version'];
	
	
	return $ver;
	}

function new_section1($db, $file, $path3){
	if (isset($db['display_name']))	{
			$db['display_name']=trim($db['display_name'], '"');}
	$db['size']=filesize($path3.$file);
	$db['file']=$file;
	unset($db['filesize'], $db['crc'], $db['name'], $db['setup'] );
	//print_r($db);
	return $db;	
	}


//Функция замены scandir для php4 (спасибо Yuryus)
if (!function_exists('scandir')) {
    function scandir($path){    
	$files11 = opendir($path);
	while (false !== ($filename = readdir($files11))) {
    	    $files1[] = $filename;
	}
    return $files1;
    }
}

// Удаление nup файлов из дирестории $path
function rmfiles($path){
    // Список файлов
    $files1 = scandir($path);
    // Проверяем каждый файл
    foreach ($files1 as $file){  
	$file_nup=explode('.', $file);
    	if (isset($file_nup[1])==true){
	    if ($file_nup[1]=="nup") {
		unlink($path.$file);
	    }
	}
    }
}

function copyfiles($paths, $pathd){
    // Список файлов
    $files1 = scandir($paths);
    // Проверяем каждый файл
    foreach ($files1 as $file){  
	$file2=explode('.', $file);
	if (isset($file2[1])==true){
	    if ($file2[1]=="nup" || $file2[1]=="ver") {
		copy($paths.$file, $pathd.$file);
	    }
	}	
    }
}
	

// Функция по разделению параметров прокси сервера
function proxy_par($proxy){
	preg_match('/http:\/\/(.[^\s:@]+):(.[^:@]+)@([a-z.0-9\-_]+):([0-9]+)/is', $proxy, $p);
	if ($p[0]) {
		$proxy1['user']=$p[1];
		$proxy1['passwd']=$p[2];
		$proxy1['host']=$p[3];
		$proxy1['port']=$p[4];
	}else{
		preg_match('/http:\/\/(.[^\s:@]+)@([a-z.0-9\-_]+):([0-9]+)/is', $proxy, $p);
		if ($p[0]) {
			$proxy1['user']=$p[1];
			$proxy1['host']=$p[2];
			$proxy1['port']=$p[3];
		}else{
			preg_match('/http:\/\/([a-z.0-9\-_]+):([0-9]+)/is', $proxy, $p);
			if ($p[0]) {
				$proxy1['host']=$p[1];
				$proxy1['port']=$p[2];
			}
		}
	}
	//print_r($proxy1);
	return $proxy1;
}

function url_servers($URL){
	$result = array(); 
    $last_section = ""; 
    $lines = explode(",",$URL); 
    $n=0;
	foreach ($lines as $line){ 
        $line = trim($line); 
        if(!empty($line)){ 
            preg_match('/(.[^\s:@]+)@http:\/\/([a-z.0-9\-_]+)\/([a-z.0-9\-_]+)/is', $line, $p);
			$result[$n]="http://".$p[2]."/";
			$n++;
            } 
        } 
    //print_r($result);
	//unset($result[0], $result[1]);
	return $result;
	} 

// Функция по закачке файла через wget + прокси 
function func_wget($url, $file, $proxy_wget="", $quiet="") {
    global $wget;
	If ($quiet==true)$quiet_wget='-q';
	else echo "\n";
	if ($proxy_wget ==true) {$proxy_parametr=' --proxy=on  --proxy-user='.$proxy_wget['user'].' --proxy-password='.
			$proxy_wget['passwd'];
			$export_proxy='export http_proxy=http://'.$proxy_wget['host'].':'.$proxy_wget['port']."\n";
	}
	//echo $export_proxy.' '.$wget.' '.$quiet_wget.' '.$url.' -O '.$file.' '.$proxy_parametr."\n";
	@exec($export_proxy.' '.$wget.' '.$quiet_wget.' '.$url.' -O '.$file.' '.$proxy_parametr, $out, $val);
	if ($val==1) { return false; } else { return true; }
	}    

function getHTTPFile1($host,$file,$save="",$user="",$password="",$proxy_wget="", $quiet=""){
    $host = trim(str_replace("http://","",$host),"/");
    $user_password =  $user.':'.$password.'@'; 
    $open_url = "http://{$user_password}{$host}/{$file}";
    $fp = func_wget($open_url, $save, $proxy_wget, $quiet); 
	return $fp;
}


?>
