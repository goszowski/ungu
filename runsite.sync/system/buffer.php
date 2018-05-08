<?php 

namespace Sync;

class Buffer {


	static function write($arr)
	{

		global $buffer_path;

		$filename = date('Y-m-d_H-i-s');
		$serialized_data = serialize($arr);

		return file_put_contents($buffer_path . '/' . $filename, $serialized_data);
	}

	static function get($filename)
	{

		global $buffer_path;

		$file_path = $buffer_path . '/' . $filename;

		if(! file_exists($file_path))
			die('Buffer file not exists: ' . $file_path . "\n");

		$serialized_data = file_get_contents($file_path);

		return unserialize($serialized_data);
	}

	static function get_list()
	{
		global $buffer_path;

		$list = false;

		$items = scandir($buffer_path);

		if($items)
		{
			foreach($items as $item)
			{
				if($item != '.' and $item != '..')
				{
					$list[] = $item;
				}
			}
		}

		rsort($list);

		return $list;
	}

	static function transform($arr)
	{
		$output = false;
		foreach($arr as $k=>$v)
		{
			$output[$v['item_path']] = array(

				'item_name' => $v['item_name'],
				'item_type' => $v['item_type'],
				'item_modification_time' => $v['item_modification_time']

			);
		}

		return $output;
	}


	static function down()
	{
		global $buffer_path;

		$list = Buffer::get_list();
		unlink($buffer_path . '/' . $list[0]);
	}

	static function clear()
	{
		global $buffer_path;

		$list = Buffer::get_list();

		if($list)
		{
			foreach($list as $item)
			{
				unlink($buffer_path . '/' . $item);
			}
		}
	}
}