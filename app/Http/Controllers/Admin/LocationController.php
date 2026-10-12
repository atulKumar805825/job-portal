<?php
namespace App\Http\Controllers\Admin;

class LocationController extends LookupController
{
    protected string $model = \App\Models\Location::class;
    protected string $route = 'admin.locations';
    protected string $title = 'Location';
}
