<?php
namespace Database\Seeders;

use App\Models\{Category, Company, JobListing, Location, Skill, User};
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@example.com'], [
            'name' => 'Admin', 'password' => 'password', 'role' => 'admin',
        ]);

        $cats = collect(['IT & Software', 'Marketing', 'Finance', 'Design', 'Sales', 'HR'])->map(fn ($n) => Category::firstOrCreate(['name' => $n]));
        $locs = collect(['Jaipur', 'Delhi', 'Mumbai', 'Bangalore', 'Remote'])->map(fn ($n) => Location::firstOrCreate(['name' => $n]));
        $skills = collect(['PHP', 'Laravel', 'JavaScript', 'React', 'MySQL', 'Figma', 'SEO', 'Excel', 'Communication'])->map(fn ($n) => Skill::firstOrCreate(['name' => $n]));
        $companies = collect(['TechNova', 'PinkCity Solutions', 'BrightPixel', 'FinEdge'])->map(fn ($n) => Company::firstOrCreate(['name' => $n], ['website' => 'https://example.com', 'description' => "$n is a growing company."]));

        $samples = [
            ['Laravel Developer', 0, 0, 'Full Time', ['PHP', 'Laravel', 'MySQL']],
            ['React Frontend Developer', 0, 3, 'Remote', ['JavaScript', 'React']],
            ['UI/UX Designer', 3, 1, 'Contract', ['Figma']],
            ['SEO Executive', 1, 0, 'Part Time', ['SEO', 'Communication']],
            ['Accounts Executive', 2, 2, 'Full Time', ['Excel']],
            ['Full Stack Intern', 0, 4, 'Internship', ['PHP', 'JavaScript']],
        ];
        foreach ($samples as $i => [$title, $c, $l, $type, $sk]) {
            $job = JobListing::create([
                'title' => $title, 'slug' => Str::slug($title) . '-' . Str::lower(Str::random(5)),
                'company_id' => $companies[$i % 4]->id, 'category_id' => $cats[$c]->id, 'location_id' => $locs[$l]->id,
                'job_type' => $type, 'salary_min' => 20000 + $i * 5000, 'salary_max' => 40000 + $i * 8000,
                'experience' => $i % 2 ? '1-3 years' : '0-2 years',
                'description' => "We are hiring a $title.\n\nResponsibilities:\n- Deliver quality work\n- Collaborate with the team\n\nRequirements:\n- Relevant skills and a passion to learn.",
                'deadline' => now()->addDays(30), 'is_active' => true,
            ]);
            $job->skills()->sync($skills->whereIn('name', $sk)->pluck('id'));
        }
    }
}
