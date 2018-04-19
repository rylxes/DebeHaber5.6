<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Taxpayer;
use App\TransactionDetail;
use App\Cycle;
use App\Journal;
use App\Chart;
use Carbon\Carbon;
use DB;

class ReportController extends Controller
{
    public function index(Taxpayer $taxPayer, Cycle $cycle)
    {
        return view('reports/index');
    }

    public function chartOfAccounts(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate)
    {
        if (isset($taxPayer))
        {
            $data = $this->chartQuery($taxPayer, $cycle, $startDate, $endDate);

            return view('reports/accounting/chart_of_accounts')
            ->with('header', $taxPayer)
            ->with('data', $data)
            ->with('strDate', $startDate)
            ->with('endDate', $endDate);
        }
    }

    public function sub_ledger(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate)
    {
        if (isset($taxPayer))
        {
            $data = $this->journalQuery($taxPayer, $startDate, $endDate);

            return view('reports/accounting/ledger-sub')
            ->with('header', $taxPayer)
            ->with('data', $data)
            ->with('strDate', $startDate)
            ->with('endDate', $endDate);
        }
    }

    public function ledger(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate)
    {
        if (isset($taxPayer))
        {
            $data = $this->journalQuery($taxPayer, $startDate, $endDate);

            return view('reports/accounting/ledger')
            ->with('header', $taxPayer)
            ->with('data', $data)
            ->with('strDate', $startDate)
            ->with('endDate', $endDate);
        }
    }

    public function balanceSheet(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate)
    {
        if (isset($taxPayer))
        {
            $journals = $this->journalQuery($taxPayer, $startDate, $endDate);
            $charts = $this->chartQuery($taxPayer, $cycle, $startDate, $endDate);

            //Loop through Journal entries and add to chart balance
            foreach ($journals->groupBy('chart_id') as $journalGrouped)
            {
                $chart = $charts->where('id', $journalGrouped->first()->chart_id)->first();

                if ($chart != null)
                {
                    $chart->balance = $journalGrouped->sum('credit') - $journalGrouped->sum('debit');
                }
            }

            $data = $charts;

            return view('reports/accounting/balance-sheet')
            ->with('header', $taxPayer)
            ->with('data', $data)
            ->with('strDate', $startDate)
            ->with('endDate', $endDate);
        }
    }

    public function balanceComparative(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate)
    {
        if (isset($taxPayer))
        {
            $data = $this->chartBalanceQuery($taxPayer, $cycle, $startDate, $endDate);

            return view('reports/accounting/balance-comparative')
            ->with('header', $taxPayer)
            ->with('data', $data)
            ->with('strDate', $startDate)
            ->with('endDate', $endDate);
        }
    }

    public function purchases(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate)
    {
        if (isset($taxPayer))
        {
            $data = $this->vatPurchaseQuery($taxPayer, $startDate, $endDate);

            return view('reports/PRY/purchases')
            ->with('header', $taxPayer)
            ->with('data', $data)
            ->with('strDate', $startDate)
            ->with('endDate', $endDate);
        }
    }

    public function purchasesByVAT(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate)
    {
        if (isset($taxPayer))
        {
            $data = $this->vatPurchaseQuery($taxPayer, $startDate, $endDate);

            return view('reports/PRY/purchases_byVAT')
            ->with('header', $taxPayer)
            ->with('data', $data)
            ->with('strDate', $startDate)
            ->with('endDate', $endDate);
        }
    }

    public function purchasesByChart(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate)
    {
        if (isset($taxPayer))
        {
            $data = $this->vatPurchaseQuery($taxPayer, $startDate, $endDate);

            return view('reports/PRY/purchases_byChart')
            ->with('header', $taxPayer)
            ->with('data', $data)
            ->with('strDate', $startDate)
            ->with('endDate', $endDate);
        }
    }

