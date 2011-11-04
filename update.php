#!/usr/bin/php
<?php 

/* 
 * Quick & Dirty Script to manage NOD 32 Updates  
 *  
 * @author mnk  
 * @email mkukushkin@mail.ru
 * @Thanks for kode@airnet.ru 
 * @version 2.1
 *  Новые версии и описание программы можно взять
 * http://www.volmed.org.ru/wiki/index.php/Скрипт_по_обновлению_антивирусных_баз_NOD32_под_Linux_(PHP) 
 */ 
$start = microtime(true); 
//Отображать все ошибки, кроме notice и strict
error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT);
include("setup.php");
include("section.php");

ini_set("display_errors",0); 
ini_set("user_agent","wget"); 

include("functions.php"); 


$umask_files=umask();
umask(0022);
$mail_text="";
$mail_cont="";

// Определяем использовать ли прокси и метод закачки
if ($proxy==true) {
	$proxy=proxy_par($proxy);
	//echo "proxy=$proxy\n";
}else{$proxy='';}
//Определяем задан ли unrar
if (isset($unrar)==false) $unrar="";

// Проверяем программу закачки
if ($wget==true) $func_download="getHTTPFile1";
else $func_download="getHTTPFilec";


//Проверяем наличие нужных каталогов
if (file_exists(DEFAULT_SAVE_PATH)==false) mkdir(DEFAULT_SAVE_PATH);

