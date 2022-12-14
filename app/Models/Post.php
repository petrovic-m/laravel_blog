<?php

namespace App\Models;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\File;

class Post
{
    public string $title, $excerpt, $date, $body, $slug;

    public function __construct(string $title,string $excerpt,string $date,string $body)
    {
        $this->title = $title;
        $this->excerpt = $excerpt;
        $this->date = $date;
        $this->body = $body;
        $this->slug = strtolower(str_replace(' ', '-', $title));

    }


    public static function find($slug)
    {
        return static::all()->firstWhere('slug', $slug);
    }

    public static function all()
    {
        return cache()->rememberForever('posts.all',function() {
            return collect(File::files(resource_path("posts")))
                ->map(function ($file) {
                    return \Spatie\YamlFrontMatter\YamlFrontMatter::parseFile($file);
                })
                ->map(function ($documents) {
                    return new Post(
                        $documents->title,
                        $documents->excerpt,
                        $documents->date,
                        $documents->body());
                })
                ->sortBy('date');
        });
    }
}
