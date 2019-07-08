<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Model;

final class ShippingType
{
    public const COMPANY = 'CompanyDelivery';

    public const HOME = 'HomeDelivery';

    public const PARCEL_SHOP = 'ParcelShopDelivery';

    public static function getShippingTypes(): array
    {
        return [
            self::COMPANY => self::COMPANY,
            self::HOME => self::HOME,
            self::PARCEL_SHOP => self::PARCEL_SHOP,
        ];
    }
}
