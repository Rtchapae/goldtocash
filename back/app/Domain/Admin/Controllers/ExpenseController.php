<?php

namespace App\Domain\Admin\Controllers;

use App\Domain\Admin\Actions\ListExpensesAction;
use App\Domain\Admin\Actions\ExportExpensesAction;
use App\Domain\Admin\Requests\ListExpensesRequest;
use App\Domain\Admin\Resources\ExpenseResource;
use App\Domain\Admin\Resources\ExpenseResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExpenseController extends Controller
{
    public function __construct(
        private readonly ListExpensesAction $listExpensesAction,
        private readonly ExportExpensesAction $exportExpensesAction,
    ) {
    }

    public function index(ListExpensesRequest $request): JsonResponse
    {
        $paginator = $this->listExpensesAction->execute(
            perPage: $request->perPage(),
            page: $request->page(),
            orderBy: $request->orderBy(),
            orderDir: $request->orderDir(),
            search: $request->search(),
        );

        return response()->json(new ExpenseResourceCollection(
            ExpenseResource::collection($paginator->items()),
            $paginator
        ));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $raw = $request->query('search');
        $search = is_string($raw) && $raw !== '' ? mb_substr($raw, 0, 255) : null;

        return $this->exportExpensesAction->execute($search);
    }
}

