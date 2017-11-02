<?php /* Smarty version Smarty-3.1.14, created on 2017-11-02 20:13:09
         compiled from "tpl/frontend/smart/core/header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:201333182059fb6035561108-26095939%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '47ac7692d30846f574104e6574337127c77cca4d' => 
    array (
      0 => 'tpl/frontend/smart/core/header.tpl',
      1 => 1509645563,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '201333182059fb6035561108-26095939',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'lang' => 0,
    'mainMenu' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_59fb60355912b3_40829079',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59fb60355912b3_40829079')) {function content_59fb60355912b3_40829079($_smarty_tpl) {?><div class="main-box main-box-header">
    <div class="midlle-container">
        <header>
            <a href="/" class="logo" style="background-image: url('/images/logo-<?php echo $_smarty_tpl->tpl_vars['lang']->value;?>
.png');"></a>
            <?php echo $_smarty_tpl->getSubTemplate ('menu/main.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('arItems'=>$_smarty_tpl->tpl_vars['mainMenu']->value,'marginLevel'=>0), 0);?>

            <?php echo $_smarty_tpl->getSubTemplate ('menu/langs.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

        </header>
    </div>
</div>
<?php }} ?>