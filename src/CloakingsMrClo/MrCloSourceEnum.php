<?php

namespace Cloakings\CloakingsMrClo;

enum MrCloSourceEnum: string
{
    case Common = 'common'; // Checking for all checks, including Referer and UTM
    case TikTok = 'tiktok'; // Using checks more suitable for tiktok
    case GoogleSearch = 'google_search'; // Using checks more suitable for Google Search
    case GoogleKmsOther = 'google_kms_other'; // Using checks more suitable for Google Kms
}
