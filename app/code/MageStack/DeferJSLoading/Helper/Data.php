<?php

namespace MageStack\DeferJSLoading\Helper;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /*
     * getting configuration from store settings
     */
    public function getConfig($optionString, $scopeCode = null)
    {
        return $this->scopeConfig->getValue($optionString, ScopeInterface::SCOPE_STORE, $scopeCode);
    }

    /*
     * defer iframe loading
     */
    public function deferIframeLoading()
    {
        //defer iframe loading
        if (!$this->getConfig('magestack_deferjs/general/enable_iframe')) {
            return '';
        }
            $script = <<<HTML
    <script>
        require(['jquery'],function($){
            $(function(){
                $('body','html').find('iframe').each(function(){
                    var src = $(this).attr('src');
                    $(this).attr('src','');
                    $(this).attr('data-src',src);
                });
            });
            $(window).load(function(){
                $('body','html').find('iframe').each(function(){
                    $(this).attr('src', $(this).attr('data-src'));
                });
            })
        })
    </script>
HTML;
        return $script;
    }
}
