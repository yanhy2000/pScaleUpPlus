<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 图片放大插件，放大倍数请自行设置.
 * Forked from 梁先生呀（http://539go.com）
 * 
 * @package pScaleUp_plus
 * @author yanhy2000
 * @version 2.0.1
 * @link https://github.om/yanhy2000/pScaleUp_plus
 */
class pScaleUpPlus_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     * 
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Archive')->footer = 'pScaleUpPlus_Plugin::footer';
    }
    
    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     * 
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate(){}
    
    /**
     * 获取插件配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        $size = new Typecho_Widget_Helper_Form_Element_Text(
            'size', NULL, '1.6', _t('点击图片后的初始放大倍数 (默认为1.6)'));
        $form->addInput($size);
        
        $sHover = new Typecho_Widget_Helper_Form_Element_Text(
            'sHover', NULL, '1.05', _t('鼠标放上去的变化倍数 (默认为1.05 建议1.05),1为禁用'));
        $form->addInput($sHover);
    }
    
    /**
     * 个人用户的配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}
    
    /**
     * 在页面底部输出自定义JS和CSS
     */
    public static function footer()
    {
        $size = Typecho_Widget::widget('Widget_Options')->plugin('pScaleUpPlus')->size;
        $size = $size <= 0 ? 1.6 : $size;
        $sHover = Typecho_Widget::widget('Widget_Options')->plugin('pScaleUpPlus')->sHover;
        $sHover = $sHover <= 0 ? 1.05 : $sHover;
        
        require_once 'footer_code.php';
        echo str_replace(['{SIZE}', '{SHOVER}'], [$size, $sHover], $code);
    }
}
