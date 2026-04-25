<?php

namespace App\Traits;

use Illuminate\Support\Str;

/**
 * Трейт DetectSearchBot предоставляет метод для проверки, приходит ли запрос от поискового бота
 * на основе заголовка User-Agent.
 */
trait DetectSearchBot
{
    /**
     * Проверяет, приходит ли запрос от поискового бота на основе заголовка User-Agent.
     *
     * @return bool Возвращает true, если запрос приходит от поискового бота, в противном случае false.
     */
    protected function isSearchBot(): bool
    {
        $userAgent = request()->header("User-Agent");

        if (!empty($userAgent)) {
            $options = [
                "YandexBot",
                "YandexAccessibilityBot",
                "YandexMobileBot",
                "YandexDirectDyn",
                "YandexScreenshotBot",
                "YandexImages",
                "YandexVideo",
                "YandexVideoParser",
                "YandexMedia",
                "YandexBlogs",
                "YandexFavicons",
                "YandexWebmaster",
                "YandexPagechecker",
                "YandexImageResizer",
                "YandexAdNet",
                "YandexDirect",
                "YaDirectFetcher",
                "YandexCalendar",
                "YandexSitelinks",
                "YandexMetrika",
                "YandexNews",
                "YandexNewslinks",
                "YandexCatalog",
                "YandexAntivirus",
                "YandexMarket",
                "YandexVertis",
                "YandexForDomain",
                "YandexSpravBot",
                "YandexSearchShop",
                "YandexMedianaBot",
                "YandexOntoDB",
                "YandexOntoDBAPI",
                "Googlebot",
                "Googlebot-Image",
                "Googlebot-News",
                "Googlebot-Video",
                "Mediapartners-Google",
                "AdsBot-Google",
                "Chrome-Lighthouse",
                "Lighthouse",
                "Mail.RU_Bot",
                "bingbot",
                "Accoona",
                "ia_archiver",
                "Ask Jeeves",
                "OmniExplorer_Bot",
                "W3C_Validator",
                "WebAlta",
                "YahooFeedSeeker",
                "Yahoo!",
                "Ezooms",
                "Tourlentabot",
                "MJ12bot",
                "AhrefsBot",
                "SearchBot",
                "SiteStatus",
                "Nigma.ru",
                "Baiduspider",
                "Statsbot",
                "SISTRIX",
                "AcoonBot",
                "findlinks",
                "proximic",
                "OpenindexSpider",
                "statdom.ru",
                "Exabot",
                "Spider",
                "SeznamBot",
                "oBot",
                "C-T bot",
                "Updownerbot",
                "Snoopy",
                "heritrix",
                "Yeti",
                "DomainVader",
                "DCPbot",
                "PaperLiBot",
                "APIs-Google",
                "AdsBot-Google-Mobile",
                "AdsBot-Google-Mobile",
                "AdsBot-Google-Mobile-Apps",
                "FeedFetcher-Google",
                "Google-Read-Aloud",
                "DuplexWeb-Google",
                "Storebot-Google",
                "SemrushBot"
            ];
            return Str::contains($userAgent, $options) || (stripos(strtolower($userAgent), 'bot') !== false);
        }

        return false;
    }
}
