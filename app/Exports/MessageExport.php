<?php

namespace App\Exports;

use App\Models\Message;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
class MessageExport implements FromQuery, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;
    
    public $startdate;
    public $enddate;


    public function __construct(array $data)
    {
        $this->startdate = $data['startDate'];
        $this->enddate = $data['endDate'];

    }


    public function headings(): array
    {
        return [
            'contact_number', 'message', 'message_length', 'price','send_date'
        ];
    }

    public function query()
    {
        // return Invoice::query()->whereYear('created_at', $this->year);
        return Message::query()->where('created_at', '>=', $this->startdate)->where('created_at', '<=', $this->enddate);
    }
   
    public function map($message): array
    {
        return [
            $message->contact_number,
            $message->message,
            $message->message_length,
            $message->price,
            $message->send_date,
        ];
    }
}
