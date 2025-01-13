<?php

namespace Orlyapps\Printable;

use Orlyapps\Printable\Support\StationeryResolver\StationeryResolverFactory;

trait Printable
{
    public function stationeryPdf()
    {
        return resource_path('pdf/stationery.pdf');
    }

    public function printContext()
    {
        return [];
    }

    public function printView()
    {
        return null;
    }

    public function browsershot($shot)
    {
        return $shot;
    }

    public function lambda()
    {
        return false;
    }

    public function print()
    {
        $resolver = StationeryResolverFactory::createForModel($this);
        $resolver->setModel($this);
        return new PrintModel($this, $resolver->resolve());
    }
}
