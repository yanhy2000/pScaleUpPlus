<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * 图片放大插件，放大倍数请自行设置
 * 
 * @package pScaleUp_plus
 * @version 1.1.0_1
 */
class pScaleUp_Plugin implements Typecho_Plugin_Interface
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
        Typecho_Plugin::factory('Widget_Archive')->footer = array('pScaleUp_Plugin', 'footer');
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
            'size', NULL, '1.6', _t('点击图片后的放大倍数 (默认为1.6)'));
        $form->addInput($size);
        
        $sHover = new Typecho_Widget_Helper_Form_Element_Text(
            'sHover', NULL, '1', _t('鼠标放上去的变化倍数 (默认为1.0 建议1.005)'));
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
     * 插件实现方法
     * 
     * @access public
     * @return void
     */
    public static function render()
    {
        echo '<span class="message success">'
            . htmlspecialchars(Typecho_Widget::widget('Widget_Options')->plugin('pScaleUp')->word)
            . '</span>';
    }
    
    public static function footer()
    {
        $code = 
<<<EOL
<style>
    img {
        cursor: pointer;
        transition: transform 0.1s ease;
    }
    img:hover {
        transform: scale({SHOVER});
    }
    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(10px);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }
    .overlay img {
        border: 5px solid rgba(255, 255, 255, 0.7);
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
        max-width: 90%;
        max-height: 90%;
        transform: scale(1);
        transition: transform 0.3s ease;
    }
    .overlay.active {
        opacity: 1;
        pointer-events: all;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var imgs = document.querySelectorAll('img');
        var overlay = document.createElement('div');
        overlay.className = 'overlay';
        var overlayImg = document.createElement('img');
        overlay.appendChild(overlayImg);
        document.body.appendChild(overlay);

        Array.prototype.forEach.call(imgs, function(el) {
            el.addEventListener('click', function() {
                overlayImg.src = el.src;
                overlayImg.style.transform = 'scale(' + {SIZE} + ')';
                overlay.classList.add('active');
            });
        });

        overlay.addEventListener('click', function() {
            overlay.classList.remove('active');
        });
    });
</script>
EOL;
        $size = Typecho_Widget::widget('Widget_Options')->plugin('pScaleUp')->size;
        $size = $size <= 0 ? 1.6 : $size;
        $sHover = Typecho_Widget::widget('Widget_Options')->plugin('pScaleUp')->sHover;
        $sHover = $sHover <= 0 ? 1 : $sHover;
        
        $code = str_replace("{SIZE}", $size, $code);
        echo str_replace("{SHOVER}", $sHover, $code);
    }
}
