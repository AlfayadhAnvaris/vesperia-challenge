<?php

namespace App\Services;

use App\Models\Form;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SubmissionAnswerValidator
{
    public function validate(Form $form, array $answers): array
    {
        $fields = collect($form->schema)
            ->flatMap(fn (array $section) => $section['payloads'] ?? [])
            ->keyBy('id');
        $rules = [];
        $unknownFieldErrors = [];

        foreach ($answers as $fieldId => $answer) {
            if (! $fields->has($fieldId)) {
                $unknownFieldErrors["answers.{$fieldId}"] = ['Field ini tidak ada dalam skema form.'];

                continue;
            }

            $field = $fields->get($fieldId);
            $optionIds = collect($field['options'] ?? [])->pluck('id')->all();

            if ($field['type'] === 'radio_button') {
                $rules["answers.{$fieldId}"] = ['nullable', 'string', Rule::in($optionIds)];
            } elseif ($field['type'] === 'checkbox') {
                $rules["answers.{$fieldId}"] = ['nullable', 'array'];
                $rules["answers.{$fieldId}.*"] = ['string', Rule::in($optionIds)];
            } elseif ($field['type'] === 'text' && ($field['sub_type'] ?? null) === 'date') {
                $rules["answers.{$fieldId}"] = ['nullable', 'date_format:Y-m-d'];
            } elseif ($field['type'] === 'text' && ($field['sub_type'] ?? null) === 'amount') {
                $rules["answers.{$fieldId}"] = ['nullable', 'string', 'max:32', 'regex:/^(?:-|\\d+(?:\\.\\d{1,2})?)$/'];
            } else {
                $rules["answers.{$fieldId}"] = ['nullable', 'string', 'max:10000'];
            }
        }

        $validator = Validator::make(['answers' => $answers], $rules);
        $validator->after(function ($validator) use ($unknownFieldErrors) {
            foreach ($unknownFieldErrors as $key => $messages) {
                foreach ($messages as $message) {
                    $validator->errors()->add($key, $message);
                }
            }
        });

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $answers;
    }
}
