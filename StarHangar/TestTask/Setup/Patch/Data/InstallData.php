<?php

declare(strict_types=1);

namespace StarHangar\TestTask\Setup\Patch\Data;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Psr\Log\LoggerInterface;

/**
 * Class InstallData
 *
 * Data patch for adding custom attributes to the product entity.
 */
class InstallData implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private EavSetupFactory $eavSetupFactory;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * InstallData constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        LoggerInterface $logger
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->logger = $logger;
    }

    /**
     * Apply data patch.
     *
     * @return void
     */
    public function apply()
    {
        try {
            $productTypes = implode(',', ['product_type_starhangar_ship']);

            /** @var EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

            // Add 'Insurance' attribute
            $eavSetup->addAttribute(Product::ENTITY, 'insurance', [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Insurance',
                'input' => 'select',
                'class' => '',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Table::class,
                'option' => [
                    'values' => [
                        'Lifetime',
                        '1 year or more',
                        'Less than 1 year',
                    ],
                ],
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'frontend_class' => 'red',
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => $productTypes,
            ]);

            // Add 'ship' attribute
            $eavSetup->addAttribute(Product::ENTITY, 'ship', [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Ship',
                'input' => 'select',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Table::class,
                'option' => [
                    'values' => [
                        'Eclipse',
                        'Idris',
                        'Prospector',
                    ],
                ],
                'class' => '',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => true,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => $productTypes,
            ]);

        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
    }

    /**
     * Get dependencies.
     *
     * @return array
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * Get aliases.
     *
     * @return array
     */
    public function getAliases(): array
    {
        return [];
    }
}
