<?php

declare(strict_types=1);

namespace StarHangar\TestTask\Model\Product\Type;

class NewProductType extends \Magento\Catalog\Model\Product\Type\AbstractType
{

   /**
    * Execute delete type data function
    *
    * @param \Magento\Catalog\Model\Product $product
    * @return void
    */
    public function deleteTypeSpecificData(\Magento\Catalog\Model\Product $product)
    {
        // No specific data to delete for this product type.
        // Implementation may be added later if needed.
    }
}
