<script type="text/javascript">
    $(document).ready(function() {
        // LINK: http://www.uploadify.com/documentation/
        // правил строку 174 в файле /js/jquery/uploadify/jquery.uploadify.v2.1.4.js (в .min также), 
        // т.е. добавлял 4 аргумент в функцию cancelFileUpload(key,true,true,false);
        var scriptURL = '/interactive/ajax.php?zone=admin&action=ajaxUserFilesUpload&<{$smarty.const.WLCMS_SESSION_NAME}>=<{$sessionID}>&UID=<{$arrPageData.pid}>&module=<{$arrPageData.pmodule}>&file_prefix=<{$arrPageData.userfileprefix}>';
        $('#userFile').uploadify({
            'queueID'        : 'fileQueue',
            'uploader'       : '/js/jquery/uploadify/uploadify.swf',
            'script'         : encodeURIComponent(scriptURL+'&uploadifyData=1&files_params=<{$arrPageData.files_params|serialize|base64_encode|urlencode}>'),
            'checkScript'    : encodeURIComponent(scriptURL+'&uploadifyCheck=1'),
            'expressInstall' : '/js/jquery/uploadify/images/expressInstall.swf',
            'cancelImg'      : '/js/jquery/uploadify/images/cancel.png',
            'buttonImg'      : '/js/jquery/uploadify/images/select_<{$lang}>.png',
            'rollover'       : true,
            'align'          : 'center',
            'width'          : 110,
            'height'         : 30,
            'multi'          : true,
            'queueSizeLimit' : '<{$arrPageData.user_can_upload}>',
            'folder'         : '<{$arrPageData.pmodule}>',
            'fileDataName'   : 'Filedata',
            'fileExt'        : '*.jpg;*.jpeg;*.gif;*.png',
            'fileDesc'       : 'Image Files (.jpg, .jpeg, .gif, .png)',
            'onCheck'        : function(event, data, key) {
                $('#userFile' + key).fadeTo('fast', 0.5, function(){ 
                    $('.percentage', this).addClass('itemExists').text(' - Exists');
                });
            },
            'onQueueFull'    : function (event, queueSizeLimit) {
                var msg;
                if(queueSizeLimit==0) msg = "The maximum files by user is <{$arrPageData.userfilesmax}>, so upload else impossible. Delete unused files and upload again another!";
                else msg = "The maximum size of the queue "+queueSizeLimit+" is full";
                $.jGrowl(msg, {
                    theme:  'error',
                    header: 'Queue is Full',
                    life:   4000,
                    sticky: false
                });
                return false;
            }, 
            'onClearQueue'   : function (event) {
                var msg = "The queue has been cleared.";
                $.jGrowl('<p></p>'+msg, {
                    theme: 	'warning',
                    header: 'Cleared Queue',
                    life:	4000,
                    sticky: false
                });
            },
            'onCancel'       : function (event, ID, fileObj, data) {
                var msg = "Cancelled uploading: "+fileObj.name;
                $.jGrowl('<p></p>'+msg, {
                    theme: 	'warning',
                    header: 'Cancelled Upload',
                    life:	4000,
                    sticky: false
                });
            },
            'onError'        : function (event, ID, fileObj, errorObj) {
                var msg;
                if (errorObj.type==="File Size")
                     msg = 'File: '+fileObj.name+'<br/>Error: '+errorObj.type+' Limit is '+Math.round(errorObj.info/1024)+'KB';
                else msg = 'Error type: '+errorObj.type+"<br/>Error Message: "+errorObj.info;
                $.jGrowl('<p></p>'+msg, {
                    theme: 	'error',
                    header: 'ERROR',
                    sticky: true
                });
                //$("#userFile" + ID).fadeOut(250, function() { $("#userFile" + ID).remove()});
                //return false;
            },
            'onComplete'     : function (event, ID, fileObj, response, data) {
                var size = Math.round(fileObj.size/1024);
                $.jGrowl('<p></p>'+fileObj.name+' - '+size+'KB', {
                    theme: 	'success',
                    header: 'Upload Complete',
                    life:	4000,
                    sticky: false
                });
            },
            'onAllComplete'  : function(event, data) {
                window.location.reload();
            }
        });         
    });
          
    function checkBoxOperations(task, value){
        var inputs = $('#operationTbl').find("input[type=checkbox]:checked:not(#checkAll)"); 
        if(inputs.length > 0) {
            var data = '';
            $.each( inputs, function() {
                data += '&'+[$(this).attr("name")]+'='+value;
            });
            window.location = "<{$arrPageData.current_url}>"+"<{$arrPageData.filter_url}>"+"&task="+task+data;
        }
    }
</script>

<div id="messages" class="<{if !empty($arrPageData.errors)}>error<{elseif !empty($arrPageData.messages)}>info<{else}>hidden_block<{/if}>">
<{if !empty($arrPageData.errors)}>
    <{$arrPageData.errors|@implode:'<br/>'}>
<{elseif !empty($arrPageData.messages)}>
    <{$arrPageData.messages|@implode:'<br/>'}>
