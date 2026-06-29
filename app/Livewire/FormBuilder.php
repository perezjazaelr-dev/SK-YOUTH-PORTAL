<?php

namespace App\Livewire;

use Livewire\Component;

class FormBuilder extends Component
{
    public array $fields = [];

    public function mount(array $initialFields = [])
    {
        $this->fields = $initialFields;
    }

    public function addField()
    {
        $id = uniqid('field_');
        $this->fields[] = [
            'id' => $id,
            'type' => 'text', // 'text' (Single Line), 'dropdown' (Dropdown), 'file' (File Upload)
            'label' => '',
            'key' => '',
            'required' => false,
            'placeholder' => '',
            'options' => '', // For dropdown type options (comma separated)
            'maxSizeInMB' => 2,
            'allowedTypes' => ['pdf', 'png', 'jpg'],
        ];
    }

    public function updateField($id, $property, $value)
    {
        foreach ($this->fields as &$field) {
            if ($field['id'] === $id) {
                if ($property === 'required') {
                    $field[$property] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                } elseif ($property === 'allowedTypes') {
                    $field[$property] = is_array($value) ? $value : array_map('trim', explode(',', $value));
                } else {
                    $field[$property] = $value;
                }
                
                // Auto-generate key from label if key is empty
                if ($property === 'label' && empty($field['key'])) {
                    $field['key'] = strtolower(preg_replace('/[^a-z0-9_]/i', '_', $value));
                }
                break;
            }
        }
    }

    public function deleteField($id)
    {
        $this->fields = array_values(array_filter($this->fields, function ($field) use ($id) {
            return $field['id'] !== $id;
        }));
    }

    public function updateFieldOrder(array $orderedIds)
    {
        $orderedFields = [];
        foreach ($orderedIds as $id) {
            foreach ($this->fields as $field) {
                if ($field['id'] === $id) {
                    $orderedFields[] = $field;
                    break;
                }
            }
        }
        // Append any fields that were not in the ordered list
        foreach ($this->fields as $field) {
            if (!in_array($field['id'], $orderedIds)) {
                $orderedFields[] = $field;
            }
        }
        $this->fields = $orderedFields;
    }

    public function render()
    {
        return view('livewire.form-builder');
    }
}
