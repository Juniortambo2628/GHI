<?php

namespace App\Models\Concerns;

trait SanitizesHtml
{
    protected static function bootSanitizesHtml(): void
    {
        static::saving(function ($model) {
            foreach ($model->getFillable() as $field) {
                if (in_array($field, ['content', 'description', 'outcome_summary', 'quote'])) {
                    $value = $model->{$field};
                    if ($value && is_string($value)) {
                        $model->{$field} = self::sanitizeContent($value);
                    }
                }
            }
        });
    }

    public static function sanitizeContent(string $value): string
    {
        $value = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $value);
        $value = preg_replace('/<iframe\b[^>]*>.*?<\/iframe>/is', '', $value);
        $value = preg_replace('/<object\b[^>]*>.*?<\/object>/is', '', $value);
        $value = preg_replace('/<embed\b[^>]*>/is', '', $value);
        $value = preg_replace('/<form\b[^>]*>.*?<\/form>/is', '', $value);

        $value = strip_tags($value, '<p><br><strong><em><u><a><ul><ol><li><blockquote><h1><h2><h3><h4><h5><h6><table><thead><tbody><tr><th><td><img><span><div>');

        $value = preg_replace('/ on\w+="[^"]*"/i', '', $value);
        $value = preg_replace("/ on\w+='[^']*'/i", '', $value);

        $value = preg_replace_callback('/<a\b[^>]*>/i', function ($matches) {
            $tag = $matches[0];
            if (preg_match('/href\s*=\s*["\']?\s*(javascript|vbscript|data)\s*:/i', $tag)) {
                return '';
            }

            return $tag;
        }, $value);

        $value = trim($value);
        $value = preg_replace('/\n{3,}/', "\n\n", $value);

        return $value;
    }
}
