<?php
namespace App\Http\Controllers\Admin;

class CategoryController extends LookupController
{
    protected string $model = \App\Models\Category::class;
    protected string $route = 'admin.categories';
    protected string $title = 'Category';
}
