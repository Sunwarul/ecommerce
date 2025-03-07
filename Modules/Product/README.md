



```
use Illuminate\Database\Eloquent\Factories\Factory;

// Factory and model scanner code
$namespace = 'Modules\\' . $this->name . '\\Models\\';
$factoryNamespace = 'Modules\\' . $this->name . '\\Database\\Factories\\';
Factory::guessFactoryNamesUsing(function ($modelName) use ($namespace, $factoryNamespace) {
    if (strpos($modelName, $namespace) === 0) {
        $modelName = substr($modelName, strlen($namespace));
    }
    return $factoryNamespace . $modelName . 'Factory';
});
Factory::guessModelNamesUsing(function ($factory) use ($namespace) {
    $modelName = Str::replaceLast('Factory', '', class_basename($factory));
    return $namespace . $modelName;
});

```