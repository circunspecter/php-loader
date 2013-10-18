<?php
error_reporting(E_ALL);
ini_set('display_errors','On');

require '../GrindNous/Loader.php';
\GrindNous\Loader::register_autoloader(array(
	'Classes' => 'Classes',
	'ExtPack\Classes' => 'ExtPack/Classes',
	'Classes\ExtPack2' => 'ExtPack2',
	'Underscores' => 'Underscores'
));

echo "AUTOLOAD CLASSES<hr>";
echo 'Class: '.\Classes\One::$prop.' | Folder: Classes/One.php<br>';
echo 'Class: '.\Classes\Utils\OneTool::$prop.' | Folder: Classes/Utils/OneTool.php<br>';
echo 'Class: '.\ExtPack\Classes\ExtClass::$prop.' | Folder: ExtPack/Classes/ExtClass.php<br>';
echo 'Class: '.\Classes\ExtPack2\AnotherExtClass::$prop.' | Folder: ExtPack2/AnotherExtClass.php<br>';
echo 'Class: '.Underscores_Util_Name::$prop.' | Folder: Underscores/Util/Name.php';

echo "<br><br>LOAD FILE<hr>";
$res = \GrindNous\Loader::file('incfolder/incfile.php');
var_dump($res);

echo "<br><br>LOAD FILE SENDING VARIABLES<hr>";
$res = \GrindNous\Loader::file('incfolder/incfile2.php', NULL, array('var' => TRUE));
echo $res;

echo "<br><br>LOAD FILE CAPTURING VARIABLES<hr>";
$res = \GrindNous\Loader::file('incfolder/incfile3.php', NULL, array('var' => TRUE), array('loaded_file_var'));
var_dump($res);
?>