<?php

include(GetLangFileName($GLOBALS['DOCUMENT_ROOT'].'/bitrix/modules/popov/lang/', '/options.php'));
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/modules/main/options.php');

$module_id = 'popov';
CModule::IncludeModule($module_id);

$defaultOptions = \Bitrix\Main\Config\Option::getDefaults($module_id); // настройки по умолчанию

CModule::IncludeModule('iblock');
$MOD_RIGHT = $APPLICATION->GetGroupRight($module_id);
if($MOD_RIGHT>='R'):

	// set up form
	$arAllOptions = Array( // доступные нам поля для настроек
		array("date_format", GetMessage('REFERENCES_DATE_FORMAT'), $defaultOptions['date_format'], array('text') ),
		array("max_image_size", GetMessage('REFERENCES_MAX_IMAGE_SIZE'), $defaultOptions['max_image_size'], array('text') ),
	);

if($MOD_RIGHT>='Y' || $USER->IsAdmin()):

	/*if ($REQUEST_METHOD=='GET' && strlen($RestoreDefaults)>0 && check_bitrix_sessid()){
		COption::RemoveOption($module_id);
		$z = CGroup::GetList($v1='id',$v2='asc', array('ACTIVE' => 'Y', 'ADMIN' => 'N'));
		while($zr = $z->Fetch())
			$APPLICATION->DelGroupRight($module_id, array($zr['ID']));
	} */

	if($REQUEST_METHOD=='POST' && strlen($Update)>0 && check_bitrix_sessid()){
		foreach($arAllOptions as $option){
			if(!is_array($option) || isset($option['note']))
				continue;

			$name = $option[0];
			$val = ${$name};
			if($option[3][0] == 'checkbox' && $val != 'Y')
				$val = 'N';
			if($option[3][0] == 'multiselectbox')
				$val = @implode(',', $val);
			if ($name == 'image_max_width' || $name == 'image_max_height')
				$val = (int) $val;
			
			COption::SetOptionString($module_id, $name, $val, $option[1]);
		}
	}

endif; //if($MOD_RIGHT>="W"):

$aTabs = array();
$aTabs[] = array('DIV' => 'set', 'TAB' => GetMessage('MAIN_TAB_SET'), 'ICON' => 'popov_settings', 'TITLE' => GetMessage('MAIN_TAB_TITLE_SET'));
$aTabs[] = array('DIV' => 'rights', 'TAB' => GetMessage('MAIN_TAB_RIGHTS'), 'ICON' => 'popov_settings', 'TITLE' => GetMessage('MAIN_TAB_TITLE_RIGHTS'));

$tabControl = new CAdminTabControl('tabControl', $aTabs);
?>
<? $tabControl->Begin(); ?>
<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialcharsbx($mid)?>&lang=<?=LANGUAGE_ID?>" name="popov_settings">
<?$tabControl->BeginNextTab();?>
<?__AdmSettingsDrawList('popov', $arAllOptions);?>
<?$tabControl->BeginNextTab();?>
<?require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/admin/group_rights.php');?>
<?$tabControl->Buttons();?>
<script language="JavaScript">
function RestoreDefaults()
{
	if(confirm('<?echo AddSlashes(GetMessage('MAIN_HINT_RESTORE_DEFAULTS_WARNING'))?>'))
		window.location = "<?echo $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?echo LANG?>&mid=<?echo rawurlencode($mid)."&".bitrix_sessid_get();?>";
}
</script>
<input type="submit" name="Update" <?if ($MOD_RIGHT<'W') echo "disabled" ?> value="<?echo GetMessage('MAIN_SAVE')?>">
<input type="reset" name="reset" value="<?echo GetMessage('MAIN_RESET')?>">
<input type="hidden" name="Update" value="Y">
<?=bitrix_sessid_post();?>
<input type="button" <?if ($MOD_RIGHT<'W') echo "disabled" ?> title="<?echo GetMessage('MAIN_HINT_RESTORE_DEFAULTS')?>" OnClick="RestoreDefaults();" value="<?echo GetMessage('MAIN_RESTORE_DEFAULTS')?>">
<?$tabControl->End();?>
</form>
<?endif;
?>