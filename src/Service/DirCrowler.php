<?php
namespace App\Service;

class DirCrowler
{
	public static function cleanArrayIndexes(array $arr)
	{
		$flipedArr = array_flip($arr);
		$i = 0;
		foreach($flipedArr as $k=>$v)
		{
			$flipedArr[$k] = $i;
			$i++;
		}
		return array_flip($flipedArr);
	}

	public static function scanuj_dir(String $path): array 
	{
		$dirs_messy_indexes = array_diff(scandir($path), array('..', '.'));
		$dirs = self::cleanArrayIndexes($dirs_messy_indexes);
		$dirsLen = sizeof($dirs);
		for ($i = 0; $i < $dirsLen;)
		{
			if(is_dir($path . DIRECTORY_SEPARATOR . $dirs[$i]))
			{
				$sub_dirs = self::scanuj_dir($path . DIRECTORY_SEPARATOR . $dirs[$i]);
				foreach ($sub_dirs as $k=>$sub_dir)
				{
					$sub_dirs[$k] = $dirs[$i] . DIRECTORY_SEPARATOR . $sub_dir;
				}
				$subLen = sizeof($sub_dirs);
				array_splice($dirs, $i, 1, $sub_dirs);
				$dirsLen = sizeof($dirs);
				$i = 0;
			}
			else
			{
				$i++;
			}
		}
		return $dirs;
	}
}