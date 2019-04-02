<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\OrderFulfiller;

use InvalidArgumentException;
use Safe\Exceptions\StringsException;
use Setono\SyliusMiintoPlugin\Model\MappingInterface;
use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Setono\SyliusMiintoPlugin\Model\ShopInterface;
use Setono\SyliusMiintoPlugin\Provider\AddressProviderInterface;
use Setono\SyliusMiintoPlugin\Provider\CustomerProviderInterface;
use Setono\SyliusMiintoPlugin\Provider\OrderItemsProviderInterface;
use Setono\SyliusMiintoPlugin\Repository\MappingRepositoryInterface;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use SM\SMException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface as SyliusOrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class OrderFulfiller implements OrderFulfillerInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var FactoryInterface
     */
    private $orderFactory;

    /**
     * @var OrderProcessorInterface
     */
    private $orderProcessor;

    /**
     * @var CustomerProviderInterface
     */
    private $customerProvider;

    /**
     * @var OrderItemsProviderInterface
     */
    private $orderItemsProvider;

    /**
     * @var StateMachineFactoryInterface
     */
    private $stateMachineFactory;

    /**
     * @var AddressProviderInterface
     */
    private $billingAddressProvider;

    /**
     * @var AddressProviderInterface
     */
    private $shippingAddressProvider;

    /**
     * @var MappingRepositoryInterface
     */
    private $mappingRepository;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        FactoryInterface $orderFactory,
        OrderProcessorInterface $orderProcessor,
        CustomerProviderInterface $customerProvider,
        OrderItemsProviderInterface $orderItemsProvider,
        StateMachineFactoryInterface $stateMachineFactory,
        AddressProviderInterface $billingAddressProvider,
        AddressProviderInterface $shippingAddressProvider,
        MappingRepositoryInterface $mappingRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderFactory = $orderFactory;
        $this->orderProcessor = $orderProcessor;
        $this->customerProvider = $customerProvider;
        $this->orderItemsProvider = $orderItemsProvider;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->billingAddressProvider = $billingAddressProvider;
        $this->shippingAddressProvider = $shippingAddressProvider;
        $this->mappingRepository = $mappingRepository;
    }

    /**
     * @param OrderInterface $order
     *
     * @return OrderFulfillment
     *
     * @throws SMException
     * @throws StringsException
     */
    public function fulfill(OrderInterface $order): OrderFulfillment
    {
        $data = $order->getData();

        $shop = $this->getShop($order);
        $providerId = $this->getProviderId($order);
        $channel = $this->getChannel($shop);
        $localeCode = $this->getLocaleCode($shop);
        $mapping = $this->getMapping($shop, $providerId);

        /** @var SyliusOrderInterface $syliusOrder */
        $syliusOrder = $this->orderFactory->createNew();

        $stateMachine = $this->stateMachineFactory->get($syliusOrder, OrderCheckoutTransitions::GRAPH);

        $syliusOrder->setChannel($channel);
        $syliusOrder->setLocaleCode($localeCode);
        $syliusOrder->setCurrencyCode($data['currency']);

        $customer = $this->customerProvider->provide($order);
        $syliusOrder->setCustomer($customer);

        // add order items
        $orderItems = $this->orderItemsProvider->provide($order);
        if (count($orderItems->getFulfillablePositionIds()) === 0) {
            return new OrderFulfillment($orderItems->getFulfillablePositionIds(), $orderItems->getUnfulfillablePositionIds(), $order);
        }
        foreach ($orderItems->getOrderItems() as $orderItem) {
            $syliusOrder->addItem($orderItem);
        }

        $this->orderProcessor->process($syliusOrder);

        // add addresses
        $billingAddress = $this->billingAddressProvider->provide($order);
        $syliusOrder->setBillingAddress($billingAddress);

        $shippingAddress = $this->shippingAddressProvider->provide($order);
        $syliusOrder->setShippingAddress($shippingAddress);

        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_ADDRESS);

        // add shipment
        foreach ($syliusOrder->getShipments() as $shipment) {
            $shipment->setMethod($mapping->getShippingMethod());
        }
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING);

        // add payment
        foreach ($syliusOrder->getPayments() as $payment) {
            $payment->setMethod($mapping->getPaymentMethod());
        }
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT);

        // complete order
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_COMPLETE);

        // todo mark payment as complete

        $order->setOrder($syliusOrder);

        $this->orderRepository->add($syliusOrder);

        return new OrderFulfillment($orderItems->getFulfillablePositionIds(), $orderItems->getUnfulfillablePositionIds(), $order);
    }

    /**
     * @param OrderInterface $order
     *
     * @return ShopInterface
     *
     * @throws StringsException
     */
    private function getShop(OrderInterface $order): ShopInterface
    {
        $shop = $order->getShop();
        if (null === $shop) {
            throw new InvalidArgumentException(\Safe\sprintf('No shop set on order %s', $order->getId()));
        }

        return $shop;
    }

    /**
     * @param ShopInterface $shop
     *
     * @return ChannelInterface
     *
     * @throws StringsException
     */
    private function getChannel(ShopInterface $shop): ChannelInterface
    {
        $channel = $shop->getChannel();
        if (null === $channel) {
            throw new InvalidArgumentException(\Safe\sprintf('No channel set on shop %s', $shop->getId()));
        }

        return $channel;
    }

    /**
     * @param ShopInterface $shop
     *
     * @return string
     *
     * @throws StringsException
     */
    private function getLocaleCode(ShopInterface $shop): string
    {
        $locale = $shop->getLocale();
        if (null === $locale) {
            throw new InvalidArgumentException(\Safe\sprintf('No locale set on shop %s', $shop->getId()));
        }

        $code = $locale->getCode();
        if (null === $code) {
            throw new InvalidArgumentException(\Safe\sprintf('No code set on locale %s', $locale->getId()));
        }

        return $code;
    }

    /**
     * @param OrderInterface $order
     *
     * @return string
     *
     * @throws StringsException
     */
    private function getProviderId(OrderInterface $order): string
    {
        $providerId = $order->getProviderId();
        if (null === $providerId) {
            throw new InvalidArgumentException(\Safe\sprintf('No provider id set on order %s', $order->getId()));
        }

        return $providerId;
    }

    /**
     * Returns a valid mapping else it throws exceptions
     *
     * @param ShopInterface $shop
     * @param string $providerId
     *
     * @return MappingInterface
     *
     * @throws InvalidArgumentException
     * @throws StringsException
     */
    private function getMapping(ShopInterface $shop, string $providerId): MappingInterface
    {
        $mapping = $this->mappingRepository->findMappedByShopAndProviderId($shop, $providerId);

        if (null === $mapping) {
            throw new InvalidArgumentException(\Safe\sprintf('No mapping for shop with id %s and provider id %s', $shop->getId(), $providerId));
        }

        if ($mapping->getShippingMethod() === null) {
            throw new InvalidArgumentException(\Safe\sprintf('No shipping method set on the mapping with id %s', $mapping->getId()));
        }

        if ($mapping->getPaymentMethod() === null) {
            throw new InvalidArgumentException(\Safe\sprintf('No payment method set on the mapping with id %s', $mapping->getId()));
        }

        return $mapping;
    }
}
