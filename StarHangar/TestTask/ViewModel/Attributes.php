<?php
/**
 * StarHangar TestTask Attributes ViewModel.
 *
 * @category   StarHangar
 * @package    StarHangar_TestTask
 * @subpackage ViewModel
 */
namespace StarHangar\TestTask\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class Attributes implements ArgumentInterface
{
    /**
     * Get the selected option for a specific product attribute.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $attributeCode
     * @return string
     */
    public function getSelectedOption($product, $attributeCode)
    {
        $attributeValue = $product->getData($attributeCode);

        if ($attribute = $product->getResource()->getAttribute($attributeCode)) {
            $attributeOptions = $attribute->getSource()->getAllOptions();

            foreach ($attributeOptions as $option) {
                if ($option['value'] == $attributeValue) {
                    return $option['label'];
                }
            }
        }

        return '';
    }
}
