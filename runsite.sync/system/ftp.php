<?php 

namespace Sync;

class Ftp {

	protected $connection;
	protected $login_result;

	protected $ftp_path;

	function __construct()
	{
		global $ftp_connection, $ftp_path;

		$this->ftp_path = $ftp_path;

		$this->connection = ftp_connect($ftp_connection['host']);
		$this->login_result = ftp_login($this->connection, $ftp_connection['user'], $ftp_connection['password']);

		ftp_pasv($this->connection, true);
	}

	public function remove_arr($arr)
	{
		
		if($arr)
		{

			$arr = array_reverse($arr);

			foreach($arr as $item)
			{
				if($item['type'] == 'dir')
				{
					ftp_rmdir($this->connection, $this->ftp_path . $item['path']);

					echo "\033[" . "0;31" . "m" . "Directory removed: " . "\033[0m";
					echo $item['path'] . "\n";
				}
				else
				{
					ftp_delete($this->connection, $this->ftp_path . $item['path']);
					echo "\033[" . "0;31" . "m" . "File removed: " . "\033[0m";
					echo $item['path'] . "\n";
				}

			}
		}
	}

	public function upload_arr($arr)
	{
		if($arr)
		{
			foreach($arr as $item)
			{
				// якшо це папка то сворюєм на сервері
				if($item['type'] == 'dir')
				{
					ftp_mkdir($this->connection, $this->ftp_path . $item['path']);
					echo "\033[" . "1;33" . "m" . "Directory created: " . "\033[0m";
					echo $item['path'] . "\n";
				}

				// якшо це файл то завантажуємо його
				elseif($item['type'] == 'file')
				{
					ftp_put($this->connection, $this->ftp_path . $item['path'], $item['path'], FTP_BINARY);
					echo "\033[" . "1;33" . "m" . "File uploaded: " . "\033[0m";
					echo $item['path'] . "\n";
				}
			}
		}
		
	}
}