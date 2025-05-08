<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    public function __construct()
    {
        //
    }

    public function delete(User $user, Category $category)
    {
        return !$category->children()->exists();
    }
}
