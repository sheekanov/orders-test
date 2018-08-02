<?php

namespace app\controllers;

use app\models\Order;
use app\models\Service;
use yii\data\Pagination;
use yii\web\Controller;


class SiteController extends Controller
{
    public function actionIndex($status = 'all', $serviceId = 'all', $mode = 'all', $search = null, $searchType = null)
    {
        $query = Order::find();

        if(!is_null($search) && $search != '') {
            switch ($searchType) {
                case 1:
                    $query->andwhere(['=', 'id', $search]);
                    break;
                case 2:
                    $query->andwhere(['LIKE', "REPLACE(link, 'https://example.com','')", $search]);
                    break;
                case 3:
                    $query->andwhere(['LIKE', 'user', $search]);
                    break;
            }
        }

        if($status != 'all') {
            $query->andwhere(['status' => $status]);
        }

        if($mode != 'all') {
            $query->andwhere(['mode' => $mode]);
        }

        $services = (new \yii\db\Query())
            ->select(['services.id as serviceId', "COUNT(orders.id) AS qty"])
            ->from('services')
            ->leftJoin(['orders' => $query], 'orders.service_id =services.id')
            ->groupBy('services.id')
            ->all();

        foreach ($services as $serviceKey => $serviceValue) {
            $services[$serviceKey]['service'] = Service::findOne($services[$serviceKey]['serviceId']);
        }

        $serviceAllMember = new Service();
        $serviceAllMember->name = 'All';
        array_unshift($services, array('serviceId' => 'all', 'service' => $serviceAllMember, 'qty' => $query->count()));

        if($serviceId != 'all') {
            $query->andwhere(['service_id' => $serviceId]);
        }

        $pagination = new Pagination(['pageSize' => 100, 'totalCount' => $query->count()]);

        $orders = $query->orderBy('id DESC')
            ->offset($pagination->offset)
            ->limit($pagination->limit)->all();

        return $this->render('orders', [
            'orders' => $orders,
            'services' => $services,
            'pagination' => $pagination,
            'allStatuses' => Order::$allStatuses,
            'allModes' => Order::$allModes,
            'allSearchTypes' => ['1' => 'Order ID', '2' => 'Link', '3' => 'Username'],
            'currentStatus' => $status,
            'currentMode' => $mode,
            'currentServiceId' => $serviceId,
            'currentSearch' => $search,
            'currentSearchType' => $searchType
        ]);
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
}
