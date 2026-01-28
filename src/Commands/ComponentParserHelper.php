<?php

namespace Rappasoft\LaravelLivewireTables\Commands;

use Illuminate\Support\Str;

/**
 * Version-agnostic component parser helper class.
 * Replaces Livewire's ComponentParser which was removed in v4.
 */
class ComponentParserHelper
{
    protected string $classNamespace;

    protected string $viewPath;

    protected string $rawName;

    protected string $className;

    protected string $classPath;

    public function __construct(?string $classNamespace, ?string $viewPath, string $name)
    {
        $this->classNamespace = $classNamespace ?? $this->getDefaultNamespace();
        $this->viewPath = $viewPath ?? resource_path('views/livewire');
        $this->rawName = $name;

        $this->parseComponentName();
    }

    /**
     * Get the default Livewire namespace (v3+ uses App\Livewire)
     */
    protected function getDefaultNamespace(): string
    {
        return 'App\\Livewire';
    }

    /**
     * Parse the component name into class name, namespace, and path
     */
    protected function parseComponentName(): void
    {
        // Handle nested components like "Admin/UserTable" or "Admin.UserTable"
        $name = str_replace('.', '/', $this->rawName);

        // Split into directory parts and class name
        $parts = collect(explode('/', $name))
            ->map(fn ($part) => Str::studly($part))
            ->toArray();

        $this->className = array_pop($parts);

        // Build the full namespace
        if (count($parts) > 0) {
            $this->classNamespace = $this->classNamespace.'\\'.implode('\\', $parts);
        }

        // Build the class path
        $relativePath = str_replace('\\', '/', $this->classNamespace);
        $relativePath = Str::after($relativePath, 'App/');

        $this->classPath = app_path($relativePath.'/'.$this->className.'.php');
    }

    /**
     * Get the class name
     */
    public function className(): string
    {
        return $this->className;
    }

    /**
     * Get the full class namespace
     */
    public function classNamespace(): string
    {
        return $this->classNamespace;
    }

    /**
     * Get the full path to the class file
     */
    public function classPath(): string
    {
        return $this->classPath;
    }

    /**
     * Get the relative path to the class file (for display purposes)
     */
    public function relativeClassPath(): string
    {
        return Str::after($this->classPath, base_path().DIRECTORY_SEPARATOR);
    }

    /**
     * Check if a class name is a PHP reserved word
     */
    public static function isReservedClassName(string $name): bool
    {
        // PHP reserved words that cannot be used as class names
        $reservedWords = [
            '__halt_compiler',
            'abstract',
            'and',
            'array',
            'as',
            'break',
            'callable',
            'case',
            'catch',
            'class',
            'clone',
            'const',
            'continue',
            'declare',
            'default',
            'die',
            'do',
            'echo',
            'else',
            'elseif',
            'empty',
            'enddeclare',
            'endfor',
            'endforeach',
            'endif',
            'endswitch',
            'endwhile',
            'enum',
            'eval',
            'exit',
            'extends',
            'false',
            'final',
            'finally',
            'fn',
            'for',
            'foreach',
            'function',
            'global',
            'goto',
            'if',
            'implements',
            'include',
            'include_once',
            'instanceof',
            'insteadof',
            'interface',
            'isset',
            'list',
            'match',
            'namespace',
            'new',
            'null',
            'or',
            'print',
            'private',
            'protected',
            'public',
            'readonly',
            'require',
            'require_once',
            'return',
            'static',
            'switch',
            'throw',
            'trait',
            'true',
            'try',
            'unset',
            'use',
            'var',
            'while',
            'xor',
            'yield',
            'yield from',
            // Livewire-specific reserved names
            'component',
            'livewire',
        ];

        return in_array(strtolower($name), $reservedWords);
    }
}