    public function purchasesBySupplier(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate)
    {
        if (isset($taxPayer))
        {
            $data = $this->vatPurchaseQuery($taxPayer, $startDate, $endDate);

            return view('reports/PRY/purchases_bySupplier')
            ->with('header', $taxPayer)
            ->with('data', $data)
            ->with('strDate', $startDate)
            ->with('endDate', $endDate);
        }
    }

    public function sales(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate)
    {
        if (isset($taxPayer))
        {
            $data = $this->vatSaleQuery($taxPayer, $startDate, $endDate);

            return view('reports/PRY/sales')
            ->with('header', $taxPayer)
            ->with('data', $data)
            ->with('strDate', $startDate)
            ->with('endDate', $endDate);
        }
    }

    public function creditNote(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate)
    {
        if (isset($taxPayer))
        {
            $data = $this->vatCreditNoteQuery($taxPayer, $startDate, $endDate);

            return view('reports/commercial/credit-note')
            ->with('header', $taxPayer)
            ->with('data', $data)
            ->with('strDate', $startDate)
            ->with('endDate', $endDate);
        }
    }

    public function salesByVAT(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate)
    {
        if (isset($taxPayer))
        {
            $data = $this->vatSaleQuery($taxPayer, $startDate, $endDate);

            return view('reports/PRY/sales_byVAT')
            ->with('header', $taxPayer)
            ->with('data', $data)
            ->with('strDate', $startDate)
            ->with('endDate', $endDate);
        }
    }

    public function salesByChart(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate)
    {
        if (isset($taxPayer))
        {
            $data = $this->vatSaleQuery($taxPayer, $startDate, $endDate);

            return view('reports/PRY/sales_byVat')
            ->with('header', $taxPayer)
            ->with('data', $data)
            ->with('strDate', $startDate)
            ->with('endDate', $endDate);
        }
    }

    public function salesByCustomer(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate)
    {
        if (isset($taxPayer))
        {
            $data = $this->vatSaleQuery($taxPayer, $startDate, $endDate);

            return view('reports/PRY/sales_byCustomer')
            ->with('header', $taxPayer)
            ->with('data', $data)
            ->with('strDate', $startDate)
            ->with('endDate', $endDate);
        }
    }

    public function vatPurchaseQuery(Taxpayer $taxPayer, $startDate, $endDate)
    {
        DB::connection()->disableQueryLog();

        return TransactionDetail::join('charts', 'charts.id', 'transaction_details.chart_id')
        ->join('charts as vats', 'vats.id', 'transaction_details.chart_vat_id')
        ->join('transactions', 'transactions.id', 'transaction_details.transaction_id')
        ->join('taxpayers as supplier', 'transactions.supplier_id', 'supplier.id')
        ->join('taxpayers as customer', 'transactions.customer_id', 'customer.id')
        ->where('customer.id', $taxPayer->id)
        ->where('transactions.deleted_at', '=', null)
        ->whereIn('transactions.type', [1, 2, 3])
        ->whereBetween('transactions.date', array(Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()))
        ->select('supplier.name as supplier',
        'supplier.taxid as supplier_code',
        'transactions.type',
        'transactions.id as purchaseID',
        'transactions.date',
        'transactions.code',
        'transactions.number',
        'transactions.payment_condition',
        'transactions.comment',
        'transactions.rate',
        'charts.name as costCenter',
        'vats.name as vat',
        'vats.coefficient',
        DB::raw('transactions.rate * if(transactions.type = 3, -transaction_details.value, transaction_details.value) as localCurrencyValue,
        (transactions.rate * if(transactions.type = 3, -transaction_details.value, transaction_details.value)) / (vats.coefficient + 1) as vatValue'
        )
        )
        ->orderBy('transactions.date', 'asc')
        ->orderBy('transactions.number', 'asc')
        ->get();
    }

