<?php

namespace App\Domain\Admin\Controllers;

use App\Domain\Admin\Requests\SearchSmsRequest;
use App\Domain\Admin\Resources\SentMessageResourceCollection;
use App\Domain\Sms\Repositories\SentMessageRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class SmsController extends Controller
{
    public function __construct(
        private SentMessageRepositoryInterface $sentMessageRepository
    ) {
    }

    public function search(SearchSmsRequest $request): JsonResponse
    {
        $filters = [
            'from' => $request->get('from'),
            'to' => $request->get('to'),
            'mid' => $request->get('mid'),
            'page' => $request->get('page', 1),
            'per_page' => $request->get('per_page', 20),
        ];

        $results = $this->sentMessageRepository->search($filters);

        return response()->json([
            'data' => new SentMessageResourceCollection($results->items(), $results),
        ]);
    }
}

