<?php /* Smarty version Smarty-3.1.14, created on 2017-11-02 20:13:09
         compiled from "tpl/frontend/smart/core/head.tpl" */ ?>
<?php /*%%SmartyHeaderCode:114179967159fb60353df489-78871994%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '304bee3444d4ec425a2eeb78771cc459813bc25d' => 
    array (
      0 => 'tpl/frontend/smart/core/head.tpl',
      1 => 1509645563,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '114179967159fb60353df489-78871994',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'lang' => 0,
    'arrLangs' => 0,
    'arCategory' => 0,
    'HTMLHelper' => 0,
    'objSettingsInfo' => 0,
    'arrPageData' => 0,
    'style' => 0,
    'script' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_59fb603554bcd1_02308285',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59fb603554bcd1_02308285')) {function content_59fb603554bcd1_02308285($_smarty_tpl) {?><head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_smarty_tpl->tpl_vars['arrLangs']->value[$_smarty_tpl->tpl_vars['lang']->value]['charset'];?>
" />
    <title><?php echo $_smarty_tpl->tpl_vars['HTMLHelper']->value->prepareHeadTitle($_smarty_tpl->tpl_vars['arCategory']->value);?>
</title>
    <meta name="keywords" content="<?php echo $_smarty_tpl->tpl_vars['arCategory']->value['meta_key'];?>
"/>
    <meta name="description" content="<?php echo $_smarty_tpl->tpl_vars['arCategory']->value['meta_descr'];?>
"/>
<?php if ($_smarty_tpl->tpl_vars['arCategory']->value['meta_robots']){?>
    <meta name="robots" content="<?php echo $_smarty_tpl->tpl_vars['arCategory']->value['meta_robots'];?>
" />
<?php }?>
<?php if ($_smarty_tpl->tpl_vars['objSettingsInfo']->value->logo){?>
    <link rel="icon" href="<?php echo $_smarty_tpl->tpl_vars['objSettingsInfo']->value->logo;?>
" type="image/x-icon" />
    <link rel="shortcut icon" href="<?php echo $_smarty_tpl->tpl_vars['objSettingsInfo']->value->logo;?>
" type="image/x-icon" />
<?php }?>
<?php  $_smarty_tpl->tpl_vars['style'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['style']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['arrPageData']->value['headCss']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['style']->key => $_smarty_tpl->tpl_vars['style']->value){
$_smarty_tpl->tpl_vars['style']->_loop = true;
?>
    <link href="<?php echo $_smarty_tpl->tpl_vars['style']->value;?>
?v=<?php echo time();?>
" type="text/css" rel="stylesheet"/>
<?php }
if (!$_smarty_tpl->tpl_vars['style']->_loop) {
?>
    <link href="/css/store/common.css?v=<?php echo time();?>
" type="text/css" rel="stylesheet"/>
<?php } ?>
<?php  $_smarty_tpl->tpl_vars['script'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['script']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['arrPageData']->value['headScripts']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['script']->key => $_smarty_tpl->tpl_vars['script']->value){
$_smarty_tpl->tpl_vars['script']->_loop = true;
?>
    <script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['script']->value;?>
?v=<?php echo time();?>
"></script>
<?php } ?>
    <?php echo $_smarty_tpl->getSubTemplate ('core/header-extra.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</head><?php }} ?>