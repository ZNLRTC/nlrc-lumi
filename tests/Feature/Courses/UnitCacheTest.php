<?php

use App\Models\Courses\Unit;
use App\Models\Courses\Topic;
use App\Models\Courses\Course;
use Illuminate\Support\Facades\Cache;
use App\Models\Meetings\Assignments\Assignment;

describe('Unit caching', function() {

    beforeEach(function () {
        $this->unit = Unit::factory()->create();
        $this->course = $this->unit->course;
        $this->topic = Topic::factory()->create(['unit_id' => $this->unit->id]);
        $this->assignment = Assignment::factory()->create(['unit_id' => $this->unit->id]);
    });
    
    function cacheCourseUnits($course)
    {
        return Cache::remember("course.{$course->id}.units", 60 * 60 * 24, function () use ($course) {
            return Course::with(['units' => function ($query) {
                $query->select('id', 'course_id', 'name', 'slug', 'description');
            }])->withCount('units')->find($course->id);
        });
    }
    
    function cacheUnit($unit)
    {
        return Cache::remember("unit.{$unit->id}", 60 * 60 * 24, function () use ($unit) {
            return Unit::with(['assignments', 'topics'])->withCount(['assignments', 'topics'])->find($unit->id);
        });
    }

    it('caches the units of a course when retrieved', function () {
        $cachedCourse = cacheCourseUnits($this->course);

        expect($cachedCourse->units->first()->id)->toBe($this->unit->id);
        expect(Cache::has("course.{$this->course->id}.units"))->toBeTrue();
    });

    it('clears unit and course cache when a unit is updated', function () {
        cacheUnit($this->unit);
    
        $this->unit->update(['name' => 'Updated Unit Name']);
    
        expect(Cache::has("unit.{$this->unit->id}"))->toBeFalse();
        expect(Cache::has("course.{$this->course->id}.units"))->toBeFalse();
    });

    it('clears the cache when a unit is added to a course', function () {
        cacheCourseUnits($this->course);
    
        Unit::factory()->create(['course_id' => $this->course->id]);
    
        expect(Cache::has("course.{$this->course->id}.units"))->toBeFalse();
    });

    it('clears the cache when a unit is removed from a course', function () {
        cacheCourseUnits($this->course);
    
        $this->unit->delete();
    
        expect(Cache::has("course.{$this->course->id}.units"))->toBeFalse();
    });

    it('caches the unit with topics and assignments when retrieved', function () {
        $cachedUnit = cacheUnit($this->unit);
    
        expect($cachedUnit->topics->first()->id)->toBe($this->topic->id);
        expect($cachedUnit->assignments->first()->id)->toBe($this->assignment->id);
        expect(Cache::has("unit.{$this->unit->id}"))->toBeTrue();
    });

    it('clears the cache when a topic is updated', function () {
        cacheUnit($this->unit);
    
        $this->topic->update(['title' => 'Updated Topic Title']);
    
        expect(Cache::has("unit.{$this->unit->id}"))->toBeFalse();
    });
    
    it('clears the cache when an assignment is updated', function () {
        cacheUnit($this->unit);
    
        $this->assignment->update(['name' => 'Updated Assignment Name']);
    
        expect(Cache::has("unit.{$this->unit->id}"))->toBeFalse();
    });
    
    it('clears the cache when a topic is added to a unit', function () {
        cacheUnit($this->unit);
    
        Topic::factory()->create(['unit_id' => $this->unit->id]);
    
        expect(Cache::has("unit.{$this->unit->id}"))->toBeFalse();
    });
    
    it('clears the cache when an assignment is added to a unit', function () {
        cacheUnit($this->unit);
    
        Assignment::factory()->create(['unit_id' => $this->unit->id]);
    
        expect(Cache::has("unit.{$this->unit->id}"))->toBeFalse();
    });
    
    it('clears the cache when a topic is removed from a unit', function () {
        cacheUnit($this->unit);
    
        $this->topic->delete();
    
        expect(Cache::has("unit.{$this->unit->id}"))->toBeFalse();
    });
    
    it('clears the cache when an assignment is removed from a unit', function () {
        cacheUnit($this->unit);
    
        $this->assignment->delete();
    
        expect(Cache::has("unit.{$this->unit->id}"))->toBeFalse();
    });

})->group('unit-cache');