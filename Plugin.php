<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * 图片放大插件，放大倍数请自行设置.
 * Forked from 梁先生呀（http://539go.com）
 * 
 * @package pScaleUp_plus
 * @author yanhy2000
 * @version 1.1.1
 * @link https://github.om/yanhy2000/pScaleUp_plus
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
    .overlay .caption {
        position: absolute;
        bottom: 20px;
        color: white;
        background: rgba(0, 0, 0, 0.7);
        padding: 10px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        max-width: 90%;
        text-align: center;
    }
    .overlay .nav-button {
        position: absolute;
        top: 50%;
        width: 50px;
        height: 50px;
        background: rgba(0, 0, 0, 0.5);
        color: white;
        border: none;
        border-radius: 50%;
        font-size: 24px;
        cursor: pointer;
    }
    .overlay .prev-button {
        left: 20px;
    }
    .overlay .next-button {
        right: 20px;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var imgs = document.querySelectorAll('img');
        var overlay = document.createElement('div');
        overlay.className = 'overlay';
        
        var overlayImg = document.createElement('img');
        var caption = document.createElement('div');
        caption.className = 'caption';
        
        var prevButton = document.createElement('button');
        prevButton.className = 'nav-button prev-button';
        prevButton.innerHTML = '❮';
        
        var nextButton = document.createElement('button');
        nextButton.className = 'nav-button next-button';
        nextButton.innerHTML = '❯';
        
        overlay.appendChild(overlayImg);
        overlay.appendChild(caption);
        overlay.appendChild(prevButton);
        overlay.appendChild(nextButton);
        
        document.body.appendChild(overlay);

        var currentIndex = 0;

        function showImage(index) {
            if (index < 0 || index >= imgs.length) return;
            currentIndex = index;
            var img = imgs[index];
            overlayImg.src = img.src;
            caption.innerHTML = img.title || '';
            overlayImg.style.transform = 'scale(' + {SIZE} + ')';
            overlay.classList.add('active');
        }

        Array.prototype.forEach.call(imgs, function(el, index) {
            el.addEventListener('click', function() {
                showImage(index);
            });
        });

        overlay.addEventListener('click', function() {
            overlay.classList.remove('active');
        });

        prevButton.addEventListener('click', function(event) {
            event.stopPropagation();
            showImage(currentIndex - 1);
        });

        nextButton.addEventListener('click', function(event) {
            event.stopPropagation();
            showImage(currentIndex + 1);
        });

        document.addEventListener('keydown', function(event) {
            if (!overlay.classList.contains('active')) return;
            if (event.key === 'ArrowLeft') {
                showImage(currentIndex - 1);
            } else if (event.key === 'ArrowRight') {
                showImage(currentIndex + 1);
            } else if (event.key === 'Escape') {
                overlay.classList.remove('active');
            }
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
