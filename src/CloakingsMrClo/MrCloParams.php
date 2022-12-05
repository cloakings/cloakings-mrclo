<?php /** @noinspection PhpUselessTrailingCommaInspection */

namespace Cloakings\CloakingsMrClo;

class MrCloParams implements \JsonSerializable
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

    public static function create(array $a): self
    {
        return new self(
            source: MrCloSourceEnum::tryFrom($a['source'] ?? '') ?? MrCloSourceEnum::Common,
            modeButton: (bool)($a['mode_button'] ?? false),
            refererCheck: (bool)($a['referer_check'] ?? false),
            utmCheck: (bool)($a['utm_check'] ?? false),
            vpnCheck: (bool)($a['vpn_check'] ?? false),
            blockIos: (bool)($a['block_ios'] ?? false),
            fakeCountries: $a['mode_button'] ?? [],
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'source' => $this->source->value,
            'mode_button' => $this->modeButton,
            'referer_check' => $this->refererCheck,
            'utm_check' => $this->utmCheck,
            'vpn_check' => $this->vpnCheck,
            'block_ios' => $this->blockIos,
            'fake_countries' => $this->fakeCountries,
        ];
    }
}
