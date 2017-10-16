<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
<{if !empty($refresh.head)}>
        <{$refresh.head}>
<{/if}>
        <title><{$arrPageData.headTitle}></title>
        <meta http-equiv="content-type" content="text/html; charset=windows-1251">
        <meta http-equiv="imagetoolbar" content="no">
        <link rel="stylesheet" type="text/css" href="/css/backend/login.css" />
        <link rel="shortcut icon" href="<{$arrPageData.images_dir}>favicon.ico" />
<{if $bannedTime>0}>
        <script type="text/javascript">
            <!--
            var banTimerID = null;
            function updateBanTimer(){
                var banDate = new Date(<{$bannedTime}>*1000); // seconds*1000 in JavaScripts Milisecons Timestamp
                var nowDate = new Date();
                if( banDate >= nowDate && document.getElementById('banTimer') != null &&
                    nowDate.getFullYear() == banDate.getFullYear() &&
                    nowDate.getMonth()    == banDate.getMonth() &&
                    nowDate.getDate()     == banDate.getDate() ) {
                        var totalRemains = (banDate.getTime()-nowDate.getTime());
                        var RemainsSec=(parseInt(totalRemains/1000));
                        var RemainsFullDays=(parseInt(RemainsSec/(24*60*60)));
                        var secInLastDay=RemainsSec-RemainsFullDays*24*3600;
                        var RemainsFullHours=(parseInt(secInLastDay/3600));
                        //if (RemainsFullHours<10){RemainsFullHours="0"+RemainsFullHours};
                        var secInLastHour=secInLastDay-RemainsFullHours*3600;
                        var RemainsMinutes=(parseInt(secInLastHour/60));
                        //if (RemainsMinutes<10){RemainsMinutes="0"+RemainsMinutes};
                        var lastSec=secInLastHour-RemainsMinutes*60;
                        //if (lastSec<10){lastSec="0"+lastSec};
                        document.getElementById('banTimer').innerHTML = /*RemainsFullHours+" hours "+*/RemainsMinutes+' min '+lastSec+' sec';
                        document.loginForm.Submit2.disabled=true;
                } else if(banTimerID != null) {
                    clearInterval(banTimerID);
                    document.loginForm.Submit2.disabled=false;
                }

            }
            function checkForm(form){
                if(banTimerID != null){
                       form.submit();
                       return true;
                } else form.Submit2.disabled=true;
                return false;
            }
            banTimerID = setInterval("updateBanTimer()", 1000);
            //-->
        </script>
<{/if}>
    </head>
    <body>
<{if !empty($refresh.head) or !empty($refresh.body)}>
        <{$refresh.body}>
<{else}>
        <div class="typo3-login">
	<div class="typo3-login-container">
		<div class="typo3-login-wrap">
			<div class="panel panel-lg panel-login">
				<div class="panel-body">
		<form id="loginForm" name="loginForm" action="" method="post" onsubmit="<{if $bannedTime>0}>return checkForm(this);<{/if}>">
	


									
	<div class="form-group t3js-login-username-section" id="t3-login-username-section">
		<div class="form-control-wrap">
			<div class="form-control-holder">
				<div class="form-control-clearable">
                                    <input type="text" name="login" value="" size="36" class="form-control" placeholder="Login"/>
                                </div>
			</div>
		</div>
	</div>
	<div class="form-group t3js-login-password-section" id="t3-login-password-section">
		<div class="form-control-wrap">
			<div class="form-control-holder">
				<div class="form-control-clearable">
                                    <input name="pass" value="" type="password" size="36" class="form-control" placeholder="Password"/>
                                </div>
			</div>
		</div>
	</div>
<{if $showCode}>
    <div class="form-group" id="t3-login-submit-section">

                        <img alt="code" src="/interactive/cv_img.php?zone=admin" border="0" align="top"/> 
    </div>
    <div class="form-group" id="t3-login-submit-section">

        <input type="text" id="fConfirmationCode" name="fConfirmationCode" value="" maxlength="<{$smarty.const.IVALIDATOR_MAX_LENTH}>" size="10" class="form-control" placeholder="Security code"/>
    </div>
<{/if}>


                                <div class="form-group" id="t3-login-submit-section">
                                        <button class="btn btn-block btn-login t3js-login-submit" name="Submit2"><{$smarty.const.BUTTON_ENTER}></button>
                                </div>
                                
</form>
							</div>
						
				</div>
			</div>
		</div>
	</div>
<{/if}>
    </body>
</html>