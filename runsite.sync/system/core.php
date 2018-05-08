<?php 

namespace Sync;

require_once './runsite.sync/config/base.php';
require_once './runsite.sync/config/ftp.php';
require_once './runsite.sync/config/ignore.php';


require_once './runsite.sync/system/directory.php';
require_once './runsite.sync/system/buffer.php';
require_once './runsite.sync/system/ftp.php';









$do = $argv[1];
$arg = $argv[2];


if($do != 'buffer' and $do != 'run')
	die(' - Failed command: ' . $do);

if(!empty($arg) and $arg != '-down' and $arg != '-state' and $arg != '-clear')
	die(' - Failed argument: ' . $arg);



if($do == 'buffer')
{
	popen('cls', 'w');

	if(isset($arg) and $arg == '-down')
	{
		Buffer::down();
		echo 'Runsite.Sync: Buffer down complete.' . "\n\n";
	}
	elseif(isset($arg) and $arg == '-clear')
	{
		Buffer::clear();
		echo 'Runsite.Sync: Buffer clear complete.' . "\n\n";
	}
	else{
		$directory = new Directory;
		$directory->read_recursive();
		Buffer::write($directory->list);

		echo 'Runsite.Sync: Buffer complete.' . "\n\n";
	}

}

if($do == 'run')
{




	$state = false;
	if(isset($arg) and $arg == '-state') $state = true;



	$buffers = Buffer::get_list();

	$new = Buffer::get($buffers[0]);
	$old = Buffer::get($buffers[1]);

	$new_data = Buffer::transform($new);
	$old_data = Buffer::transform($old);

	$to_create = false;
	$to_remove = false;
	$to_update = false;

	if($state)
	{
		echo "\n\n";
		echo "\033[" . "0;32" . "m" . " * * * DISPLAY ONLY * * * " . "\033[0m";
		echo "\n\n";
	}


	echo "\n";
	foreach($new_data as $new_path=>$new_item)
	{
		// якшо шляху немає в старому масиві - значить файл треба СТВОРИТИ
		if(! isset($old_data[$new_path]))
		{
			$to_create[] = array('path' => $new_path, 'type' => $new_item['item_type']);
			if($state)
			{
				echo "\033[" . "0;32" . "m" . "to create: " . "\033[0m";
				echo $new_path . ' ['.$new_item['item_type'].']' . "\n";
			}
			
		}

	}

	echo "\n";
	foreach($old_data as $old_path=>$old_item)
	{
		// якшо шляху немає в старому масиві - значить файл треба ВИДАЛИТИ
		if(! isset($new_data[$old_path]))
		{
			$to_remove[] = array('path' => $old_path, 'type' => $old_item['item_type']);
			if($state)
			{
				echo "\033[" . "0;31" . "m" . "to remove: " . "\033[0m";
				echo $old_path . ' ['.$old_item['item_type'].']' . "\n";
			}
			
		}

	}

	echo "\n";
	foreach($new_data as $new_path=>$new_item)
	{
		if(isset($old_data[$new_path]))
		{
			if($new_item['item_modification_time'] != $old_data[$new_path]['item_modification_time'])
			{
				$to_update[] = array('path' => $new_path, 'type' => $new_item['item_type']);
				if($state)
				{
					echo "\033[" . "1;33" . "m" . "to update: " . "\033[0m";
					echo $new_path . ' ['.$new_item['item_type'].']' . "\n";
				}
				
			}
		}
	}


	if(!$to_create and !$to_remove and !$to_update)
	{
		echo "\n\n";
		echo "\033[" . "0;32" . "m" . " [No items] " . "\033[0m";
		echo "\n\n";
	}




	// дії на сервері
	if(! $state)
	{
		echo "\n\n\n";
		echo "\033[" . "0;31" . "m" . " * * * FTP * * * " . "\033[0m";
		echo "\n\n\n";

		$ftp = new Ftp();

		$ftp->upload_arr($to_create);
		$ftp->upload_arr($to_update);

		$ftp->remove_arr($to_remove);
	}


}