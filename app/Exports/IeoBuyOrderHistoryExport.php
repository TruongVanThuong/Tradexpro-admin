<?php
namespace App\Exports;

use App\Model\UserRegisteredIeo;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IeoBuyOrderHistoryExport implements FromCollection, WithHeadings
{
    public function __construct(
        public $request
    )
    {
    }

    /**
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        try {
            $data = UserRegisteredIeo::select([
                'users.first_name as first_name',
                'users.last_name as last_name',
                'users.email as email',
                'ieo.name as ieo_name',
                'user_registered_ieo.quantity',
                'user_registered_ieo.created_at',
                'ieo.value as ieo_value',
            ])
            ->join('users', 'user_registered_ieo.user_id', '=', 'users.id')
            ->join('ieo', 'user_registered_ieo.ieo_id', '=', 'ieo.id')
            ->where('user_registered_ieo.quantity', '>', 0);

            if (isset($this->request->from_date) && isset($this->request->to_date)) {
                $data = $data->whereBetween('user_registered_ieo.created_at', [
                    date('Y-m-d', strtotime($this->request->from_date)),
                    date('Y-m-d', strtotime($this->request->to_date))
                ]);
            }

            $data = $data->get();

            $data->map(function ($purchase) {
                $purchase->totalAmount = $purchase->ieo_value * $purchase->quantity;
                $purchase->totalAmount = number_format($purchase->totalAmount, 6);
                $purchase->totalAmount = rtrim($purchase->totalAmount, '0');
                $purchase->totalAmount = rtrim($purchase->totalAmount, '.');

                $purchase->created_at = $purchase->created_at ? Carbon::parse($purchase->created_at)->format('Y-m-d H:i:s') : 'N/A';
            });

            return $data;
        } catch (\Exception $e) {
            storeException('IEO Buy Order Export', $e->getMessage());
            return collect();
        }
    }

    /**
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            __("First Name"), __("Last Name"), __("Email"), __("IEO Name"), __("Quantity"), __("Created At"), __("Value"), __("Total Amount")
        ];
    }
}
