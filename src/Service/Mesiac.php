<?php
namespace App\Service;

class Mesiac
{
	// statické pole mesiacov a ich indexov
	static private $mena_mesiacov = array("01" => "Január", "02" => "Február", "03" => "Marec", "04" => "Apríl", "05" => "Máj", "06" => "Jún", "07" => "Júl", "08" => "August", "09" => "September", "10" => "Október", "11" => "November", "12" => "December");

	public static function dajMenoMesica(int $cislo_mesiaca): String
	{
	    return self::$mena_mesiacov[$cislo_mesiaca];
	}

	public static function dajIndexMesiaca(String $meno_mesiaca): int
	{
	    $indexy_mesiacov = array_flip(self::$mena_mesiacov);
	    return $indexy_mesiacov[$meno_mesiaca];
	}
}