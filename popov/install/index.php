<?

Class popov extends CModule
{
var $MODULE_ID = "popov";
var $MODULE_VERSION;
var $MODULE_VERSION_DATE;
var $MODULE_NAME;
var $MODULE_DESCRIPTION;
var $MODULE_CSS;

function popov()
{
$arModuleVersion = array();

$path = str_replace("\\", "/", __FILE__);
$path = substr($path, 0, strlen($path) - strlen("/index.php"));
include($path."/version.php");

if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
{
$this->MODULE_VERSION = $arModuleVersion["VERSION"];
$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
}
$this->MODULE_NAME = "popov – модуль с компонентом";
$this->MODULE_DESCRIPTION = "После установки вы сможете пользоваться компонентом popov:date.current";
}

function InstallFiles()
{
CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/popov/install/components",
             $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
return true;
}

function UnInstallFiles()
{
DeleteDirFilesEx("/bitrix/components/popov");
return true;
}

function DoInstall()
{
global $DOCUMENT_ROOT, $APPLICATION;
$this->InstallFiles();
RegisterModule("popov");
$APPLICATION->IncludeAdminFile("Установка модуля popov", $DOCUMENT_ROOT."/bitrix/modules/popov/install/step.php");
}

function DoUninstall()
{
global $DOCUMENT_ROOT, $APPLICATION;
$this->UnInstallFiles();
UnRegisterModule("popov");
$APPLICATION->IncludeAdminFile("Деинсталляция модуля popov", $DOCUMENT_ROOT."/bitrix/modules/popov/install/unstep.php");
}

}
?>