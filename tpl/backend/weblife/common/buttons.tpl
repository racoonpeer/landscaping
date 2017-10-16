<{* REQUIRE VARS: $task=string, $item=array() item *}>

<div class="buttons_list">
       <input class="buttons" name="submit" type="submit" value="<{$smarty.const.BUTTON_SAVE}>" />
       <input class="buttons" name="submit_apply" type="submit" value="<{$smarty.const.BUTTON_APPLY}>"  />
       <{if $item.id && $arrPageData.module!='comments'}>
           <input class="buttons" name="submit_copy" type="submit" value="<{$smarty.const.BUTTON_COPY}>"  
                  onclick="return userConfirm('copy', '<{$smarty.const.CONFIRM_COPY}>')" />
       <{/if}>
       
<{if $task=='addItem'}>
       <input class="buttons" name="submit_add" type="submit" value="<{$smarty.const.BUTTON_SAVE_ADD}>"  />
       <input class="buttons" name="submit_clear" type="reset" value="<{$smarty.const.BUTTON_CLEAR}>"
              onclick="return userConfirm('clear', '<{$smarty.const.CONFIRM_EMPTY}>')" />
<{/if}>

       <input class="buttons" name="submit_cancel" type="submit" value="<{$smarty.const.BUTTON_CANCEL}>"
              onclick="return userConfirm('cancel', '<{$smarty.const.CONFIRM_NOT_SAVE}>')" />

<{if $task=='editItem' && $item.id > $deleteIDLimit && (!isset($item.module) || (array_key_exists($item.module, $arrModules) && $item.id != $arrModules[$item.module].id)) }>
       <input class="buttons" name="submit_delete" type="submit" value="<{$smarty.const.BUTTON_DELETE}>"
              onclick="return userConfirm('delete', '<{$smarty.const.CONFIRM_DELETE}>')" />
<{/if}>
</div>

<script type="text/javascript">
<!--
function userConfirm(task, message) {
    if(window.confirm(message)) {
        switch (task) {
	  case 'copy':
	    window.location='<{$arrPageData.current_url|cat:"&task=addItem&copyID="|cat:$item.id}>';
	    break
          case 'clear':
            document.forms[0].reset();
            $.each(tinyMCE.editors, function() {
                this.setContent('');
            }); 
            
	    $.each($('input:text, textarea'), function() {
                $(this).val('');
            });
            if($('select').length>0){
                $.each($('select'), function(){
                    $('option', this).removeAttr("selected");
                    $('option:nth(0)', this).attr("selected", "selected");
                });
            }
            // catalog clear
            if($('#attrTable').length > 0) {
                $('#attrTable tbody').html('');
            }
            if($('#list_settings_selected_related').length >0 ) {
                $('#list_settings_selected_related').html('');
            }
            if($('#list_settings_all_related').length >0 ) {
                $('#list_settings_all_related').html('');
            }
            if($('#list_settings_all_kits').length >0 ) {
                $('#list_settings_all_kits').html('');
            }
            if($('#list_settings_selected_kits').length >0 ) {
                $('#list_settings_selected_kits').html('');
            }
            // main clear
            if($('#attrGroupsList').length>0) {
                $.each($('#attrGroupsList').find('input:checkbox'), function() {
                    $(this).removeAttr('checked');
                    updateAttributesList(this);
                });
            }
            if($('#filtersAllList').length>0) {
                $.each($('#filtersAllList').find('input:checkbox'), function() {
                    $(this).removeAttr('checked');
                    updateFiltersList(this);
                });
            }
            //attributes
            if($('#defaultVals').length>0) {
                $('#defaultVals').html('');
            }

	    break
	  case 'cancel':
	    window.location='<{$arrPageData.current_url|cat:$arrPageData.filter_url}>';
	    break
	  case 'delete':
	    window.location='<{$arrPageData.current_url|cat:"&task=deleteItem&itemID="|cat:$item.id}>';
	    break
	}   
    }
    return false; 
}
//-->
</script>