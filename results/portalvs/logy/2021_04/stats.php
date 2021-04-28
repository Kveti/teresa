<?php


/**
 * function xml2array
 *
 * This function is part of the PHP manual.
 *
 * The PHP manual text and comments are covered by the Creative Commons 
 * Attribution 3.0 License, copyright (c) the PHP Documentation Group
 *
 * @author  k dot antczak at livedata dot pl
 * @date    2011-04-22 06:08 UTC
 * @link    http://www.php.net/manual/en/ref.simplexml.php#103617
 * @license http://www.php.net/license/index.php#doc-lic
 * @license http://creativecommons.org/licenses/by/3.0/
 * @license CC-BY-3.0 <http://spdx.org/licenses/CC-BY-3.0>
 */
function xml2array ( $xmlObject, $out = array () ): array
{
    foreach ( (array) $xmlObject as $index => $node )
        $out[$index] = ( is_object ( $node ) ) ? xml2array ( $node ) : $node;

    return $out;
}



$logy = scandir(__DIR__); //, array('..', '.'));

foreach ($logy as $zaznam)
{
	if (strpos($zaznam, "output") !== 0)
	{
		$logy = array_diff($logy, array($zaznam));
	}
}

$stats = array();
//foreach ($logy as $zaznam)
//{
        //echo __DIR__ . "/" . $logy[2];
	//$xmlko = simplexml_load_file(__DIR__ . "\\" . $logy[7]);
	$xmlko = simplexml_load_file("C:\\Users\\matov\\Documents\\web\\reporty5W2\\results\\portalvs\\logy\\2020Februar\output.xml");
	//$testy = $xmlko["robot"]["suite"]["test"][0];
	//var_dump($xmlko);
	//echo "\n\n\n";
	$xml_arr = xml2array($xmlko);
	//print_r($xml_arr);
	echo $xml_arr["suite"]["kw"]["status"]["@attributes"]["status"];

        //foreach ($logy as $zaznam)
        //{
        //    $path = explode("\\", __DIR__);
        //    $size = sizeof($path);
        //    unset($path[$size-1]);
        //    unset($path[$size-2]);
        //    $path[] = "results";
        //    $path[] = $project;
        //    $path[] = "logy";
        //    $path[] = "2020Februar";
        //    $path[] = $zaznam;
        //    //$path2 = implode("/", $path);
        //    $xml[] = simplexml_load_file(implode("/", $path));
        //}


	// mozno aj toto funguje
	//$xml   = simplexml_load_string($string, 'SimpleXMLElement', LIBXML_NOCDATA);
    //
    //$array = json_decode(json_encode((array)$xml), TRUE);


	//foreach($xml_arr["suite"]["kw"]["status"]["@attributes"] as $k=>$xml)
	//{
	//	//print_r($xml);
	//	echo "idx: " . $k . "\n";
    //
	//	echo "--------------------------------\n";
	//}
//}
