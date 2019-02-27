<?php

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Text\String;

defined('ADMIN_MODULE_NAME') or define('ADMIN_MODULE_NAME', 'popov');

if (!$USER->isAdmin()) {
    $APPLICATION->authForm('Nope');
}

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();

Loc::loadMessages($context->getServer()->getDocumentRoot()."/bitrix/modules/main/options.php");
Loc::loadMessages(__FILE__);
$RIGHT = $APPLICATION->GetGroupRight(ADMIN_MODULE_NAME);
if($RIGHT >= "R") :
$defaultOptions = \Bitrix\Main\Config\Option::getDefaults(ADMIN_MODULE_NAME); // настройки по умолчанию
$arAllOptions = Array( // доступные нам поля для настроек
    //array(ид в языковом файле, значение по умолчанию, имя поля,),
    array("REFERENCES_MAX_IMAGE_SIZE", $defaultOptions['max_image_size'], "max_image_size", ),
    array("REFERENCES_DATE_FORMAT", $defaultOptions['date_format'], "date_format", ),
);

// вкоалки модуля
$tabControl = new CAdminTabControl("tabControl", array(
    array(
        "DIV" => "edit1",
        "TAB" => Loc::getMessage("MAIN_TAB_SET"),
        "TITLE" => Loc::getMessage("MAIN_TAB_TITLE_SET"),
    ),
	array(
		"DIV" => "edit2", 
		"TAB" => GetMessage("MAIN_TAB_RIGHTS"), 
		"ICON" => "perfmon_settings", 
		"TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")
	),
));

// сохранение данных
if ((!empty($save) || !empty($restore)) && $request->isPost() && check_bitrix_sessid()) {

    if (!empty($restore)) {
        Option::delete(ADMIN_MODULE_NAME);
        CAdminMessage::showMessage(array(
            "MESSAGE" => Loc::getMessage("REFERENCES_OPTIONS_RESTORED"),
            "TYPE" => "OK",
        ));
    } elseif ($request->getPost('save_data') && ($request->getPost('save_data') > 0)) {

        foreach($arAllOptions as $arOption){
			Option::set(ADMIN_MODULE_NAME, $arOption[2], $request->getPost($arOption[2]) );
        }
		
        CAdminMessage::showMessage(array(
            "MESSAGE" => Loc::getMessage("REFERENCES_OPTIONS_SAVED"),
            "TYPE" => "OK",
        ));
    } else {
        CAdminMessage::showMessage(Loc::getMessage("REFERENCES_INVALID_VALUE"));
    }
	
}
?>
<?$tabControl->begin();?>
<form method="post" action="<?=sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID)?>">
    <?php
    echo bitrix_sessid_post();
    $tabControl->beginNextTab(); 
    ?>
	<? foreach($arAllOptions as $arOption):?>
    <tr>
        <td width="40%">
            <label for="<?=$arOption[2]?>"><?=Loc::getMessage($arOption[0]) ?>:</label>
        <td width="60%">
            <input type="text" size="50" id="<?=$arOption[2]?>" name="<?=$arOption[2]?>" value="<?=String::htmlEncode(Option::get(ADMIN_MODULE_NAME, $arOption[2], $defaultOptions[$arOption[2]]));?>" />
        </td>
    </tr>
	<? endforeach;?>
    
	<?$tabControl->BeginNextTab();?>
    <?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
	
    <?php $tabControl->buttons(); ?>
    <input type="hidden" name="save_data" value="1" />
	<input type="submit" name="save" value="<?=Loc::getMessage("MAIN_SAVE") ?>" title="<?=Loc::getMessage("MAIN_OPT_SAVE_TITLE") ?>" class="adm-btn-save" <?if ($RIGHT<"W") echo "disabled" ?> />
    <input type="submit" name="restore" title="<?=Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>" onclick="return confirm('<?= AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')" value="<?=Loc::getMessage("MAIN_RESTORE_DEFAULTS") ?>" <?if ($RIGHT<"W") echo "disabled" ?> />
    <?php $tabControl->end(); ?>
</form>

<?endif;?>