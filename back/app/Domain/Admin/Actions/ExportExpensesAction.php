<?php

namespace App\Domain\Admin\Actions;

use App\Domain\Admin\Repositories\ExpenseRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportExpensesAction
{
    public function __construct(
        private readonly ExpenseRepositoryInterface $expenseRepository,
    ) {
    }

    public function execute(?string $search = null): StreamedResponse
    {
        $adminUser = Auth::guard('admin')->user();

        if (!$adminUser) {
            abort(401, 'Unauthorized');
        }

        $filename = 'expenses.csv';

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $fieldsAllowed = [
            'last_name',
            'first_name',
            'order_id',
            'order_status',
            'order_amount',
            'order_shipping',
            'order_created_at',
            'order_updated_at',
        ];

        $callback = function() use ($fieldsAllowed, $adminUser, $search) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, $fieldsAllowed);

            $this->expenseRepository->getExpensesForExport(
                $adminUser->id,
                function($users) use ($handle) {
                    foreach ($users as $user) {
                        $row = [
                            $user->last_name ?? '',
                            $user->first_name ?? '',
                            $user->order_id ?? '',
                            $user->order_status ?? '',
                            $user->order_amount ?? '',
                            $user->order_shipping ?? '',
                            $user->order_created_at ? date('Y-m-d H:i:s', strtotime($user->order_created_at)) : '',
                            $user->order_updated_at ? date('Y-m-d H:i:s', strtotime($user->order_updated_at)) : '',
                        ];
                        fputcsv($handle, $row);
                    }
                },
                $search
            );

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }
}

