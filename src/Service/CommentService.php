<?php

namespace App\Service;

use App\Repository\CommentRepository;

class CommentService {

    public function __construct(
        private readonly CommentRepository $commentRepository
    ) {}
}