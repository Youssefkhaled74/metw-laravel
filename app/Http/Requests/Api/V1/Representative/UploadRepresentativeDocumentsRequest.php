<?php

namespace App\Http\Requests\Api\V1\Representative;

use App\Enum\RepresentativeDocumentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UploadRepresentativeDocumentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('document_type') && ! $this->filled('document_types')) {
            $this->merge([
                'document_types' => [$this->input('document_type')],
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'collection_name' => ['nullable', 'string', 'max:100'],
            'documents' => ['required', 'array', 'min:1'],
            'documents.*' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:10240'],
            'document_type' => ['nullable', 'string', Rule::in(RepresentativeDocumentType::values())],
            'document_types' => ['required', 'array', 'min:1'],
            'document_types.*' => ['required', 'string', Rule::in(RepresentativeDocumentType::values())],
            'titles' => ['nullable', 'array'],
            'titles.*' => ['nullable', 'string', 'max:255'],
            'is_primary' => ['nullable', 'boolean'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $documents = (array) $this->input('documents', []);
            $documentTypes = (array) $this->input('document_types', []);

            if (count($documents) !== count($documentTypes)) {
                $validator->errors()->add('document_types', 'Each uploaded file must have a document type.');
            }
        });
    }
}
