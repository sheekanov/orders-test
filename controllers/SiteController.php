<?php

namespace app\controllers;

use app\models\Order;
use app\models\Service;
use yii\data\Pagination;
use yii\web\Controller;


class SiteController extends Controller
{
    public function actionIndex($status = 'all', $service_id = 'all', $mode = 'all', $search = null, $searchType=null)
    {
        $query = Order::find();

        if($status != 'all') {
            $query->andwhere(['status' => $status]);
        }

        if($mode != 'all') {
            $query->andwhere(['mode' => $mode]);
        }

        if(!is_null($search) && $search != '') {
            switch ($searchType) {
                case 1:
                    $query->andwhere(['=', 'id', $search]);
                    break;
                case 2:
                    $query->andwhere(['LIKE', 'link', $search]);
                    break;
                case 3:
                    $query->andwhere(['LIKE', 'user', $search]);
                    break;
            }
        }

        $services = (new \yii\db\Query())
            ->select(['services.id as service_id', "COUNT(orders.id) AS qty"])
            ->from('services')
            ->leftJoin(['orders' => $query], 'orders.service_id =services.id')
            ->groupBy('services.id')
            ->all();

        foreach ($services as $serviceKey => $serviceValue) {
            $services[$serviceKey]['service'] = Service::findOne($services[$serviceKey]['service_id']);
        }

        $serviceAllMember = new Service();
        $serviceAllMember->name = 'All';
        array_unshift($services, array('service_id' => 'all', 'service' => $serviceAllMember, 'qty' => $query->count()));

        if($service_id != 'all') {
            $query->andwhere(['service_id' => $service_id]);
        }

        $pagination = new Pagination(['pageSize' => 100, 'totalCount' => $query->count()]);

        $orders = $query->orderBy('id DESC')
            ->offset($pagination->offset)
            ->limit($pagination->limit)->all();

        return $this->render('orders', [
            'orders' => $orders,
            'services' => $services,
            'pagination' => $pagination,
            'statuses' => ['all' => 'All orders', '0' => 'Pending', '1' => 'In Progress', '2' => 'Completed', '3' => 'Canceled', '4' => 'Error'],
            'modes' => ['all' => 'All', '0' => 'Manual', '1' => 'Auto'],
            'searchTypes' => ['1' => 'Order ID', '2' => 'Link', '3' => 'Username'],
            'status' => $status,
            'mode' => $mode,
            'service_id' => $service_id,
            'search' => $search,
            'searchType' => $searchType
        ]);
    }
}
