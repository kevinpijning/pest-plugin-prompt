<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Helpers;

use Stringable;

readonly class Path implements Stringable
{
    private function __construct(
        private string $name,
        private ?string $folder = null,
        private string $extension = 'html',
        private bool $includeDatetime = false,
        private bool $includeUniqueId = false,
    ) {}

    /**
     * Start a fluent builder with a name.
     */
    public static function withFileName(string $name): self
    {
        return new self(name: $name);
    }

    /**
     * Set the output folder (fluent builder method).
     */
    public function inFolder(?string $folder): self
    {
        return new self(
            name: $this->name,
            folder: $folder,
            extension: $this->extension,
            includeDatetime: $this->includeDatetime,
            includeUniqueId: $this->includeUniqueId,
        );
    }

    /**
     * Set the file extension (fluent builder method).
     */
    public function withExtension(string $extension): self
    {
        return new self(
            name: $this->name,
            folder: $this->folder,
            extension: $extension,
            includeDatetime: $this->includeDatetime,
            includeUniqueId: $this->includeUniqueId,
        );
    }

    /**
     * Include datetime in the filename (fluent builder method).
     */
    public function includeDatetime(): self
    {
        return new self(
            name: $this->name,
            folder: $this->folder,
            extension: $this->extension,
            includeDatetime: true,
            includeUniqueId: $this->includeUniqueId,
        );
    }

    /**
     * Include unique ID in the filename (fluent builder method).
     */
    public function includeUniqueId(): self
    {
        return new self(
            name: $this->name,
            folder: $this->folder,
            extension: $this->extension,
            includeDatetime: $this->includeDatetime,
            includeUniqueId: true,
        );
    }

    /**
     * Generate the final output path (fluent builder method).
     */
    public function toString(): string
    {
        $filename = $this->generateFilename();
        $folderPath = $this->folder !== null
            ? rtrim($this->folder, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR
            : '';

        return $folderPath.$filename;
    }

    private function generateFilename(): string
    {
        // Remove Pest's internal prefix
        $name = str_replace('__pest_evaluable_', '', $this->name);

        $sanitizedName = strtolower($name);
        $sanitizedName = (string) preg_replace('/[^a-zA-Z0-9-_]/', '_', $sanitizedName);
        $sanitizedName = (string) preg_replace('/_+/', '_', $sanitizedName); // Replace multiple underscores with single underscore
        $sanitizedName = trim($sanitizedName, '_'); // Remove leading/trailing underscores

        $parts = [];

        if ($this->includeDatetime) {
            $parts[] = date('Y-m-d_His');
        }

        $parts[] = $sanitizedName;

        if ($this->includeUniqueId) {
            // Use more_entropy=true to prevent race conditions in high-concurrency scenarios
            // This adds additional entropy based on microseconds, making collisions extremely unlikely
            $parts[] = uniqid('', true);
        }

        $filename = implode('_', array_filter($parts));
        $extension = ltrim($this->extension, '.');

        return $filename.'.'.$extension;
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
