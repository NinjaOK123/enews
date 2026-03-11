<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Services\JoomlaImporter;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('enews:import-joomla {--only= : users|categories|posts|featured|tags|comments} {--dry-run : Count only, no writes}', function (JoomlaImporter $importer) {
    $only = (string) ($this->option('only') ?? '');
    $dryRun = (bool) $this->option('dry-run');

    if ($dryRun) {
        $joomla = \Illuminate\Support\Facades\DB::connection('joomla');
        $counts = [
            'users'      => (int) $joomla->table('enews_users')->count(),
            'categories' => (int) $joomla->table('enews_categories')->where('extension', 'com_content')->where('title', '!=', 'ROOT')->count(),
            'posts'      => (int) $joomla->table('enews_content')->count(),
            'featured'   => (int) $joomla->table('enews_content_frontpage')->count(),
            'tags'       => (int) $joomla->table('enews_tags')->count(),
            'comments'   => (int) $joomla->table('enews_komento_comments')->where('component', 'com_content')->count(),
        ];
        $this->info('Dry-run counts from Joomla DB:');
        foreach ($counts as $k => $v) {
            $this->line("  - {$k}: {$v}");
        }
        return;
    }

    $this->info('Importing from Joomla connection "joomla" → current DB…');

    try {
        $result = match ($only) {
            'users'      => ['users'      => $importer->importUsers()],
            'categories' => ['categories' => $importer->importCategories()],
            'posts'      => ['posts'      => $importer->importPosts()],
            'featured'   => ['featured'   => $importer->importFeatured()],
            'tags'       => ['tags'       => $importer->importTags()],
            'comments'   => ['comments'   => $importer->importComments()],
            default      => $importer->importAll(),
        };

        foreach ($result as $k => $v) {
            $this->line("  ✔ imported {$k}: {$v}");
        }
        $this->info('Done!');
    } catch (\Throwable $e) {
        $this->error('[ERROR] ' . $e->getMessage());
        $this->newLine();
        $this->line($e->getTraceAsString());
        return 1;
    }
})->purpose('Import legacy Joomla enews.sql data into Laravel tables');

