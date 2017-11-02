<?php /* Smarty version Smarty-3.1.14, created on 2017-11-02 20:13:09
         compiled from "tpl/frontend/smart/core/href.tpl" */ ?>
<?php /*%%SmartyHeaderCode:118422689959fb6035630fb6-88970897%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '68863dedf4148aee313d39d010efab53745aba03' => 
    array (
      0 => 'tpl/frontend/smart/core/href.tpl',
      1 => 1509645563,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '118422689959fb6035630fb6-88970897',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'params' => 0,
    'arCategory' => 0,
    'UrlWL' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_59fb603568aea7_12671905',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59fb603568aea7_12671905')) {function content_59fb603568aea7_12671905($_smarty_tpl) {?><?php if (!isset($_smarty_tpl->tpl_vars['params']->value)){?><?php $_smarty_tpl->tpl_vars['params'] = new Smarty_variable('', null, 0);?><?php }?><?php echo $_smarty_tpl->tpl_vars['UrlWL']->value->buildCategoryUrl($_smarty_tpl->tpl_vars['arCategory']->value,$_smarty_tpl->tpl_vars['params']->value);?>
<?php }} ?>