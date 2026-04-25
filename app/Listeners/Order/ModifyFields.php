<?php

namespace App\Listeners\Order;

use App\Events\OrderCreate;
use App\Models\LegalEntitiesRecipient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class ModifyFields implements ShouldQueue
{
    use SerializesModels;

    /**
     * Имя соединения, на которое должно быть отправлено задание.
     *
     * @var string|null
     */
    public ?string $connection = 'redis';

    /**
     * Имя очереди, в которую должно быть отправлено задание.
     *
     * @var string|null
     */
    public ?string $queue = 'high';

    public $afterCommit = true;

    private $start_num_order = 77064;

    public function handle(OrderCreate $event): void
    {
        $event->order->num = $event->order->id + $this->start_num_order; //создание номера заказа
        if ($event->order->pay_method_id == 4 && ! $event->order->legal_entities_recipient_id) { //Если оплата по реквизитам, устанавливаем реквизиты по-умолчанию
            $event->order->legal_entities_recipient_id = LegalEntitiesRecipient::where('is_default', 1)->value('id');
        }
        $event->order->saveQuietly();
    }
}
