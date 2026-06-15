<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Order;
use App\Models\Stock;
use App\Models\Income;
use Illuminate\Http\Request;

class WbDataController extends Controller
{
    private $perPageLimit = 500;

    public function sales(Request $request)
    {
        $request->validate([
            'dateFrom' => 'required|date_format:Y-m-d',
            'dateTo'   => 'required|date_format:Y-m-d|after_or_equal:dateFrom',
        ]);

        $limit = min((int) $request->input('limit', $this->perPageLimit), $this->perPageLimit);

        $query = Sale::whereBetween('date', [
            $request->dateFrom,
            $request->dateTo,
        ]);

        return $query->orderBy('date')->paginate($limit);
    }

    public function orders(Request $request)
    {
        $request->validate([
            'dateFrom' => 'required|date_format:Y-m-d',
            'dateTo'   => 'required|date_format:Y-m-d|after_or_equal:dateFrom',
        ]);

        $limit = min((int) $request->input('limit', $this->perPageLimit), $this->perPageLimit);

        $query = Order::whereBetween('date', [
            $request->dateFrom,
            $request->dateTo,
        ]);

        return $query->orderBy('date')->paginate($limit);
    }

    public function stocks(Request $request)
    {
        $request->validate([
            'dateFrom' => 'required|date_format:Y-m-d',
        ]);

        $limit = min((int) $request->input('limit', $this->perPageLimit), $this->perPageLimit);

        $query = Stock::whereDate('date', $request->dateFrom);

        return $query->orderBy('date')->paginate($limit);
    }

    public function incomes(Request $request)
    {
        $request->validate([
            'dateFrom' => 'required|date_format:Y-m-d',
            'dateTo'   => 'required|date_format:Y-m-d|after_or_equal:dateFrom',
        ]);

        $limit = min((int) $request->input('limit', $this->perPageLimit), $this->perPageLimit);

        $query = Income::whereBetween('date', [
            $request->dateFrom,
            $request->dateTo,
        ]);

        return $query->orderBy('date')->paginate($limit);
    }
}
