<?php

namespace App\Http\Controllers;

use App\Taxpayer;
use App\Cycle;
use App\Transaction;
use App\TransactionDetail;
use Illuminate\Http\Request;
use DB;

class SalesController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Taxpayer $taxPayer, Cycle $cycle)
    {
        return view('/commercial/sales');
    }

    public function get_sales($taxPayerID, Cycle $cycle)
    {
        //friends column is used to display the button in data like dit ,delete
        $Transaction = Transaction::MySales()->join('taxpayers', 'taxpayers.id', 'transactions.customer_id')
        ->where('supplier_id', $taxPayerID)
        ->with('details')
        ->select(DB::raw('0 as friends,
        transactions.id,
        taxpayers.name as Customer,
        customer_id,
        document_id,
        currency_id,
        rate,
        payment_condition,
        chart_account_id,
        date,
        number,
        transactions.code,
        code_expiry'))
        ->orderBy('date', 'desc')
        ->orderBy('number', 'desc')
        ->take(100)
        ->get();
        return response()->json($Transaction);

    }

    public function getLastPurchase($taxPayerID)
    {
        $Transaction = Transaction::MySales()->join('taxpayers', 'taxpayers.id', 'transactions.customer_id')
        ->where('supplier_id', $taxPayerID)
        ->with('details')
        ->orderBy('date', 'desc')
        ->select(DB::raw('false as friends, transactions.id,taxpayers.name as Supplier
        ,supplier_id,document_id,currency_id,rate,payment_condition,chart_account_id,date
        ,number,transactions.code,code_expiry'))
        ->first();
        return response()->json($Transaction);
    }


    public function get_salesByID($taxPayerID,Cycle $cycle,$id)
    {
        $Transaction = Transaction::MySales()->join('taxpayers', 'taxpayers.id', 'transactions.customer_id')
        ->where('supplier_id', $taxPayerID)
        ->where('transactions.id', $id)
        ->with('details')
        ->select(DB::raw('false as selected,transactions.id,taxpayers.name as customer
        , customer_id,
        document_id,
        currency_id,
        rate,
        payment_condition,
        chart_account_id,
        date,
        number,
        transactions.code,code_expiry'))
        ->get();
        return response()->json($Transaction);
    }

    public function get_lastDate($taxPayerID,Cycle $cycle)
    {
        $Transaction = Transaction::MySales()
        ->where('supplier_id', $taxPayerID)
        ->orderBy('created_at', 'desc')
        ->first();

        if (isset($Transaction))
        { return response()->json($Transaction->date); }
        else
        { return response()->json($cycle->start_date); }
    }


    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function create()
    {
        //
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request,Taxpayer $taxPayer)
    {

        if ($request->id == 0)
        {
            $Transaction = new Transaction();
        }
        else
        {
            $Transaction = Transaction::where('id', $request->id)->first();
        }
        if ($request->customer_id > 0)
        {
            $Transaction->customer_id = $request->customer_id;
        }

        $Transaction->supplier_id = $taxPayer->id;

        if ($request->document_id > 0)
        {
            $Transaction->document_id = $request->document_id;
        }

        $Transaction->currency_id = $request->currency_id;
        $Transaction->rate = $request->rate;
        $Transaction->payment_condition = $request->payment_condition;

        if ($request->chart_account_id > 0)
        {
            $Transaction->chart_account_id = $request->chart_account_id;
        }

        $Transaction->date = $request->date;
        $Transaction->number = $request->number;

        if ($Transaction->code!='')
        {
            $Transaction->code = $request->code;
        }

        if ($Transaction->code_expiry!='')
        {
            $Transaction->code_expiry = $request->code_expiry;
        }

        $Transaction->comment = $request->comment;
        $Transaction->type = $request->type;
        $Transaction->save();

        foreach ($request->details as $detail)
        {
            if ($detail['id'] == 0)
            {
                $TransactionDetail = new TransactionDetail();
            }
            else
            {
                $TransactionDetail = TransactionDetail::where('id',$detail['id'])->first();
            }

            $TransactionDetail->transaction_id = $Transaction->id;
            $TransactionDetail->chart_id = $detail['chart_id'];
            $TransactionDetail->chart_vat_id = $detail['chart_vat_id'];
            $TransactionDetail->value = $detail['value'];
            $TransactionDetail->save();
        }
        return response()->json('ok');
    }

    /**
    * Display the specified resource.
    *
    * @param  \App\Transaction  $transaction
    * @return \Illuminate\Http\Response
    */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\Transaction  $transaction
    * @return \Illuminate\Http\Response
    */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \App\Transaction  $transaction
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\Transaction  $transaction
    * @return \Illuminate\Http\Response
    */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
