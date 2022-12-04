<?php /** @noinspection PhpUselessTrailingCommaInspection */

namespace Cloakings\CloakingsMrClo;

class MrCloParams
{
    public function __construct(
        public readonly MrCloSourceEnum $source = MrCloSourceEnum::Common,
        public readonly bool $modeButton = false, // true - button cloaking mode
        public readonly bool $refererCheck = false, // true - enabled referer check
        public readonly bool $utmCheck = false, // true - UTM check
        public readonly bool $vpnCheck = true, // true - Proxy, VPN or Tor connection check
        public readonly bool $blockIos = false, // block iOS
        public readonly array $fakeCountries = [], // list of countries that always see fake page
    ) {
    }
}