<{/if}>
</div>
<table width="100%" cellspacing="5" cellpadding="0" border="0" class="list">
    <tbody>
        <tr>
            <td width="400" id="head" align="center"><h3>Форма загрузки</h3></td>
            <td id="head" align="center"><h3>Загруженные файлы</h3></td>
        </tr>
        <tr>
            <td valign="top" >
                <table width="100%" cellspacing="1" cellpadding="0" border="0" class="list">
                    <tbody>
                        <tr>
                            <td id="head"><{$smarty.const.FILES_UPLOAD_MULTIPLE_SELECT_FILES}>: </td>
                        </tr>
                        <tr>
                            <td >
                                <div id="upload_box">
                                    <fieldset>
                                        <legend><{$smarty.const.FILES_UPLOADIFY_ADDED}>:</legend>
                                        <div id="upload_links_box">
                                            <div id="userFile">
                                                <{$smarty.const.FILES_UPLOAD_ERROR_JAVASCRIPT}>
                                            </div>
                                            <div id="fileQueue"></div>
                                            <a href="javascript:<{*ajaxFormValidate('#userFile');*}>$('#userFile').uploadifyUpload();"><{$smarty.const.FILES_UPLOADIFY_DOWNLOAD}></a> |
                                            <a href="javascript:$('#userFile').uploadifyClearQueue();">
                                                <{$smarty.const.FILES_UPLOADIFY_CLEAR}>
                                            </a>
                                        </div>
                                    </fieldset>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td valign="top" >
                <table width="100%" border="0" cellspacing="1" cellpadding="0" class="list" id="operationTbl">
                    <tr>
                        <td id="headb" align="center" width="12">
                            <input class="checkboxes check_all" type="checkbox" value="0" onchange="SelectCheckBox(this)"/>
                        </td>
                        <td id="headb" align="center" width="12"><{$smarty.const.HEAD_ID}></td>
                        <td id="headb" align="left"><{$smarty.const.HEAD_NAME}></td>
                        <td id="headb" align="center" width="22"><{$smarty.const.HEAD_PUBLICATION}></td>
                        <td id="headb" align="center" width="35"><{$smarty.const.HEAD_DELETE}></td>
                    </tr>
<{section name=i loop=$items}>
                     <tr>
                        <td  class="inputs" align="center">
                            <input type="checkbox" class="checkboxes" name="arItems[<{$items[i].id}>]" onchange="SelectCheckBox(this)" value="1" />
                        </td>
                        <td  align="center"><{$items[i].id}></td>
                        <td >
                            <a href="<{$arrPageData.files_url|cat:$items[i].filename}>" onclick="return parent.hs.expand (this, { })" class="highslide"><{$items[i].filename}></a>
                        </td>                      
                        <td  align="center">
<{if $items[i].active==1}>
                            <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=publishItem&status=0&itemID="|cat:$items[i].id}>" title="Publication">
                                <img src="<{$arrPageData.system_images}>check.png" alt="Publication" title="Publication" />
                            </a>
<{else}>
                            <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=publishItem&status=1&itemID="|cat:$items[i].id}>" title="No Publication">
                                <img src="<{$arrPageData.system_images}>un_check.png" alt="No Publication" title="No Publication" />
                            </a>
<{/if}>
                        </td>
                        <td  align="center">
                            <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=deleteItem&itemID="|cat:$items[i].id}>" onclick="return confirm('Вы уверены что хотите удалить файл?');" title="Delete!">
                               <img src="<{$arrPageData.system_images}>delete.png" alt="Delete!" title="Delete!" />
                            </a>
                        </td>
                    </tr>
<{/section}>
                </table>

                <table width="100%" border="0" cellspacing="1" cellpadding="0" class="list">
                    <tr>
                        <td id="line" colspan="2"><{$smarty.const.SITE_COUNT_RECORDS}><{$arrPageData.total_items}></td>
                    </tr>
<{if $arrPageData.total_pages>1}>
                    <tr>
                        <td id = "line" colspan="2">
<!-- ++++++++++ Start PAGER ++++++++++++++++++++++++++++++++++++++++++++++++ -->
<{include file='common/pager.tpl' arrPager=$arrPageData.pager page=$arrPageData.page showTitle=1 showFirstLast=0 showPrevNext=0}>
<!-- ++++++++++ End PAGER ++++++++++++++++++++++++++++++++++++++++++++++++++ -->
                        </td>
                    </tr>
<{/if}>
                    <tr>
                        <td id = "line">
                            <{if $arrPageData.total_items>0}>
                                Выбранные: <br/>
                                <a href="javascript:void(0)" onclick="checkBoxOperations('publishItems', '1')">
                                    Опубликовать
                                </a>
                                <br/>
                                <a href="javascript:void(0)" onclick="checkBoxOperations('publishItems', '0')">
                                    Не публиковать
                                </a>
                                <br/>
                                <a href="javascript:void(0)" onclick="checkBoxOperations('deleteItems', '1')">
                                    Удалить
                                </a>  
                            <{/if}>
                        </td>
                        <td id="controls-box" >
                            <a class="exit-button" href="javascript:void(0)" onclick="parent.window.hs.close();" title="<{$smarty.const.BUTTON_EXIT}>"></a>
                            <a class="reload-button" href="javascript:void(0)" onclick="window.location.reload();" title="<{$smarty.const.BUTTON_RELOAD}>"></a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </tbody>
</table>

