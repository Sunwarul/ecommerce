<?php

namespace App\Generators;

use Blueprint\Contracts\Generator;
use Blueprint\Models\Model;
use Blueprint\Tree;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class InertiaReactGenerator implements Generator
{
    protected $filesystem;

    protected $stubPath;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        $this->stubPath = \base_path('stubs/custom');
    }

    public function types(): array
    {
        return ['inertia_react'];
    }

    public function output(Tree $tree): array
    {
        $output = [];

        $models = $tree->models();

        if (empty($models)) {
            return $output;
        }

        foreach ($models as $model) {
            $paths = $this->generateForModel($model);
            if (! empty($paths)) {
                $output = array_merge($output, $paths);
            }
        }

        return $output;
    }

    protected function generateForModel(Model $model): array
    {
        $output = [];

        // Generate React components
        $output = array_merge($output, $this->generateCrud($model));
        $output = array_merge($output, $this->generateForm($model));

        return $output;
    }

    protected function generateCrud(Model $model): array
    {
        $path = $this->getReactComponentPath($model, 'Index');

        if (! $this->filesystem->exists(dirname($path))) {
            $this->filesystem->makeDirectory(dirname($path), 0755, true);
        }

        $this->filesystem->put(
            $path,
            $this->compileStub('crud', [
                '{{ ModelName }}' => $model->name(),
                '{{ modelName }}' => Str::camel($model->name()),
                '{{ modelVariablePlural }}' => Str::plural(Str::camel($model->name())),
                '{{ modelRouteName }}' => Str::kebab(Str::plural($model->name())),
                '{{ modelFields }}' => $this->getFieldsForCrudIndex($model),
                '{{ columnDefinitions }}' => $this->getColumnDefinitions($model),
            ])
        );

        return [$path];
    }

    protected function generateForm(Model $model): array
    {
        $path = $this->getReactComponentPath($model, 'Form');

        if (! $this->filesystem->exists(dirname($path))) {
            $this->filesystem->makeDirectory(dirname($path), 0755, true);
        }

        $this->filesystem->put(
            $path,
            $this->compileStub('form', [
                '{{ ModelName }}' => $model->name(),
                '{{ modelName }}' => Str::camel($model->name()),
                '{{ formFields }}' => $this->getFormFields($model),
            ])
        );

        return [$path];
    }

    protected function getReactComponentPath(Model $model, string $type): string
    {
        $modelPlural = Str::plural($model->name());

        return resource_path('js/Pages/' . $modelPlural . '/' . $type . '.jsx');
    }

    protected function compileStub(string $stubName, array $replacements = []): string
    {
        $stub = $this->filesystem->get("{$this->stubPath}/{$stubName}.stub");

        foreach ($replacements as $search => $replace) {
            $stub = str_replace($search, $replace, $stub);
        }

        return $stub;
    }

    protected function getFieldsForCrudIndex(Model $model): string
    {
        $fields = [];

        foreach ($model->columns() as $column) {
            if (in_array($column->name(), ['id', 'password', 'remember_token', 'deleted_at'])) {
                continue;
            }
            $fields[] = $column->name() . ": '',";
        }

        return implode("\n        ", $fields);
    }

    protected function getColumnDefinitions(Model $model): string
    {
        $columns = [];

        foreach ($model->columns() as $column) {
            if (in_array($column->name(), ['id', 'password', 'remember_token', 'deleted_at'])) {
                continue;
            }

            $fieldName = $column->name();
            $headerName = Str::title(str_replace('_', ' ', $fieldName));

            $columns[] = "        // <Column field=\"{$fieldName}\" header=\"{$headerName}\" sortable />";
        }

        return implode("\n", $columns);
    }

    protected function getFormFields(Model $model): string
    {
        $fields = [];

        foreach ($model->columns() as $column) {
            // Skip fields that shouldn't be in forms
            if (in_array($column->name(), ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                continue;
            }

            $label = Str::title(str_replace('_', ' ', $column->name()));
            $name = $column->name();
            $modelVariable = Str::camel($model->name());

            // Use different PrimeReact components based on column type and name
            if ($name === 'password') {
                $fields[] = "            <div className=\"mb-4\">
                <label htmlFor=\"{$name}\" className=\"block font-bold mb-2\">{$label}</label>
                <Password
                    id=\"{$name}\"
                    value={form.data.{$name}}
                    onChange={(e) => form.setData('{$name}', e.target.value)}
                    className=\"w-full\"
                    toggleMask
                />
                {form.errors.{$name} && <small className=\"text-red-500\">{form.errors.{$name}}</small>}
            </div>";
            } elseif (str_contains($column->dataType(), 'text')) {
                $fields[] = "            <div className=\"mb-4\">
                <label htmlFor=\"{$name}\" className=\"block font-bold mb-2\">{$label}</label>
                <InputTextarea
                    id=\"{$name}\"
                    value={form.data.{$name}}
                    onChange={(e) => form.setData('{$name}', e.target.value)}
                    className=\"w-full\"
                    rows={3}
                />
                {form.errors.{$name} && <small className=\"text-red-500\">{form.errors.{$name}}</small>}
            </div>";
            } elseif (str_contains($column->dataType(), 'boolean')) {
                $fields[] = "            <div className=\"mb-4\">
                <div className=\"flex items-center\">
                    <Checkbox
                        inputId=\"{$name}\"
                        checked={form.data.{$name}}
                        onChange={(e) => form.setData('{$name}', e.checked)}
                    />
                    <label htmlFor=\"{$name}\" className=\"ml-2\">{$label}</label>
                </div>
                {form.errors.{$name} && <small className=\"text-red-500\">{form.errors.{$name}}</small>}
            </div>";
            } elseif (
                str_contains($column->dataType(), 'int')
                || str_contains($column->dataType(), 'decimal')
                || str_contains($column->dataType(), 'float')
            ) {
                $fields[] = "            <div className=\"mb-4\">
                <label htmlFor=\"{$name}\" className=\"block font-bold mb-2\">{$label}</label>
                <InputNumber
                    id=\"{$name}\"
                    value={form.data.{$name}}
                    onValueChange={(e) => form.setData('{$name}', e.value)}
                    className=\"w-full\"
                />
                {form.errors.{$name} && <small className=\"text-red-500\">{form.errors.{$name}}</small>}
            </div>";
            } elseif (
                str_contains($column->dataType(), 'date')
                || str_contains($column->dataType(), 'time')
                || str_contains($column->dataType(), 'datetime')
            ) {
                $fields[] = "            <div className=\"mb-4\">
                <label htmlFor=\"{$name}\" className=\"block font-bold mb-2\">{$label}</label>
                <Calendar
                    id=\"{$name}\"
                    value={form.data.{$name}}
                    onChange={(e) => form.setData('{$name}', e.value)}
                    className=\"w-full\"
                    showIcon
                />
                {form.errors.{$name} && <small className=\"text-red-500\">{form.errors.{$name}}</small>}
            </div>";
            } elseif ($name === 'email') {
                $fields[] = "            <div className=\"mb-4\">
                <label htmlFor=\"{$name}\" className=\"block font-bold mb-2\">{$label}</label>
                <InputText
                    id=\"{$name}\"
                    type=\"email\"
                    value={form.data.{$name}}
                    onChange={(e) => form.setData('{$name}', e.target.value)}
                    className=\"w-full\"
                />
                {form.errors.{$name} && <small className=\"text-red-500\">{form.errors.{$name}}</small>}
            </div>";
            } elseif (str_contains($column->dataType(), 'file')) {
                $fields[] = "            <div className=\"mb-4\">
                <label htmlFor=\"{$name}\" className=\"block font-bold mb-2\">{$label}</label>
                <FileUpload
                    id=\"{$name}\"
                    mode=\"basic\"
                    onSelect={(e) => form.setData('{$name}', e.files[0])}
                    className=\"w-full\"
                />
                {form.errors.{$name} && <small className=\"text-red-500\">{form.errors.{$name}}</small>}
            </div>";
            } else {
                $fields[] = "            <div className=\"mb-4\">
                <label htmlFor=\"{$name}\" className=\"block font-bold mb-2\">{$label}</label>
                <InputText
                    id=\"{$name}\"
                    value={form.data.{$name}}
                    onChange={(e) => form.setData('{$name}', e.target.value)}
                    className=\"w-full\"
                />
                {form.errors.{$name} && <small className=\"text-red-500\">{form.errors.{$name}}</small>}
            </div>";
            }
        }

        return implode("\n\n", $fields);
    }
}
