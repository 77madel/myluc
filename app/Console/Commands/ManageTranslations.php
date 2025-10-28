<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\LMS\Models\Translation;
use Modules\LMS\Models\Language;

class ManageTranslations extends Command
{
    protected $signature = 'translations:manage {action} {--lang=} {--key=} {--value=}';
    protected $description = 'Manage translations for the application';

    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'list':
                $this->listTranslations();
                break;
            case 'add':
                $this->addTranslation();
                break;
            case 'copy':
                $this->copyTranslations();
                break;
            case 'status':
                $this->showStatus();
                break;
            default:
                $this->error('Invalid action. Use: list, add, copy, status');
        }
    }

    private function listTranslations()
    {
        $lang = $this->option('lang') ?? 'fr';
        $translations = Translation::where('lang', $lang)->get();

        $this->info("Translations for language: {$lang}");
        $this->table(['Key', 'Value'], $translations->map(function($t) {
            return [$t->lang_key, $t->lang_value];
        })->toArray());
    }

    private function addTranslation()
    {
        $lang = $this->option('lang') ?? 'fr';
        $key = $this->option('key');
        $value = $this->option('value');

        if (!$key || !$value) {
            $this->error('Please provide --key and --value options');
            return;
        }

        Translation::updateOrCreate(
            ['lang' => $lang, 'lang_key' => $key],
            ['lang_value' => $value]
        );

        $this->info("Translation added: {$key} => {$value} ({$lang})");
    }

    private function copyTranslations()
    {
        $fromLang = $this->option('lang') ?? 'en';
        $toLang = $this->ask('Target language', 'fr');

        $this->info("Copying translations from {$fromLang} to {$toLang}...");

        $sourceTranslations = Translation::where('lang', $fromLang)->get();
        $copied = 0;

        foreach ($sourceTranslations as $translation) {
            Translation::updateOrCreate(
                ['lang' => $toLang, 'lang_key' => $translation->lang_key],
                ['lang_value' => $translation->lang_value]
            );
            $copied++;
        }

        $this->info("Copied {$copied} translations from {$fromLang} to {$toLang}");
    }

    private function showStatus()
    {
        $languages = Language::all();
        $this->info('Language Status:');

        foreach ($languages as $lang) {
            $translationCount = Translation::where('lang', $lang->code)->count();
            $status = $lang->active ? 'Active' : 'Inactive';
            $this->line("{$lang->name} ({$lang->code}): {$translationCount} translations - {$status}");
        }
    }
}





