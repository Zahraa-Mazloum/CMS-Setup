<?php 
use app\lib\TwigFilter;

TwigFilter::assign(\app\twig\filters\Vue::class);
TwigFilter::assign(\app\twig\filters\Translation::class);
TwigFilter::assign(\app\twig\filters\Debug::class);
TwigFilter::assign(\app\twig\filters\BodyClass::class);
TwigFilter::assign(\app\twig\filters\LayoutClass::class);
TwigFilter::assign(\app\twig\filters\BodyStyle::class);
TwigFilter::assign(\app\twig\filters\Title::class);
TwigFilter::assign(\app\twig\filters\Alias::class);
TwigFilter::assign(\app\twig\filters\Url::class);
TwigFilter::assign(\app\twig\filters\Link::class);
TwigFilter::assign(\app\twig\filters\Json::class);
TwigFilter::assign(\app\twig\filters\FormErrors::class);
TwigFilter::assign(\app\twig\filters\Meta::class);
TwigFilter::assign(\app\twig\filters\OpenGraphMeta::class);
TwigFilter::assign(\app\twig\filters\Call::class);
TwigFilter::assign(\app\twig\filters\Run::class);
TwigFilter::assign(\app\twig\filters\Pad::class);
TwigFilter::assign(\app\twig\filters\Letters::class);
TwigFilter::assign(\app\twig\filters\Setting::class);
TwigFilter::assign(\app\twig\filters\Ref::class);
TwigFilter::assign(\app\twig\filters\Nl2Br::class);
TwigFilter::assign(\app\twig\filters\Language::class);
TwigFilter::assign(\app\twig\filters\Cookies::class);
TwigFilter::assign(\app\twig\filters\At::class);

return TwigFilter::filters();
