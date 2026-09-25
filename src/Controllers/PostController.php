<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Post;

class PostController extends Controller
{
    public function index(): void
    {

        $postModels = new Post();
        $listPosts = $postModels->getListPosts();
        $this->view('posts/index', [
            'pageCss' => 'posts',
            'listPosts' => $listPosts,
        ]);

    }

    public function detail(string $slug): void
    {
        $post = (new Post())->findBySlug($slug);

        if ($post === false) {
            http_response_code(404);
            (new ErrorController())->notFound();
            return;
        }

        $this->view('posts/detailpost', [
            'pageCss' => 'posts',
            'post' => $post,
        ]);
    }
}
