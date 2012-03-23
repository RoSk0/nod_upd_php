#!/usr/bin/php
<?php

/*
 * Quick & Dirty Script to manage NOD 32 Updates
 *
 * @author mnk
 * @email mkukushkin@mail.ru
 * @Thanks for kode@airnet.ru
 * @version 2.5
 *  Новые версии и описание программы можно взять
 * http://www.volmed.org.ru/wiki/index.php/Скрипт_по_обновлению_антивирусных_баз_NOD32_под_Linux_(PHP)
 */

 $start = microtime(true);
//Отображать все ошибки, кроме notice и strict
error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT);

ini_set("register_argc_argv","on");


include("section.php");
include("setup.php");



ini_set("display_errors",0);
ini_set("user_agent","wget");

include("functions.php");


$umask_files=umask();
umask(0022);
$mail_text="";
$mail_cont="";

If(!isset($diff))$diff=1;

// Определяем использовать ли прокси и метод закачки
if ($proxy==true) $proxy=proxy_par($proxy);
else $proxy='';
//Определяем задан ли unrar
if (isset($unrar)==false) $unrar="";

// Проверяем программу закачки
if ($wget==true) $func_download="getHTTPFile1";
else $func_download="getHTTPFilec";


//Проверяем наличие нужных каталогов
if (file_exists(DEFAULT_SAVE_PATH)==false) mkdir(DEFAULT_SAVE_PATH);

