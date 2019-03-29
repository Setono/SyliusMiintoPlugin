<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Provider;

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
     * @var VariantProviderInterface
     */
    private $variantProvider;

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
        VariantProviderInterface $variantProvider,
        FactoryInterface $orderItemFactory,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier
    ) {
        $this->availabilityChecker = $availabilityChecker;
        $this->variantProvider = $variantProvider;
        $this->orderItemFactory = $orderItemFactory;
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
    }

    public function provide(OrderInterface $order): OrderItems
    {
        $data = $order->getData();

        $orderItems = $fulfillablePositionIds = $unfulfillablePositionIds = [];

        foreach ($data['pendingPositions'] as $pendingPosition) {
            $variant = $this->variantProvider->provide($pendingPosition['item']);

            if ($this->availabilityChecker->isStockSufficient($variant, $pendingPosition['quantity'])) {
                $fulfillablePositionIds[] = $pendingPosition['id'];

                /** @var OrderItemInterface $orderItem */
                $orderItem = $this->orderItemFactory->createNew();
                $orderItem->setVariant($variant);
                $orderItem->setUnitPrice($pendingPosition['price']);

                $this->orderItemQuantityModifier->modify($orderItem, $pendingPosition['quantity']);

                $orderItems[] = $orderItem;
            } else {
                $unfulfillablePositionIds[] = $pendingPosition['id'];
            }
        }

        return new OrderItems($orderItems, $fulfillablePositionIds, $unfulfillablePositionIds);
    }
}
