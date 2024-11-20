<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Container\Container;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerSerializer($this->app);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * @param Container $container
     */
    public function registerSerializer(Container $container): void
    {
        $container->bind('Serializer', function () {
            return new Serializer(
                [
                    new DateTimeNormalizer(),
                    new ObjectNormalizer(),
                    new ArrayDenormalizer(),
                ],
                [
                    new JsonEncoder(),
                    new CsvEncoder(),
                ]
            );
        });
    }
}
