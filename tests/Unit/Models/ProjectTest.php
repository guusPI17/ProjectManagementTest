<?php

declare(strict_types=1);

namespace App\Tests\Unit\Models;

use App\Models\Project;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProjectTest extends TestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    }

    public function testValidProjectHasNoViolationsOnCreate(): void
    {
        $project = new Project();
        $project->name = 'Test Project';
        $project->url = 'https://example.com';
        $project->platformId = 1;
        $project->statusId = 1;

        $violations = $this->validator->validate($project, null, ['create']);

        $this->assertCount(0, $violations);
    }

    public function testEmptyNameCausesViolationOnCreate(): void
    {
        $project = new Project();
        $project->url = 'https://example.com';
        $project->platformId = 1;
        $project->statusId = 1;

        $violations = $this->validator->validate($project, null, ['create']);

        $this->assertGreaterThan(0, count($violations));
    }

    public function testInvalidUrlCausesViolationOnCreate(): void
    {
        $project = new Project();
        $project->name = 'Test';
        $project->url = 'not-a-url';
        $project->platformId = 1;
        $project->statusId = 1;

        $violations = $this->validator->validate($project, null, ['create']);

        $this->assertGreaterThan(0, count($violations));
    }

    public function testNegativePlatformIdCausesViolation(): void
    {
        $project = new Project();
        $project->name = 'Test';
        $project->url = 'https://example.com';
        $project->platformId = -1;
        $project->statusId = 1;

        $violations = $this->validator->validate($project, null, ['create']);

        $this->assertGreaterThan(0, count($violations));
    }

    public function testDefaultValues(): void
    {
        $project = new Project();

        $this->assertNull($project->id);
        $this->assertNull($project->name);
        $this->assertNull($project->url);
        $this->assertNull($project->platformId);
        $this->assertNull($project->statusId);
        $this->assertNull($project->description);
        $this->assertNull($project->platform);
        $this->assertNull($project->status);
        $this->assertNull($project->createdAt);
        $this->assertNull($project->updatedAt);
    }

    public function testUpdateGroupValidatesRequiredFields(): void
    {
        $project = new Project();
        $project->name = 'Updated';
        $project->url = 'https://example.com';
        $project->platformId = 1;
        $project->statusId = 1;

        $violations = $this->validator->validate($project, null, ['update']);

        $this->assertCount(0, $violations);
    }

    public function testUpdateGroupRejectsNullRequiredFields(): void
    {
        $project = new Project();

        $violations = $this->validator->validate($project, null, ['update']);

        $this->assertGreaterThan(0, count($violations));
    }

    public function testUpdateGroupValidatesUrlIfProvided(): void
    {
        $project = new Project();
        $project->url = 'invalid';

        $violations = $this->validator->validate($project, null, ['update']);

        $this->assertGreaterThan(0, count($violations));
    }
}
