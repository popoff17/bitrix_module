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

$tabControl = new CAdminTabControl("tabControl", array(
    array(
        "DIV" => "edit1",
        "TAB" => Loc::getMessage("MAIN_TAB_SET"),
        "TITLE" => Loc::getMessage("MAIN_TAB_TITLE_SET"),
    ),
));

if ((!empty($save) || !empty($restore)) && $request->isPost() && check_bitrix_sessid()) {
    if (!empty($restore)) {
        Option::delete(ADMIN_MODULE_NAME);
        CAdminMessage::showMessage(array(
            "MESSAGE" => Loc::getMessage("REFERENCES_OPTIONS_RESTORED"),
            "TYPE" => "OK",
        ));
    } elseif ($request->getPost('save_data') && ($request->getPost('save_data') > 0)) {
        Option::set(
            ADMIN_MODULE_NAME,
            "max_image_size",
            $request->getPost('max_image_size')
        );
        Option::set(
            ADMIN_MODULE_NAME,
            "date_format",
            $request->getPost('date_format')
        );
        CAdminMessage::showMessage(array(
            "MESSAGE" => Loc::getMessage("REFERENCES_OPTIONS_SAVED"),
            "TYPE" => "OK",
        ));
    } else {
        CAdminMessage::showMessage(Loc::getMessage("REFERENCES_INVALID_VALUE"));
    }
}

$tabControl->begin();
$defaultOptions = \Bitrix\Main\Config\Option::getDefaults(ADMIN_MODULE_NAME);
?>

<form method="post" action="<?=sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID)?>">
    <?php
    echo bitrix_sessid_post();
    $tabControl->beginNextTab();
    ?>
    <tr>
        <td width="40%">
            <label for="max_image_size"><?=Loc::getMessage("REFERENCES_MAX_IMAGE_SIZE") ?>:</label>
        <td width="60%">
            <input type="text"
                   size="50"
                   id="max_image_size"
                   name="max_image_size"
                   value="<?=String::htmlEncode(Option::get(ADMIN_MODULE_NAME, "max_image_size", $defaultOptions['max_image_size']));?>"
                   />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <label for="date_format"><?=Loc::getMessage("REFERENCES_DATE_FORMAT") ?>:</label>
        <td width="60%">
            <input type="text"
                   size="50"
				   id="date_format"
                   name="date_format"
                   value="<?=String::htmlEncode(Option::get(ADMIN_MODULE_NAME, "date_format", $defaultOptions['date_format']));?>"
                   />
        </td>
    </tr>

    <?php
    $tabControl->buttons();
    ?>
    <input type="hidden" name="save_data" value="1" />
   <input type="submit"
           name="save"
           value="<?=Loc::getMessage("MAIN_SAVE") ?>"
           title="<?=Loc::getMessage("MAIN_OPT_SAVE_TITLE") ?>"
           class="adm-btn-save"
           />
    <input type="submit"
           name="restore"
           title="<?=Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
           onclick="return confirm('<?= AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')"
           value="<?=Loc::getMessage("MAIN_RESTORE_DEFAULTS") ?>"
           />
    <?php
    $tabControl->end();
    ?>
</form>
