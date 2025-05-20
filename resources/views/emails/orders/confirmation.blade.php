<x-mail::message>
    <?= $introduction?>
    <x-mail::button :url="$orderUrl">
        مشاهده جزئیات سفارش
    </x-mail::button>
    <?= 'با تشکر.' . config('app.name')?>
</x-mail::message>
