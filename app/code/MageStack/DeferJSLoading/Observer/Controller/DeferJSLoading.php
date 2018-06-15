<?php

namespace MageStack\DeferJSLoading\Observer\Controller;

use Magento\Framework\Event\ObserverInterface;
use MageStack\DeferJSLoading\Helper\Data;
use Magento\Framework\Event\Observer;

class DeferJSLoading implements ObserverInterface
{

    private $data;

    public function __construct(Data $data)
    {
        $this->data = $data;
    }

    public function execute(Observer $observer)
    {
        if ($this->data->getConfig('magestack_deferjs/general/enable')) {
            $response = $observer->getEvent()->getResponse();

            if (!$response) {
                return;
            }

            $html = $response->getBody();

            if (stripos($html, "</body>") === false) {
                return;
            }

            preg_match_all('~((<[\\s\\/]*script\\b[^>]*>)([^>]*)(<\\/script>))~i', $html, $scripts);

            if ($scripts and isset($scripts[0]) and $scripts[0]) {
                $html = preg_replace('~((<[\\s\\/]*script\\b[^>]*>)([^>]*)(<\\/script>))~i', '', $response->getBody());

                $scripts = implode("", $scripts[0]);

                $script = $this->data->deferIframeLoading();

                $html = str_ireplace("</body>", "{$scripts}{$script}</body>", $html);

                $response->setBody($html);
            }
        }
    }
}
