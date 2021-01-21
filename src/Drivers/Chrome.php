<?php

declare(strict_types=1);

namespace NunoMaduro\LaravelConsoleDusk\Drivers;

use Closure;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Laravel\Dusk\Chrome\SupportsChrome;
use NunoMaduro\LaravelConsoleDusk\Contracts\Drivers\DriverContract;

class Chrome implements DriverContract
{
    use SupportsChrome;

    public function __destruct()
    {
        $this->close();
    }

    public function open(): void
    {
        static::startChromeDriver();
    }

    public function close(): void
    {
        static::stopChromeDriver();
    }

    public static function afterClass(Closure $callback): void
    {
        // ..
    }

    public function getDriver()
    {
        $options = (new ChromeOptions())->addArguments(
            array_filter([
                '--disable-gpu',
                $this->runHeadless(),
            ])
        );

        return RemoteWebDriver::create(
            'http://localhost:9515',
            DesiredCapabilities::chrome()
                ->setCapability(
                    ChromeOptions::CAPABILITY,
                    $options
                )
        );
    }

    /**
     * Running around headless, or not..
     */
    protected function runHeadless(): ?string
    {
        return ! config('laravel-console-dusk.headless', true) && ! app()->isProduction() ? null : '--headless';
    }
}