    public function vatSaleQuery(Taxpayer $taxPayer, $startDate, $endDate)
    {
        DB::connection()->disableQueryLog();

        return TransactionDetail::join('charts', 'charts.id', 'transaction_details.chart_id')
        ->join('charts as vats', 'vats.id', 'transaction_details.chart_vat_id')
        ->join('transactions', 'transactions.id', 'transaction_details.transaction_id')
        ->join('taxpayers as supplier', 'transactions.supplier_id', 'supplier.id')
        ->join('taxpayers as customer', 'transactions.customer_id', 'customer.id')
        ->where('supplier.id', $taxPayer->id)
        ->whereIn('transactions.type', [4, 5])
        ->where('transactions.deleted_at', '=', null)
        ->whereBetween('transactions.date', array(Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()))
        ->select('customer.name as customer',
        'customer.taxid as customer_code',
        'transactions.type',
        'transactions.id as salesID',
        'transactions.date',
        'transactions.code',
        'transactions.number',
        'transactions.payment_condition',
        'transactions.comment',
        'transactions.rate',
        'charts.name as costCenter',
        'vats.name as vat',
        'vats.coefficient',
        DB::raw('transactions.rate * if(transactions.type = 5, -transaction_details.value, transaction_details.value) as localCurrencyValue,
        (transactions.rate * if(transactions.type = 5, -transaction_details.value, transaction_details.value)) / (vats.coefficient + 1) as vatValue')
        )
        ->orderBy('transactions.date', 'asc')
        ->orderBy('transactions.number', 'asc')
        ->get();
    }

    public function vatCreditNoteQuery(Taxpayer $taxPayer, $startDate, $endDate)
    {
        DB::connection()->disableQueryLog();

        return TransactionDetail::join('charts', 'charts.id', 'transaction_details.chart_id')
        ->join('charts as vats', 'vats.id', 'transaction_details.chart_vat_id')
        ->join('transactions', 'transactions.id', 'transaction_details.transaction_id')
        ->join('taxpayers as supplier', 'transactions.supplier_id', 'supplier.id')
        ->join('taxpayers as customer', 'transactions.customer_id', 'customer.id')
        ->where('supplier.id', $taxPayer->id)
        ->where('transactions.type', 5)
        ->where('transactions.deleted_at', '=', null)
        ->whereBetween('transactions.date', array(Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()))
        ->select('customer.name as customer',
        'customer.taxid as customer_code',
        'transactions.type',
        'transactions.id as creditID',
        'transactions.date',
        'transactions.code',
        'transactions.number',
        'transactions.payment_condition',
        'transactions.comment',
        'transactions.rate',
        'charts.name as costCenter',
        'vats.name as vat',
        'vats.coefficient',
        DB::raw('transactions.rate * if(transactions.type = 5, -transaction_details.value, transaction_details.value) as localCurrencyValue,
        (transactions.rate * if(transactions.type = 5, -transaction_details.value, transaction_details.value)) / (vats.coefficient + 1) as vatValue')
        )
        ->orderBy('transactions.date', 'asc')
        ->orderBy('transactions.number', 'asc')
        ->get();
    }

    public function journalQuery(Taxpayer $taxPayer, $startDate, $endDate)
    {
        DB::connection()->disableQueryLog();

        return Journal::join('journal_details', 'journals.id', 'journal_details.journal_id')
        ->join('charts', 'charts.id', 'journal_details.chart_id')
        ->select('journals.id',
        'journals.date',
        'journals.comment',
        'journals.number',
        'journal_details.debit',
        'journal_details.credit',
        'charts.id as chart_id',
        'charts.name as chartName',
        'charts.code as chartCode',
        'charts.type as chartType',
        'charts.sub_type as chartSubType')
        ->whereBetween('journals.date', array(Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()))
        ->orderBy('date')
        ->get();
    }

    public function chartQuery(Taxpayer $taxPayer, $cycle, $startDate, $endDate)
    {
        DB::connection()->disableQueryLog();

        return Chart::orderBy('type')
        ->orderBy('code')
        ->select('id', 'parent_id', 'code', 'name', 'type', 'sub_type', 'is_accountable')
        ->get();
    }
}