foreach ($servers as $server){   
	//Строим Section2 (необязательные компоненты)
	//print_r($server['compons']);
	$section2=array();
	if (isset($server['compons'])){
		foreach($server['compons'] as $compon){
			//echo "$compon\n";
			$section2 =$section2 + ${'section2_'.$server['type'].'_'.$compon};
			}
	}		
	//print_r($section2);
	$error1=0;
    //Определяем стандартные пути для каждого сервера
    $savepath=DEFAULT_SAVE_PATH.'/mirror_'.$server['type'].'/';
	
	//Проверяем наличие нужных каталогов 
	if (file_exists($savepath)==false) mkdir($savepath);
    if (file_exists($savepath."arc")==false) mkdir($savepath."arc");
    $section1=${'section1_'.$server['type']};
	if($server['type']=='V3'  && $server['ess']==false){
		unset($section1['SMON0'], $section1['SMON1'], $section1['SMON2']);
	}
    	if( $server['type']=='V5' && $server['ess']==false){
		unset($section1['SMON0'], $section1['SMON1'], $section1['SMON2'],$section1['HORUS0'],$section1['HORUS1'],$section1['HORUS2']);
	}
    //print_r($section1);
    //if (isset(${'section2_'.$server['type']})==true && $server['compon']==true) $section2=${'section2_'.$server['type']};
       	
    if(file_exists($savepath."update.ver")) {
		//$current_db = parse_ini_file($savepath."update.ver", true, INI_SCANNER_RAW );
        $current_db = parseDB(file_get_contents($savepath."update.ver")); 
		//print_r($current_db);
		$version_old=version1($current_db);
	}	
    
	// Если не указан сервер обновлений, и нет файла со списком серверов от eset,
	// то список серверов будет взят из  update.ver с сервера http://update.eset.com/
	if (file_exists($savepath."/arc/servers")){
		//$upd_ser1=@parse_ini_file($savepath."/arc/servers", true, INI_SCANNER_RAW );
		$upd_ser1=@parseDB(@file_get_contents($savepath."/arc/servers"));
		$upd_ser2=@url_servers($upd_ser1[HOSTS][Other]);
		// Удалим первые два сервера.
		unset($upd_ser2[0], $upd_ser2[1]);
		// отсортируем сервера, что бы не начинать обновлятся с самого загруженного.
		shuffle($upd_ser2);
		//arsort($upd_ser2, SORT_NUMERIC);
		//print_r($upd_ser2);
		//echo 'server[host]='.$server['host']."\n";
	}
	if (isset($server['host'])==true) {
		$upd_ser2="";
		$upd_ser2[0]=$server['host'];}
	else {
		if (isset($upd_ser2) ==false){
			$upd_ser2="";
			$upd_ser2[0]='http://update.eset.com/';
		}	
	}	
	//print_r($upd_ser2);
	//echo "1111=".$upd_ser2['host'];
	if ($quiet==false){ echo "Update base for NOD32 {$server['type']}\n";}
	// Для каждого из хостов пробуем обновиться, пока не получится
	foreach ($upd_ser2 as $upd_ser3){
		$error1=0;	
		if ($quiet==false){ 
			echo "Checking {$upd_ser3}\n"; }
	
		// Определяем пути до файлов в зависимости от версии
		if ($server['user']==true && $server['type'] == "V2" && $server['all_in_one'] == false) $newpath1="nod_upd/";
		elseif($server['user']==true && $server['type'] == "V3" && $server['all_in_one'] == false) $newpath1="eset_upd/";
		elseif($server['user']==true && $server['type'] == "V5" && $server['all_in_one'] == false) $newpath1="eset_upd/v5/"; 
		//else $server['newpath']="";
		else $newpath1="";
		//echo "url=".$newpath1."\n";
		// Удаляем старый update.ver
		@unlink ($savepath."/arc/update.ver");	
		if ($func_download($upd_ser3.$newpath1,"update.ver",$savepath."arc/update.ver",@$server['user'],@$server['password'], $proxy, $quiet) == false) { 
		$error1=1;
		
		continue;
		 } 
					
		if ($server['user']==true) {
			if(file_exists($savepath."arc/update.ver")) rename($savepath."arc/update.ver", $savepath."arc/update.rar");
			$rar_val=func_rar($savepath.'arc', 'update.rar', $unrar, $verrar);
			if ($rar_val==false){
				if(file_exists($savepath."arc/update.rar"))rename($savepath."/arc/update.rar", $savepath."/arc/update.ver");
			}
		@unlink($savepath."/arc/update.rar");
		}	
	
		
		//$updatedb=@parse_ini_file($savepath."/arc/update.ver", true, INI_SCANNER_RAW );
		$updatedb=@parseDB(@file_get_contents($savepath."/arc/update.ver"));
		if (isset($updatedb['HOSTS']['Other'])==true){
			$upd_ser['HOSTS']['Other']=$updatedb['HOSTS']['Other'];}
		//print_r($updatedb[HOSTS]);
    
		// Сохраняем список серверов для будущего применения
		//print_r($upd_ser);
		if (isset($upd_ser)==true){
			$upd_ser1=createDB($upd_ser);
			file_put_contents($savepath."/arc/servers",$upd_ser1);
		}
	
		// Проверяем правильность update.ver
		if(!$updatedb || $updatedb['ENGINE0']==false){ 
			if ($quiet==false){echo "Invalid server!\n";}
			$error1=1;
			continue; 
		} 
		
		// Заносим список серверов в update.ver
		$new_update="";
		if(isset($updatedb['HOSTS']['Other'])==true){
			$new_update['HOSTS']['Other']=$updatedb['HOSTS']['Other'];}
	
		// Список файлов
		$files1 = scandir($savepath);

		// Проверяем каждый файл
		foreach ($files1 as $file){
			//echo "file=$file\n";
			// Если до этого была ошибка, то прекращаем обновление
			if ($error1==1){continue;}
			$file_nup=explode('.', $file);
			if (isset($file_nup[1])==true && $file_nup[1]=="nup") {
				// Считываем данные из файла
				$handle = fopen($savepath.$file, "r");
				$contents = fread($handle, 550);
				fclose($handle);
				
				$data=parseDB($contents);
				if (isset($data['UPDATE_INFO']['name'])==true){
					$name_upd=@basename($updatedb[$data['UPDATE_INFO']['name']]['file']);
				}
				// Если секция в update.ver существует
				if (isset($data['UPDATE_INFO']['name'])==true && isset($updatedb[$data['UPDATE_INFO']['name']])==true && (isset($section2[$data['UPDATE_INFO']['name']]) || isset($section1[$data['UPDATE_INFO']['name']]))){
					// Если build  файлов совпадают
					//echo "filesize=".filesize($savepath.$file)."\n";
					//echo "file_upd=".$updatedb[$data['UPDATE_INFO']['name']]['size']."\n";
					if ($data['UPDATE_INFO']['build'] == $updatedb[$data['UPDATE_INFO']['name']]['build']){
						// Если имена не совпадают
						if ($name_upd!=$file){
							if ($quiet==false){	echo "Rename file $file in {$name_upd}\n";}
							rename($savepath.$file, $savepath.$name_upd);
						}
						//Если секция в update.ver существует но билд файла меньше
						// Добавляем данные в update.ver
						$new_update[$data['UPDATE_INFO']['name']]=$updatedb[$data['UPDATE_INFO']['name']];
						$new_update[$data['UPDATE_INFO']['name']]['file']=$name_upd;
					}elseif($data['UPDATE_INFO']['build'] <= $updatedb[$data['UPDATE_INFO']['name']]['build']){
						$file_new=$updatedb[$data['UPDATE_INFO']['name']]['file'];
						if ($quiet==false){	
							echo "\nThe different size of files $file and $name_upd\n";
							echo "Delete old $file and upload new $name_upd\n";}
						unlink($savepath.$file);
						if($func_download($upd_ser3, $file_new, $savepath.$name_upd, @$server['user'],@$server['password'],$proxy, $quiet)==false){
							$error1=1;
							if ($quiet==false) echo "Error downloading file $file_new \n";
							unlink($savepath.$file);
							break;} 
						// Если после скачивания размеры не совпадают
						/*
						if (filesize($savepath.$name_upd) !=$updatedb[$data['UPDATE_INFO']['name']]['size']){
							$error1=1;
							echo "Error downloading file $file_new \n";
							unlink($savepath.$file);
							break;
						}
						*/
						// Добавляем данные в update.ver
						$new_update[$data['UPDATE_INFO']['name']]=$updatedb[$data['UPDATE_INFO']['name']];
						$new_update[$data['UPDATE_INFO']['name']]['file']=$name_upd;
						if(!file_exists($savepath.$name_upd))break;
						else $new_update[$data['UPDATE_INFO']['name']]['size']=filesize($savepath.$name_upd);
						//Если файл более новый, чем в секции
					}elseif($data['UPDATE_INFO']['build'] > $updatedb[$data['UPDATE_INFO']['name']]['build']){
						// Добавляем данные в update.ver
						$new_update[$data['UPDATE_INFO']['name']]=new_section1($data['UPDATE_INFO'], $file, $savepath);
					}	
					
					// Если секции не существует, но она есть в section2  или билд нового файла существует и
					// меньше старого
					}elseif (isset($section2[$data['UPDATE_INFO']['name']]) || (isset($updatedb[$data['UPDATE_INFO']['name']]['build']) && $data['UPDATE_INFO']['build'] > $updatedb[$data['UPDATE_INFO']['name']]['build'])) {
					// Добавляем данные в update.ver
					$new_update[$data['UPDATE_INFO']['name']]=new_section1($data['UPDATE_INFO'], $file ,$savepath);
				} else 	{
					if ($quiet==false){	echo "Erase needless file $file \n";}
					unlink($savepath.$file);
				}
			} 
		}

		// Докачиваем нужные файлы
		foreach ($updatedb as $section=>$vars){ 
		// Все названия секций в Верхний регистр.
		//echo 'section='.$section."\n";
		$section=strtoupper($section);
			// Если до этого была ошибка, то прекращаем обновление
			if ($error1==1){break;}
			//echo "33333333333333\n";
			$file_nup=@explode('.', $vars['file']);
			if (isset($file_nup[1])==false) $file_nup[1]="";
			$file_name=@basename($vars['file']);
			//echo "11111=".$section2[$section]."\n";
			if( file_exists($savepath.$file_name)==false && $file_nup[1]=="nup" && (isset($section1[$section]) || isset($section2[$section] ))){ 
				if($func_download($upd_ser3,$vars['file'], $savepath.$file_name,@$server['user'],@$server['password'], $proxy, $quiet)==false){
				$error1=1;
				break;
				} 
				//echo "filesize=".filesize($savepath.$file_name)."\n"; 
				//echo "size_upd=".$vars['size']."\n"; 
				if (file_exists($savepath.$file_name)){
					$new_update[$section]=$vars;
					$new_update[$section]['size']=filesize($savepath.$file_name);
					$new_update[$section]['file']=$file_name;
				}else{
					echo "Error downloading file {$vars['file']}\n";
					$error1=1;
					break;
				}
			}
			//Формируем секции COMPATLIST и data0001 для 3 NOD
			elseif($section=="COMPATLIST" || $section=="data0001"){
			$new_update[$section]=$vars;	
			}	
		} 
		if ($error1==1) {continue;}
		else{
		//Сохраняем новый файл  update.ver 
		//print_r($new_update[RA0]);
		$new_upd=createDB($new_update);
		//$version_new=
		file_put_contents($savepath."update.ver",$new_upd); 
		$version_new=version1($new_update);
		if ($quiet==false){	
			echo "Old version  $version_old\n";
			echo "New version $version_new\n";}
		// При удачном обновлении прерываем цикл
		break;
		}
	}
	
	
	// Если все удачно, переписываем в WEB каталог
	if (file_exists($server['www'])==false) mkdir($server['www']);
	
	//Если была ошибка восстанавливаем базы из web
	
	if ($error1==true){
		if ($quiet==false){
			echo "ERROR of updating base {$server['type']}\n";
			echo "RESTORE bases\n";}
		rmfiles($savepath);
		copyfiles($server['www'],  $savepath); 
		// Если задан ящик отправляем почту
		if (isset($user_mail)==true){
			$mail_text = $mail_text."\n!!! ERROR of update base for NOD32 ".$server['type']." last " .$version_old;
			$mail_cont = $mail_cont." Error ESET ".$server['type'].",";
			//mail($user_mail, "ERROR ESET ".$server['type']." ".$version_old, $text);
			}
	}else{
		if ($quiet==false){	echo "SUCCESS of updating bases {$server['type']}\n";}
		//Переписываем новые базы в web каталог		
		rmfiles($server['www']);
		copyfiles($savepath, $server['www']);
		if (file_exists($server['www'].$newpath1)==false) {
			preg_match('/([a-z0-9_-]+)\/([v\d]*)(\/*)/',$newpath1, $path_v );
			mkdir($server['www'].$path_v[1]);
			if ($path_v[2]==true)mkdir($server['www'].$newpath1);
		}
		copy($savepath.'update.ver', $server['www'].$newpath1.'update.ver');
		// Меняем права на файл
		exec('chown -R '.HTTP_USER.' '.$server['www']);
		// Если задан ящик и новая версия отправляем почту
		if ($version_new != $version_old){
			if (isset($user_mail)) {
				$mail_text = $mail_text."\nSUCCESS update base for NOD32 ".$server['type']." from  version " .$version_old." to version ".$version_new;
				$mail_cont = $mail_cont." Succees ESET ".$server['type'].",";
				//mail($user_mail, "SUCCESS ESET ".$server['type']."  ".$version_new, $text);
			}
		}
	}	

	if ($quiet==false){	echo "\n\n";}
}
if ($mail_text) mail($user_mail, $mail_cont, $mail_text);

umask($umask_files);
if ($quiet==false){	
	echo "Execution time ",round(microtime(true)-$start,4)," sec.\n"; }
?>
