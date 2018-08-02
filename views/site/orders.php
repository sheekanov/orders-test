<?php
    use yii\helpers\Url;
?>

<div class="container-fluid">
    <ul class="nav nav-tabs p-b">
        <?php foreach ($allStatuses as $statusKey => $statusValue): ?>
            <li class="<?php if((string)$statusKey === $currentStatus): ?> active <?php endif; ?>">
                <a href="<?= Url::to(['site/index', 'status' => $statusKey]); ?>"><?= $statusValue ?></a>
            </li>
        <?php endforeach; ?>
        <li class="pull-right custom-search">
            <form class="form-inline" action="<?= Url::to(['site/index', 'status' => $currentStatus]); ?>" method="get">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" value="<?= $currentSearch ?>" placeholder="Search orders">
                    <span class="input-group-btn search-select-wrap">
                    <select class="form-control search-select" name="searchType">
                    <?php foreach ($allSearchTypes as $searchTypeKey => $searchTypeValue): ?>
                      <option value="<?= $searchTypeKey ?>" <?php if($searchTypeKey == $currentSearchType): ?> selected <?php endif; ?>><?= $searchTypeValue ?></option>
                    <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                    </span>
                </div>
            </form>
        </li>
    </ul>
    <table class="table order-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Link</th>
            <th>Quantity</th>
            <th class="dropdown-th">
                <div class="dropdown">
                    <button class="btn btn-th btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        Service
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        <?php foreach ($services as $service): ?>
                            <li class="<?php if($currentServiceId == $service['serviceId']): ?> active <?php endif; ?>">
                                <a href="<?= Url::to(['site/index', 'status' => $currentStatus, 'mode' => $currentMode, 'serviceId' => $service['serviceId'], 'search' => $currentSearch, 'searchType' => $currentSearchType]); ?>">
                                    <span class="label-id"><?= $service['qty'] ?></span>  <?= $service['service']->name ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </th>
            <th>Status</th>
            <th class="dropdown-th">
                <div class="dropdown">
                    <button class="btn btn-th btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        Mode
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        <?php foreach ($allModes as $modeKey => $modeValue): ?>
                            <li class="<?php if((string)$modeKey === $currentMode): ?> active <?php endif; ?>">
                                <a href="<?= Url::to(['site/index', 'status' => $currentStatus, 'mode' => $modeKey, 'serviceId' =>$serviceId, 'search' => $currentSearch, 'searchType' => $currentSearchType]); ?>"><?= $modeValue ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </th>
            <th>Created</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($orders as $order): ?>
        <tr>
            <td><?= $order->id ?></td>
            <td><?= $order->user ?></td>
            <td class="link"><?= strtr($order->link, ['https://example.com' => ''])  ?></td>
            <td><?= $order->quantity ?></td>
            <td class="service">
                <span class="label-id"><?= $services[array_search($order->service->id, array_column($services, 'serviceId'))]['qty'] ?></span>
                <?= $order->service->name ?>
            </td>
            <td><?= $allStatuses[$order->status]; ?></td>
            <td><?= $allModes[$order->mode]; ?></td>
            <td><span class="nowrap"><?= date('Y-m-d', $order->created_at) ?></span><span class="nowrap"><?= date('H:i:s', $order->created_at) ?></span></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="row">
        <div class="col-sm-8">

            <nav>
                <ul class="pagination">
                    <li class="<?php if($pagination->page == 0) : ?> disabled <?php endif; ?>"><a href="<?= Url::to(['site/index', 'status' => $currentStatus, 'mode' => $currentMode, 'serviceId' =>$currentServiceId, 'search' => $currentSearch, 'searchType' => $currentSearchType, 'page' => $pagination->page]); ?>" aria-label="Previous">&laquo;</a></li>
                    <?php for($p=1; $p <= $pagination->pageCount; $p++) : ?>
                        <li class="<?php if($pagination->page+1 == $p): ?> active <?php endif; ?>"><a href="<?= Url::to(['site/index', 'status' => $currentStatus, 'mode' => $currentMode, 'serviceId' =>$currentServiceId, 'search' => $currentSearch, 'searchType' => $currentSearchType, 'page' => $p]); ?>"><?= $p ?></a></li>
                    <?php endfor; ?>
                    <li class="<?php if($pagination->page+1 == $pagination->pageCount) : ?> disabled <?php endif; ?>"><a href="<?= Url::to(['site/index', 'status' => $currentStatus, 'mode' => $currentMode, 'serviceId' =>$currentServiceId, 'search' => $currentSearch, 'searchType' => $currentSearchType, 'page' => $pagination->page+2]); ?>" aria-label="Next">&raquo;</a></li>
                </ul>
            </nav>

        </div>
        <div class="col-sm-4 pagination-counters">
            <?= $pagination->offset + 1 ?> to <?= $pagination->offset + count($orders) ?> of <?= $pagination->totalCount ?>
        </div>

    </div>
</div>