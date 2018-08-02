<?php

namespace app\controllers;

use app\models\Order;
use app\models\Service;
use yii\data\Pagination;
use yii\web\Controller;


class SiteController extends Controller
{
    /*
     * Основное и единственное действие - отображение странички с заказами.
     * Принимает следующие параметры
     * status - ключ выбранного в фильтре статуса заказа. По умолчанию - all (все статусы).
     * serviceId - ключ выбранной в фильтре услуги. По умолчанию all.
     * mode - ключ выбранного в фильтре режима (mode). По умолчанию all.
     * search - введенная в поле поиска строка. По умолчанию null.
     * searchType - ключ выбранного режиме поиска. По умолчанию null.
     */
    public function actionIndex($status = 'all', $serviceId = 'all', $mode = 'all', $search = null, $searchType = null)
    {
        //Заготовка запроса для выборки заказов. Далее к этой заготовке будем применять фильтры в зависимости от выбранных условий.
        $query = Order::find();

        //Если пришли данные из поля поиска, фильтруем затовку запроса соответствующим образом
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

        //Если выбран элемент в фильтре статусов, фильтруем затовку запроса соответствующим образом
        if($status != 'all') {
            $query->andwhere(['status' => $status]);
        }

        //Если выбран элемент в фильтре режимов, фильтруем затовку запроса соответствующим образом
        if($mode != 'all') {
            $query->andwhere(['mode' => $mode]);
        }

        //Здесь получаем массив с Id услуг и количеством заказов на данные услуги с учетом контекста (т.е. проведенной выше фильтрации заказов)
        $services = (new \yii\db\Query())
            ->select(['services.id as serviceId', "COUNT(orders.id) AS qty"])
            ->from('services')
            ->leftJoin(['orders' => $query], 'orders.service_id =services.id')
            ->groupBy('services.id')
            ->having('COUNT(orders.id) > 0')
            ->all();

        //Добавляем в полученный выше массив с Id услуг и количеством заказов инициализированные объекты модели услуг
        foreach ($services as $serviceKey => $serviceValue) {
            $services[$serviceKey]['service'] = Service::findOne($services[$serviceKey]['serviceId']);
        }

        //Добавляем в начало полученного выше массива элемент, который будет соответствовать "всем услугам"
        $serviceAllMember = new Service();
        $serviceAllMember->name = 'All';
        array_unshift($services, array('serviceId' => 'all', 'service' => $serviceAllMember, 'qty' => $query->count()));

        ///Если выбран элемент в фильтре услуг, фильтруем затовку запроса для заказов соответствующим образом
        if($serviceId != 'all') {
            $query->andwhere(['service_id' => $serviceId]);
        }

        //Получаем массив с заказами и пагинацию для него
        $pagination = new Pagination(['pageSize' => 100, 'totalCount' => $query->count()]);

        $orders = $query->orderBy('id DESC')
            ->offset($pagination->offset)
            ->limit($pagination->limit)->all();

        //Рендерим представление
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
