<?php 

namespace Sync;

class Directory {

	protected $irnore_paths;
	protected $scan_path;


	public $list = false;




	function __construct()
	{
		global $irnore_paths, $scan_path;

		$this->irnore_paths = $irnore_paths;
		$this->scan_path = $scan_path;
	}


	public function read_recursive($scan_path = false)
	{

		if(! $scan_path) $scan_path = $this->scan_path;

		$list = scandir($scan_path);
		if($list)
		{
			foreach($list as $item)
			{

				$item_path = $scan_path . $item;

				if($item != '.' and $item != '..' and ! in_array($item_path, $this->irnore_paths))
				{

					$item_type = 'file';
					if(is_dir($item_path)) $item_type = 'dir';

					if($item_type == 'file')
						$item_modification_time = filemtime($item_path);
					else 
						$item_modification_time = false;

					$this->list[] = array(

						'item_name' => $item,
						'item_path' => $item_path,
						'item_type' => $item_type,
						'item_modification_time' => $item_modification_time

					);

					if($item_type == 'dir')
						$this->read_recursive($item_path . '/');
				}
			}
		}
	}
}