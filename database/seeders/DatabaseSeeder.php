<?php

namespace Database\Seeders;

use App\Models\Form;
use Illuminate\Database\Seeder;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $source = base_path('../submission.json');

        if (! is_file($source) || ! is_readable($source)) {
            throw new RuntimeException(
                "Cannot read {$source}. Place submission.json in the parent folder of the Laravel project."
            );
        }

        $json = file_get_contents($source);
        $schema = json_decode($json, true);

        if (! is_array($schema) || ! array_is_list($schema) || json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('submission.json must contain a valid JSON array of sections.');
        }

        foreach ($schema as $section) {
            if (
                ! is_array($section)
                || ! is_string($section['name'] ?? null)
                || ! is_array($section['payloads'] ?? null)
            ) {
                throw new RuntimeException('Each form section must have a name and a payloads array.');
            }

            foreach ($section['payloads'] as $field) {
                if (
                    ! is_array($field)
                    || ! is_string($field['id'] ?? null)
                    || ! is_string($field['label'] ?? null)
                    || ! is_string($field['type'] ?? null)
                    || ! is_array($field['options'] ?? null)
                ) {
                    throw new RuntimeException('Each form field must have an id, label, type, and options array.');
                }
            }
        }

        Form::updateOrCreate(
            ['slug' => 'operational-risk'],
            [
                'name' => 'Detail Risiko Operasional',
                'schema' => $schema,
            ]
        );
    }
}
