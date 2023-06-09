<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::getCategoriesWithTags();
        $return = [];
        foreach ($categories as $catetgory) {
            $tags = [];
            $repositories = 0;
            foreach ($catetgory->tags as $tag) {
                $tags[] = [
                    'name' => $tag->name,
                    'repositories' => $tag->repositories_count,
                ];
                $repositories += $tag->repositories_count;
            }
            $return[] = [
                'category' => $catetgory->name,
                'repositories' => $repositories,
                'tags' => $tags,
            ];
        }

        return $return;
    }
}
