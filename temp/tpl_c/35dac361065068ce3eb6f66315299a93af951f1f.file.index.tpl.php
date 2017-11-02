<?php /* Smarty version Smarty-3.1.14, created on 2017-11-02 20:13:09
         compiled from "tpl/frontend/smart/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:125091675459fb6035302104-85469577%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '35dac361065068ce3eb6f66315299a93af951f1f' => 
    array (
      0 => 'tpl/frontend/smart/index.tpl',
      1 => 1509645563,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '125091675459fb6035302104-85469577',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'lang' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_59fb60353d4357_29651167',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59fb60353d4357_29651167')) {function content_59fb60353d4357_29651167($_smarty_tpl) {?><!DOCTYPE html>
<html lang="<?php echo $_smarty_tpl->tpl_vars['lang']->value;?>
">
    <?php echo $_smarty_tpl->getSubTemplate ("core/head.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

    <body>
        <?php echo $_smarty_tpl->getSubTemplate ('core/header.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

        <?php echo $_smarty_tpl->getSubTemplate ('core/homeslider.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

    </body>
</html><?php }} ?>