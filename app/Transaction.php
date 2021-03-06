<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\ModelStatus\HasStatuses;
use RyanWeber\Mutators\Timezoned;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Transaction extends Model
{
    use SoftDeletes, Searchable;
    use HasStatuses, Timezoned;

    protected $timezoned = ['date', 'created_at', 'updated_at', 'deleted_at'];
    protected $softDelete = true;
    protected $fillable = [
        'type',
        'customer_id',
        'supplier_id',
        'document_id',
        'currency_id',
        'rate',
        'payment_condition',
        'chart_account_id',
        'date',
        'number',
        'code',
        'code_expiry',
        'comment',
    ];

    public function toSearchableArray()
    {
        return [
            'type' => $this->type,
            'customer' => $this->customer->name,
            'customer_tax_id' => $this->customer->taxid,
            'customer_id' => $this->customer_id,
            'supplier' => $this->supplier->name,
            'supplier_tax_id' => $this->supplier->taxid,
            'supplier_id' => $this->supplier_id,
            'currency' => $this->currency->name,
            'items' => $this->items->flatMap->name,
            'payment_condition' => $this->payment_condition,
            'date' => $this->date,
            'number' => $this->number,
            'code' => $this->code,
            'comment' => $this->comment,
        ];
    }

    public function scopeMySales($query)
    {
        $taxPayerID = request()->route('taxPayer')->id ?? request()->route('taxPayer');

        return $query->where('transactions.type', 4)
        ->where('supplier_id', $taxPayerID);
    }

    public function scopeMySalesForJournals($query, $startDate, $endDate, $taxPayerID)
    {
        return $query
        ->whereBetween('date', [$startDate, $endDate])
        ->otherCurrentStatus(['Annuled'])
        ->where('transactions.type', 4)
        ->where('supplier_id', $taxPayerID);
    }

    public function scopeMyCreditNotes($query)
    {
        $taxPayerID = request()->route('taxPayer')->id ?? request()->route('taxPayer');

        return $query->where('transactions.type', 5)
        ->where('supplier_id', $taxPayerID);
    }

    public function scopeMyCreditNotesForJournals($query, $startDate, $endDate, $taxPayerID)
    {
        return $query
        ->whereBetween('date', [$startDate, $endDate])
        ->otherCurrentStatus(['Annuled'])
        ->where('transactions.type', 5)
        ->where('supplier_id', $taxPayerID);
    }

    public function scopeMyPurchases($query)
    {
        $taxPayerID = request()->route('taxPayer')->id ?? request()->route('taxPayer');

        return $query->whereIn('transactions.type', [1, 2])
        ->where('customer_id', $taxPayerID);
    }

    public function scopeMyPurchasesForJournals($query, $startDate, $endDate, $taxPayerID)
    {
        return $query
        ->whereBetween('date', [$startDate, $endDate])
        ->otherCurrentStatus(['Annuled'])
        ->whereIn('transactions.type', [1, 2])
        ->where('customer_id', $taxPayerID);
    }

    public function scopeMyDebitNotes($query)
    {
        $taxPayerID = request()->route('taxPayer')->id ?? request()->route('taxPayer');

        return $query->where('transactions.type', 3)
        ->where('customer_id', $taxPayerID);
    }

    public function scopeMyDebitNotesForJournals($query, $startDate, $endDate, $taxPayerID)
    {
        return $query
        ->whereBetween('date', [$startDate, $endDate])
        ->otherCurrentStatus(['Annuled'])
        ->where('transactions.type', 3)
        ->where('customer_id', $taxPayerID);
    }

    /**
    * Get the accountChart that owns the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function journal()
    {
        return $this->belongsTo('App\Journal');
    }

    /**
    * Get the accountChart that owns the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function accountChart()
    {
        return $this->belongsTo(Chart::class, 'chart_account_id', 'id');
    }

    /**
    * Get the taxPayer that owns the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function customer()
    {
        return $this->belongsTo(Taxpayer::class, 'customer_id');
    }

    /**
    * Get the taxPayer that owns the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function supplier()
    {
        return $this->belongsTo(Taxpayer::class, 'supplier_id');
    }

    /**
    * Get the document that owns the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    /**
    * Get the currency that owns the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function currency()
    {
        return $this->belongsTo(Currency::class,'currency_id','id');
    }


    /**
    * Get the accountMovements for the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function accountMovements()
    {
        return $this->hasMany(AccountMovement::class);
    }


    /**
    * Get the details for the model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function items()
    {
        return $this->belongsToMany('App\Chart', 'transaction_details');
    }

    /**
    * Get only the total value from details.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function total()
    {
        return $this->hasMany(TransactionDetail::class)
        ->selectRaw('sum(value) as total');
    }

    public function balance()
    {
        return $this->hasMany(AccountMovement::class)
        ->selectRaw('sum(credit - debit)');
    }
}
