<?php /* Smarty version Smarty-3.1.14, created on 2017-11-02 20:13:09
         compiled from "tpl/frontend/smart/menu/langs.tpl" */ ?>
<?php /*%%SmartyHeaderCode:88542317259fb6035697309-41241955%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '91a8ee69fc784168beb9a802925dc59dd108485d' => 
    array (
      0 => 'tpl/frontend/smart/menu/langs.tpl',
      1 => 1509645563,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '88542317259fb6035697309-41241955',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'arrLangs' => 0,
    'lnKey' => 0,
    'lang' => 0,
    'arLangsUrls' => 0,
    'arLnItem' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_59fb60357a2712_14049116',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59fb60357a2712_14049116')) {function content_59fb60357a2712_14049116($_smarty_tpl) {?><div class="soc">
    <ul>
<?php if (count($_smarty_tpl->tpl_vars['arrLangs']->value)>1){?>
<?php  $_smarty_tpl->tpl_vars['arLnItem'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['arLnItem']->_loop = false;
 $_smarty_tpl->tpl_vars['lnKey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['arrLangs']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['arLnItem']->key => $_smarty_tpl->tpl_vars['arLnItem']->value){
$_smarty_tpl->tpl_vars['arLnItem']->_loop = true;
 $_smarty_tpl->tpl_vars['lnKey']->value = $_smarty_tpl->tpl_vars['arLnItem']->key;
?>
        <li class="language <?php if ($_smarty_tpl->tpl_vars['lnKey']->value==$_smarty_tpl->tpl_vars['lang']->value){?>active<?php }?>">
<?php if ($_smarty_tpl->tpl_vars['lnKey']->value!=$_smarty_tpl->tpl_vars['lang']->value){?><a href="<?php echo $_smarty_tpl->tpl_vars['arLangsUrls']->value[$_smarty_tpl->tpl_vars['lnKey']->value];?>
"><?php }?>
            <?php echo $_smarty_tpl->tpl_vars['arLnItem']->value['title'];?>

<?php if ($_smarty_tpl->tpl_vars['lnKey']->value!=$_smarty_tpl->tpl_vars['lang']->value){?></a><?php }?>
        </li>
<?php } ?>
<?php }?>
        <li><a href="//facebook.com/landscaping.kiev.ua" class="fb" target="_blank"></a></li>
        <li><a href="//instagram.com/amazing_gardening_kiev" class="inst" target="_blank"></a></li>
    </ul>
</div>
<?php }} ?>