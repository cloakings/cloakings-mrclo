<?php

namespace Cloakings\CloakingsMrClo;

enum MrCloLandingModeEnum: string
{
    case Content = 'content'; // show this html (doesn't exist in original code)

    case Local = 'local'; // open local file (Attention to the link to apply!)
    case Iframe = 'iframe'; // open link in a frame
    case Redirect = 'redirect'; // redirect to link
}
