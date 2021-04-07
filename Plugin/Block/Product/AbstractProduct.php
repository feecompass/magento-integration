<?php

namespace Feecompass\Rankings\Plugin\Block\Product;

class AbstractProduct
{

		const SALES_ARG_ID_PREFIX = 'fc-sales-args-';

    public function afterGetProductDetailsHtml(
        \Magento\Catalog\Block\Product\AbstractProduct $subject,
        $result,
				\Magento\Catalog\Model\Product $product,
        $templateType = false,
        $displayIfNoReviews = false
        )
    {
				if ($product) {
				         $salesArgumentId = self::SALES_ARG_ID_PREFIX . $product->getId();
                 return $result . '<div id ="' . $salesArgumentId . '"></div>';
				}
				return $result;
    }
}
?>