<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\OrderFulfiller;

use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Setono\SyliusMiintoPlugin\Provider\AddressProviderInterface;
use Setono\SyliusMiintoPlugin\Provider\CustomerProviderInterface;
use Setono\SyliusMiintoPlugin\Provider\OrderItemsProviderInterface;
use Setono\SyliusMiintoPlugin\Provider\PaymentProviderInterface;
use Setono\SyliusMiintoPlugin\Provider\ShipmentProviderInterface;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use SM\SMException;
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
     * @var CustomerProviderInterface
     */
    private $customerProvider;

    /**
     * @var OrderItemsProviderInterface
     */
    private $orderItemsProvider;

    /**
     * @var OrderProcessorInterface
     */
    private $orderProcessor;

    /**
     * @var ShipmentProviderInterface
     */
    private $shipmentProvider;

    /**
     * @var PaymentProviderInterface
     */
    private $paymentProvider;

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

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        FactoryInterface $orderFactory,
        CustomerProviderInterface $customerProvider,
        OrderItemsProviderInterface $orderItemsProvider,
        ShipmentProviderInterface $shipmentProvider,
        PaymentProviderInterface $paymentProvider,
        StateMachineFactoryInterface $stateMachineFactory,
        AddressProviderInterface $billingAddressProvider,
        AddressProviderInterface $shippingAddressProvider
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderFactory = $orderFactory;
        $this->customerProvider = $customerProvider;
        $this->orderItemsProvider = $orderItemsProvider;
        $this->shipmentProvider = $shipmentProvider;
        $this->paymentProvider = $paymentProvider;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->billingAddressProvider = $billingAddressProvider;
        $this->shippingAddressProvider = $shippingAddressProvider;
    }

    /**
     * todo better exceptions
     * todo catch exceptions where this is called
     * todo better exception messages
     *
     * @param OrderInterface $order
     *
     * @throws SMException
     */
    public function fulfill(OrderInterface $order): void
    {
        $data = $order->getData();

        $shop = $order->getShop();
        if (null === $shop) {
            throw new \RuntimeException('No shop defined');
        }

        $channel = $shop->getChannel();
        if (null === $channel) {
            throw new \RuntimeException('No channel defined');
        }

        $localeCode = $shop->getLocaleCode();
        if (null === $localeCode) {
            throw new \RuntimeException('No locale code defined');
        }

        /** @var SyliusOrderInterface $syliusOrder */
        $syliusOrder = $this->orderFactory->createNew();

        $stateMachine = $this->stateMachineFactory->get($syliusOrder, OrderCheckoutTransitions::GRAPH);

        $syliusOrder->setChannel($channel);
        $syliusOrder->setLocaleCode($localeCode); // todo needs to be fixed on the shop mapping
        $syliusOrder->setCurrencyCode($data['currency']);

        $customer = $this->customerProvider->provide($order);
        $syliusOrder->setCustomer($customer);

        // add order items
        $orderItems = $this->orderItemsProvider->provide($order);
        foreach ($orderItems->getOrderItems() as $orderItem) {
            $syliusOrder->addItem($orderItem);
        }

        // add addresses
        $billingAddress = $this->billingAddressProvider->provide($order);
        $syliusOrder->setBillingAddress($billingAddress);

        $shippingAddress = $this->shippingAddressProvider->provide($order);
        $syliusOrder->setShippingAddress($shippingAddress);

        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_ADDRESS);

        // add shipment
        $shipment = $this->shipmentProvider->provide($order);
        $syliusOrder->addShipment($shipment);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING);

        // add payment
        $payment = $this->paymentProvider->provide($order);
        $syliusOrder->addPayment($payment);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT);

        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_COMPLETE);

        $this->orderRepository->add($syliusOrder);
    }
}
