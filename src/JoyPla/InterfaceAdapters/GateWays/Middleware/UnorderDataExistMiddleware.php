<?php

namespace JoyPla\InterfaceAdapters\GateWays\Middleware;

use App\Http\Middleware\Middleware;
use App\Http\Middleware\MiddlewareInterface;
use framework\Facades\Gate;
use JoyPla\Enterprise\Models\OrderStatus;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;

class UnorderDataExistMiddleware extends Middleware implements
    MiddlewareInterface
{
    public function process(array $vars): void
    {
        $gate = Gate::getGateInstance('fixed_quantity_order_slips');

        $auth = $this->request->user();
        $unOrder = ModelRepository::getOrderInstance()
            ->where('hospitalId', $auth->hospitalId)
            ->where('orderStatus', OrderStatus::UnOrdered)
            ->value('id');

        if ($gate->isOnlyMyDivision()) {
            $unOrder->where('divisionId', $auth->divisionId);
        }

        $unOrder = $unOrder->get();

        if ($unOrder->count() != 0) {
            $body = <<<EOL
<script>
Swal.fire({
    title: '未発注書が存在するため定数発注は使用できません。',
    text: "未発注書一覧へ遷移します。",
    icon: 'warning',
    confirmButtonText: 'OK'
}).then((result) => {
    location.href = _ROOT + "&path=/order/unapproved/show";   
})
</script>
EOL;
            echo view('html/Common/Template', compact('body'), false)->render();
            exit();
        }
    }
}
