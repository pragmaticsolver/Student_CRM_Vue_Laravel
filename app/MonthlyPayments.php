<?php

namespace App;

use App\Helpers\CommonHelper;
use Illuminate\Database\Eloquent\Model;

class MonthlyPayments extends Model
{
    protected $table = 'monthly_payments';
    
    // Possible payment status
    // draft
    // paid
    // invoice-sent
    // stripe-error

    protected $fillable = [
    	'customer_id',
    	'price',
    	'date',
    	'period',
    	'number_of_lessons',
      'payment_method',
      'payment_category',
      'memo',
      'status',
      'stripe_invoice_id',
      'stripe_invoice_url',
      'payment_recieved_at',
      'rest_month'
    ];

    public function student()
    {
        return $this->belongsTo('App\Students', 'customer_id', 'id');
    }

    public function getDisplayPaymentMethodAttribute()
    {
      return ucwords($this->payment_method);
    }

    public function getDisplayStatusAttribute()
    {
      return ucwords(str_replace("-"," ",$this->status));
    }

    public function canBeEdited()
    {
      return true;
    }

    public function canBemarkedAsPaidManually()
    {
      if($this->payment_method == 'stripe')
      {
        return false;
      }

      if($this->status == 'paid')
      {
        return false;
      }

      if($this->rest_month)
      {
        return false;
      }

      return true;
    }

    public function canStripeInvoiceBeSent()
    {
      if($this->payment_method != 'stripe')
      {
        return false;
      }

      if($this->status == 'paid' || $this->status == 'sending-invoice')
      {
        return false;
      }

      if($this->stripe_invoice_id)
      {
        return false;
      }

      return true;
    }

    public function localPaymentRecievedAt()
    {
      if($this->payment_recieved_at)
      {
        return \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $this->payment_recieved_at, 'UTC')->setTimezone(CommonHelper::getSchoolTimezone())->format('Y-m-d H:i:s');
      }
      else
      {
        return NULL;
      }
    }

    public function localCreatedAt()
    {
      return \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $this->created_at, 'UTC')->setTimezone(CommonHelper::getSchoolTimezone())->format('Y-m-d H:i:s');
    }

    public function localUpdatedAt()
    {
      return \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $this->updated_at, 'UTC')->setTimezone(CommonHelper::getSchoolTimezone())->format('Y-m-d H:i:s');
    }

    public function scopeOnlyMonthyPayments()
    {
      return $this->where('payment_category', NULl);
    }

    public function isOneOffPayment()
    {
      return $this->payment_category != NULL ? true : false;
    }

    public function formatForManagePaymetsPage($user)
    {
        $paymentDeletePermission = $user->can('payment-delete');
        $paymentMarkAsPaidPermission = $user->can('payment-mark-as-paid');
        $paymentEditPermission = $user->can('payment-edit');

       return [
          'id' => $this->id,
          'student' => [
              'id' => $this->student->id,
              'fullname' => $this->student->fullname
          ],
          'rest_month' => $this->rest_month,
          'period' => $this->period,
          'price' => $this->price,
          'number_of_lessons' => $this->number_of_lessons,
          'memo' => $this->memo,
          'payment_method' => $this->payment_method,
          'display_payment_method' => $this->display_payment_method,
          'status' => $this->status,
          'display_status' => $this->display_status,
          'payment_recieved_at' => $this->localPaymentRecievedAt(),
          'created_at' => $this->localCreatedAt(),
          'updated_at' => $this->localUpdatedAt(),
          'action_btns' => [
              'edit_payment' => $paymentEditPermission && $this->canBeEdited() ? 1 : 0,
              'delete_payment' => $paymentDeletePermission ? 1 : 0,
              'mark_as_paid' => $paymentMarkAsPaidPermission && $this->canBemarkedAsPaidManually(),
              'send_stripe_invoice' => $this->canStripeInvoiceBeSent() ? 1 : 0,
          ],
          'stripe_invoice_url' => $this->stripe_invoice_url
      ];
    }
}