foreach ($servers as $server)
{
	//Строим Section2 (необязательные компоненты)
	//print_r($server['compons']);
	$section2=array();
	unset($upd_ser2);
	if (isset($server['compons']))
	{
		foreach($server['compons'] as $compon)
		{
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
    if (file_exists($savepath.$server['path_dop'])==false) mkdir_new($savepath,$server['path_dop']);
	if (file_exists($server['www'].$server['path_dop'])==false) mkdir_new($server['www'],$server['path_dop']);
	$section1=${'section1_'.$server['type']};
	if($server['type']=='V3'  && $server['ess']==false) unset($section1['SMON0'], $section1['SMON1'], $section1['SMON2']);
    if( $server['type']=='V5' && $server['ess']==false)	unset($section1['SMON0'], $section1['SMON1'], $section1['SMON2'],$section1['HORUS0'],$section1['HORUS1'],$section1['HORUS2']);

    //print_r($section1);
    if(file_exists($savepath."update.ver"))
    {
		//$current_db = parse_ini_file($savepath."update.ver", true, INI_SCANNER_RAW );
        $current_db = parseDB(file_get_contents($savepath."update.ver"));
		$version_old=version1($current_db);
	}

	// Если не указан сервер обновлений, и нет файла со списком серверов от eset,
	// то список серверов будет взят из  update.ver с сервера http://update.eset.com/
	if (file_exists($savepath."/arc/servers"))
	{
		//$upd_ser1=@parse_ini_file($savepath."/arc/servers", true, INI_SCANNER_RAW );
		$upd_ser1=@parseDB(@file_get_contents($savepath."/arc/servers"));
		$upd_ser2=@url_servers($upd_ser1[HOSTS][Other],$server['path_dop']);
		// Удалим первые два сервера.
		unset($upd_ser2[0], $upd_ser2[1]);
		// отсортируем сервера, что бы не начинать обновлятся с самого загруженного.
		shuffle($upd_ser2);
		//arsort($upd_ser2, SORT_NUMERIC);
		//print_r($upd_ser2);
	}
	if (isset($server['host'])==true)
	{
		$upd_ser2="";
		//echo "host=".$server['host']."\n";
		$upd_ser2[0]=$server['host'];
	}else
	{
		if (isset($upd_ser2) ==false)
		{
			$upd_ser2="";
			$upd_ser2[0]='http://update.eset.com/'.$server['path_dop'].'/';
		}
	}
	//print_r($upd_ser2);
	if ($quiet==false) echo "Update base for NOD32 {$server['type']}\n";
	// Для каждого из хостов пробуем обновиться, пока не получится
	foreach ($upd_ser2 as $upd_ser3)
	{
		$error1=0;
		if ($quiet==false) echo "Checking {$upd_ser3}\n";

		// Определяем пути до файлов в зависимости от версии
		// Удаляем старый update.ver
		@unlink ($savepath."/arc/update.ver");
		if ($func_download($upd_ser3.$newpath1,"update.ver",$savepath."arc/update.ver",@$server['user'],@$server['password'], $proxy, $quiet) == false)
		{
			$error1=1;
			continue;
		 }
		// После выкачивания update.ver преобразуем путь сервера.
		$upd_ser3=url_ser($upd_ser3, $server['path_dop']);

		if(substr(file_get_contents($savepath."/arc/update.ver"), 0,3)=='Rar')
		{
			rename($savepath."arc/update.ver", $savepath."arc/update.rar");
			$rar_val=func_rar($savepath.'arc', 'update.rar', $unrar);
			if ($rar_val==true)
			{
				echo 'The archiver '.$unrar['path'].' can not extract the rar archive';
				echo "\n";
				$error6=1;
				$error1=1;
			}
			@unlink($savepath."/arc/update.rar");
			if ($error6==true) break;
		}

		//$updatedb=@parse_ini_file($savepath."/arc/update.ver", true, INI_SCANNER_RAW );
		$updatedb=@parseDB(@file_get_contents($savepath."/arc/update.ver"));
		if (isset($updatedb['HOSTS']['Other'])==true) $upd_ser['HOSTS']['Other']=$updatedb['HOSTS']['Other'];
		//print_r($updatedb[HOSTS]);

		// Сохраняем список серверов для будущего применения
		if (isset($upd_ser)==true)
		{
			$upd_ser1=createDB($upd_ser);
			file_put_contents($savepath."/arc/servers",$upd_ser1);
		}

		// Проверяем правильность update.ver
		if(!$updatedb || $updatedb['ENGINE0']==false)
		{
			if ($quiet==false) echo "Invalid server!\n";
			$error1=1;
			continue;
		}

		// Заносим список серверов в update.ver
		$new_update="";
		if( isset($updatedb['HOSTS']['Other']) == true ) $new_update['HOSTS']['Other']=$updatedb['HOSTS']['Other'];

		// Список файлов
		$files1 = scandir($savepath);

		// Проверяем каждый файл
		foreach ($files1 as $file)
		{
			// Если до этого была ошибка, то прекращаем обновление
			if ($error1==1) continue;
			$file_nup=explode('.', $file);
			if (isset($file_nup[1])==true && $file_nup[1]=="nup")
			{
				// Считываем данные из файла
				$handle = fopen($savepath.$file, "r");
				$contents = fread($handle, 550);
				fclose($handle);

				$data=parseDB($contents);
				if (isset($data['UPDATE_INFO']['name'])==true) $name_upd=@basename($updatedb[$data['UPDATE_INFO']['name']]['file']);
				// Если секция в update.ver существует
				if (isset($data['UPDATE_INFO']['name'])==true && isset($updatedb[$data['UPDATE_INFO']['name']])==true && (isset($section2[$data['UPDATE_INFO']['name']]) || isset($section1[$data['UPDATE_INFO']['name']])))
				{
					// Если build  файлов совпадают
					if ($data['UPDATE_INFO']['build'] == $updatedb[$data['UPDATE_INFO']['name']]['build'])
					{
						// Если размеры не совпадают
						$size5=filesize($savepath.$name_upd);
						if (abs($updatedb[$data['UPDATE_INFO']['name']]['size']-$size5) > $diff )
						{
							if ($quiet==false)
							{
								echo "size $name_upd from update.ver=".$updatedb[$data['UPDATE_INFO']['name']]['size']."\n";
								echo "size real of $file=".$size5."\n";
								echo "\nThe different size of files $file and $name_upd\n";
								echo "Delete old $file and upload new $name_upd\n";
							}
							unlink($savepath.$file);
							$file_new=$updatedb[$data['UPDATE_INFO']['name']]['file'];
							if($func_download($upd_ser3, $file_new, $savepath.$name_upd, @$server['user'],@$server['password'],$proxy, $quiet)==false)
							{
								$error1=1;
								if ($quiet==false) echo "Error downloading file $file_new \n";
								unlink($savepath.$file);
								break;
							}
						}
						// Если имена не совпадают
						if ($name_upd!=$file)
						{
							if ($quiet==false)	echo "Rename file $file in {$name_upd}\n";
							rename($savepath.$file, $savepath.$name_upd);
						}
						//Если секция в update.ver существует но билд файла меньше
						// Добавляем данные в update.ver
						$new_update[$data['UPDATE_INFO']['name']]=$updatedb[$data['UPDATE_INFO']['name']];
						$new_update[$data['UPDATE_INFO']['name']]['file']=$name_upd;
					}
					elseif($data['UPDATE_INFO']['build'] <= $updatedb[$data['UPDATE_INFO']['name']]['build'])
					{
						$file_new=$updatedb[$data['UPDATE_INFO']['name']]['file'];
						if ($quiet==false)
						{
							echo "\nThe  file $file older $name_upd\n";
							echo "Delete old $file and upload new $name_upd\n";
						}
						unlink($savepath.$file);
						if($func_download($upd_ser3, $file_new, $savepath.$name_upd, @$server['user'],@$server['password'],$proxy, $quiet)==false)
						{
							$error1=1;
							if ($quiet==false) echo "Error downloading file $file_new \n";
							unlink($savepath.$file);
							break;
						}
						// Если после скачивания размеры не совпадают
						$size5=filesize($savepath.$name_upd);
						//echo "size_file=$size5\n";
						//echo "size_file_upd.ver=".$updatedb[$data['UPDATE_INFO']['name']]['size']."\n";
						if (abs($updatedb[$data['UPDATE_INFO']['name']]['size']-$diff) > $size5)
						{
							$error1=1;
							echo "Error size of downloading file $file_new \n";
							unlink($savepath.$file);
							break;
						}
						// Добавляем данные в update.ver
						$new_update[$data['UPDATE_INFO']['name']]=$updatedb[$data['UPDATE_INFO']['name']];
						$new_update[$data['UPDATE_INFO']['name']]['file']=$name_upd;
						if(!file_exists($savepath.$name_upd)) break;
						else $new_update[$data['UPDATE_INFO']['name']]['size']=filesize($savepath.$name_upd);
						//Если файл более новый, чем в секции
					}
					elseif($data['UPDATE_INFO']['build'] > $updatedb[$data['UPDATE_INFO']['name']]['build'])
					{
						// Добавляем данные в update.ver
						$new_update[$data['UPDATE_INFO']['name']]=new_section1($data['UPDATE_INFO'], $file, $savepath);
					}
					// Если секции не существует, но она есть в section2  или билд нового файла существует и
					// меньше старого
				}
				elseif (isset($section2[$data['UPDATE_INFO']['name']]) || (isset($updatedb[$data['UPDATE_INFO']['name']]['build']) && $data['UPDATE_INFO']['build'] > $updatedb[$data['UPDATE_INFO']['name']]['build']))
				{
					// Добавляем данные в update.ver
					$new_update[$data['UPDATE_INFO']['name']]=new_section1($data['UPDATE_INFO'], $file ,$savepath);
				}
				else
				{
					if ($quiet==false){	echo "Erase needless file $file \n";}
					unlink($savepath.$file);
				}
				$new_update[$data['UPDATE_INFO']['name']]['size']=filesize($savepath.$name_upd);
				// Если нужно, удаляем ограничение версии для обновления компонентов.
				if ($server['compons']==true && $server['DelVer']==true && $new_update[$data['UPDATE_INFO']['name']][$server['num_vers_comp']]==true) unset($new_update[$data['UPDATE_INFO']['name']][$server['num_vers_comp']]);
			}

		}


		// Докачиваем нужные файлы
		foreach ($updatedb as $section=>$vars)
		{
			// Все названия секций в Верхний регистр.
			//echo 'section='.$section."\n";
			$section=strtoupper($section);
			// Если до этого была ошибка, то прекращаем обновление
			if ($error1==1) break;
			$file_nup=@substr($vars['file'], -4);
			//echo "file_nup=$file_nup\n";
			if ($file_nup != '.nup') $file_nup[1]="";
			$file_name=@basename($vars['file']);
			if( file_exists($savepath.$file_name)==false && $file_nup=='.nup' && (isset($section1[$section]) || isset($section2[$section] )))
			{
				if($func_download($upd_ser3,$vars['file'], $savepath.$file_name,@$server['user'],@$server['password'], $proxy, $quiet)==false)
				{
					$error1=1;
					break;
				}
				//echo "filesize=".filesize($savepath.$file_name)."\n";
				//echo "size_upd=".$vars['size']."\n";
				if (file_exists($savepath.$file_name))
				{
					$new_update[$section]=$vars;
					$new_update[$section]['file']=$file_name;
					$size5=filesize($savepath.$file_name);
					if(($vars['size'])-$diff <= $size5 &&  $size5 <= ($vars['size'])+$diff)
					{
						$new_update[$section]['size']=filesize($savepath.$file_name);
					}
					else
					{
						print_r($vars);
						echo "size_file=$size5\n";
						echo "size_file_upd.ver=".$vars['size']."\n";
						echo "Error size of loading file {$vars['file']}\n";
						$error1=1;
						break;
					}
				}
				else
				{
					echo "Error downloading file {$vars['file']}\n";
					$error1=1;
					break;
				}
			}
			//Формируем секции COMPATLIST и data0001 для 3 NOD
			elseif($section=="COMPATLIST" || $section=="data0001")	$new_update[$section]=$vars;
		}
		if ($error1==1) continue;
		else
		{
			//Сохраняем новый файл  update.ver
			//print_r($new_update[RA0]);
			$new_upd=createDB($new_update);
			//$version_new=
			file_put_contents($savepath."update.ver",$new_upd);
			file_put_contents($savepath.$server['path_dop']."/update.ver",$new_upd);
			$version_new=version1($new_update);
			if ($quiet==false)
			{
				echo "Old version  $version_old\n";
				echo "New version $version_new\n";
			}
			// При удачном обновлении прерываем цикл
			break;
		}
	}


	// Если все удачно, переписываем в WEB каталог
	if (file_exists($server['www'])==false) mkdir($server['www']);

	//Если была ошибка восстанавливаем базы из web

	if ($error1==true)
	{
		if ($quiet==false)
		{
			echo "ERROR of updating base {$server['type']}\n";
			echo "RESTORE bases\n";
		}
		//На всякий случай удалим файл servers. Вдруг он не из той версии
		unlink($savepath.'arc/servers');
		rmfiles($savepath);
		copyfiles($server['www'],  $savepath);
		// Если задан ящик отправляем почту
		if (isset($user_mail)==true)
		{
			$mail_text = $mail_text."\n!!! ERROR of update base for NOD32 ".$server['type']." last " .$version_old;
			$mail_cont = $mail_cont." Error ESET ".$server['type'].",";
			//mail($user_mail, "ERROR ESET ".$server['type']." ".$version_old, $text);
		}
	}
	else
	{
		if ($quiet==false)	echo "SUCCESS of updating bases {$server['type']}\n";
		//Переписываем новые базы в web каталог
		rmfiles($server['www']);
		copyfiles($savepath, $server['www'], $server['path_dop']);
		// Меняем права на файл
		exec('chown -R '.HTTP_USER.' '.$server['www']);
		// Если задан ящик и новая версия отправляем почту
		if ($version_new != $version_old)
		{
			if (isset($user_mail))
			{
				$mail_text = $mail_text."\nSUCCESS update base for NOD32 ".$server['type']." from  version " .$version_old." to version ".$version_new;
				$mail_cont = $mail_cont." Succees ESET ".$server['type'].",";
				//mail($user_mail, "SUCCESS ESET ".$server['type']."  ".$version_new, $text);
			}
		}
	}

	if ($quiet==false)	echo "\n\n";
}
if ($mail_text) mail($user_mail, $mail_cont, $mail_text);

umask($umask_files);
if ($quiet==false)	echo "Execution time ",round(microtime(true)-$start,4)," sec.\n";
?>
