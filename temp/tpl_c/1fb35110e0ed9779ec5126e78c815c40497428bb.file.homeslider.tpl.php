<?php /* Smarty version Smarty-3.1.14, created on 2017-11-02 20:13:09
         compiled from "tpl/frontend/smart/core/homeslider.tpl" */ ?>
<?php /*%%SmartyHeaderCode:41725969359fb60357acb10-74876618%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1fb35110e0ed9779ec5126e78c815c40497428bb' => 
    array (
      0 => 'tpl/frontend/smart/core/homeslider.tpl',
      1 => 1509645563,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '41725969359fb60357acb10-74876618',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'items' => 0,
    'item' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_59fb60358133d8_81382538',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59fb60358133d8_81382538')) {function content_59fb60358133d8_81382538($_smarty_tpl) {?><div class="homeslider">
    <div class="swiper-wrapper">
<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['items']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
        <div class="swiper-slide"  style="background-image: url('<?php echo $_smarty_tpl->tpl_vars['item']->value['image'];?>
');">
<?php if (!empty($_smarty_tpl->tpl_vars['item']->value['url'])){?>
            <a href="<?php echo $_smarty_tpl->tpl_vars['item']->value['url'];?>
" class="trigger"></a>
<?php }?>
            <div class='line'><?php echo $_smarty_tpl->tpl_vars['item']->value['title'];?>
</div>
        </div>
<?php } ?>
    </div>
    <div class="swiper-pagination"></div>
</div><?php }} ?>