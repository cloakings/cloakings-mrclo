<?php

namespace Cloakings\CloakingsMrClo;

class MrCloRenderParams
{
    public function __construct(
        public readonly MrCloLandingModeEnum $fakeMode = MrCloLandingModeEnum::Local,
        public readonly MrCloLandingModeEnum $realMode = MrCloLandingModeEnum::Local,
        public readonly string $fakeTarget = '',
        public readonly string $realTarget = '',
    ) {
    }
}
