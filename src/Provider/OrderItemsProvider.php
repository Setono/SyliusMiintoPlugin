<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Provider;

use Setono\SyliusMiintoPlugin\Mapper\ProductVariantMapperInterface;
use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Setono\SyliusMiintoPlugin\Provider\OrderItems\OrderItems;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class OrderItemsProvider implements OrderItemsProviderInterface
{
    /**
     * @var AvailabilityCheckerInterface
     */
    private $availabilityChecker;

    /**
     * @var ProductVariantMapperInterface
     */
    private $productVariantMapper;

    /**
     * @var FactoryInterface
     */
    private $orderItemFactory;

    /**
     * @var OrderItemQuantityModifierInterface
     */
    private $orderItemQuantityModifier;

    public function __construct(
        AvailabilityCheckerInterface $availabilityChecker,
        ProductVariantMapperInterface $productVariantMapper,
        FactoryInterface $orderItemFactory,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier
    ) {
        $this->availabilityChecker = $availabilityChecker;
        $this->productVariantMapper = $productVariantMapper;
        $this->orderItemFactory = $orderItemFactory;
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
    }

    public function provide(OrderInterface $order): OrderItems
    {
        $data = $order->getData();

        $orderItems = $fulfillablePositionIds = $unfulfillablePositionIds = [];

        foreach ($data['acceptedPositions'] as $position) {
            $variant = $this->productVariantMapper->map($position['item']);

            if ($this->availabilityChecker->isStockSufficient($variant, $position['quantity'])) {
                $fulfillablePositionIds[] = $position['id'];

                /** @var OrderItemInterface $orderItem */
                $orderItem = $this->orderItemFactory->createNew();
                $orderItem->setVariant($variant);
                $orderItem->setUnitPrice($position['price']);

                $this->orderItemQuantityModifier->modify($orderItem, $position['quantity']);

                $orderItems[] = $orderItem;
            } else {
                $unfulfillablePositionIds[] = $position['id'];
            }
        }

        return new OrderItems($orderItems, $fulfillablePositionIds, $unfulfillablePositionIds);
    }
}
