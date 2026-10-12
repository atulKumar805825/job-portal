<?php
namespace App\Http\Controllers\Admin;

class SkillController extends LookupController
{
    protected string $model = \App\Models\Skill::class;
    protected string $route = 'admin.skills';
    protected string $title = 'Skill';
}
