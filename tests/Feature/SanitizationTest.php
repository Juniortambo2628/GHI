<?php

namespace Tests\Feature;

use App\Models\Cause;
use App\Models\Initiative;
use App\Models\Concerns\SanitizesHtml;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SanitizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_script_tags_are_stripped_from_description(): void
    {
        $cause = Cause::create([
            'title' => 'Test',
            'slug' => 'test',
            'description' => '<p>Safe content</p><script>alert("xss")</script>',
            'status' => 'draft',
        ]);

        $this->assertDatabaseHas('causes', [
            'id' => $cause->id,
            'description' => '<p>Safe content</p>',
        ]);
    }

    public function test_iframe_tags_are_stripped(): void
    {
        $cause = Cause::create([
            'title' => 'Test Iframe',
            'slug' => 'test-iframe',
            'description' => '<p>Normal text</p><iframe src="evil.com"></iframe>',
            'status' => 'draft',
        ]);

        $this->assertDatabaseHas('causes', [
            'id' => $cause->id,
            'description' => '<p>Normal text</p>',
        ]);
    }

    public function test_event_handlers_are_stripped(): void
    {
        $cause = Cause::create([
            'title' => 'Test Events',
            'slug' => 'test-events',
            'description' => '<p onclick="alert(1)">Click me</p>',
            'status' => 'draft',
        ]);

        $this->assertDatabaseHas('causes', [
            'id' => $cause->id,
            'description' => '<p>Click me</p>',
        ]);
    }

    public function test_javascript_protocols_are_stripped(): void
    {
        $cause = Cause::create([
            'title' => 'Test JS Protocol',
            'slug' => 'test-js-protocol',
            'description' => '<a href="javascript:alert(1)">link</a>',
            'status' => 'draft',
        ]);

        $cause->refresh();
        $this->assertStringNotContainsString('javascript:', $cause->description);
    }

    public function test_allowed_html_tags_are_preserved(): void
    {
        $cause = Cause::create([
            'title' => 'Test Allowed',
            'slug' => 'test-allowed',
            'description' => '<p><strong>Bold</strong> <em>italic</em> <a href="https://example.com">link</a></p>',
            'status' => 'draft',
        ]);

        $this->assertDatabaseHas('causes', [
            'id' => $cause->id,
            'description' => '<p><strong>Bold</strong> <em>italic</em> <a href="https://example.com">link</a></p>',
        ]);
    }

    public function test_initiative_content_is_sanitized(): void
    {
        $initiative = Initiative::create([
            'title' => 'Test Initiative',
            'slug' => 'test-initiative',
            'content' => '<p>Safe</p><object>evil</object>',
            'category' => 'education',
            'status' => 'draft',
        ]);

        $this->assertDatabaseHas('initiatives', [
            'id' => $initiative->id,
            'content' => '<p>Safe</p>',
        ]);
    }

    public function test_sanitize_html_method_removes_dangerous_content(): void
    {
        $clean = SanitizesHtml::sanitizeContent('<p>Hello</p><script>steal()</script><img onerror="hack()" src="x">');
        $this->assertStringContainsString('<p>Hello</p>', $clean);
        $this->assertStringNotContainsString('script', $clean);
        $this->assertStringNotContainsString('onerror', $clean);
    }

    public function test_relative_urls_in_links_are_neutralized(): void
    {
        $clean = SanitizesHtml::sanitizeContent('<a href="javascript:void(0)">click</a>');
        $this->assertStringNotContainsString('javascript', $clean);
    }
}
